<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\PaymentIntent;

/**
 * @OA\Tag(
 *     name="Órdenes y Pagos",
 *     description="Procesamiento de compras mediante pasarela de pago Stripe e historial"
 * )
 */
class OrderController extends Controller
{
    public function __construct()
    {
        // Obliga a que todas las operaciones de órdenes requieran token JWT
        $this->middleware('auth:api');
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Crear una orden y procesar el pago seguro con Stripe",
     *     tags={"Órdenes y Pagos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"stripe_token", "items"},
     *             @OA\Property(property="stripe_token", type="string", example="tok_visa", description="Token de tarjeta de prueba provisto por Stripe"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Compra completada con éxito"),
     *     @OA\Response(response=400, description="Error en la validación, procesamiento o stock insuficiente")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stripe_token' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            $total = 0;
            $itemsToProcess = [];

            // 1. Validar el Stock e inventario
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json(['error' => "Stock insuficiente para el producto: {$product->name}"], 400);
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ];
            }

            // 2. Crear la Orden Maestro
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $total,
                'status' => 'pending'
            ]);

            // 3. Registrar el detalle de productos y decrementar stock
            foreach ($itemsToProcess as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product']->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price']
                ]);

                $itemData['product']->decrement('stock', $itemData['quantity']);
            }

            // 4. Integrar con el SDK oficial de Stripe
            Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

            $intent = PaymentIntent::create([
                'amount' => $total * 100, // Stripe lee montos en centavos
                'currency' => 'usd',
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => ['token' => $request->stripe_token],
                ],
                'confirm' => true,
                'off_session' => true,
            ]);

            // 5. Validar estado del pago
            if ($intent->status === 'succeeded') {
                $order->update(['status' => 'paid']);

                Payment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $intent->id,
                    'amount' => $total,
                    'currency' => 'usd',
                    'status' => 'succeeded'
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Compra realizada y procesada de forma segura',
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $intent->id,
                    'total_pagado' => $total
                ], 201);
            }

            throw new \Exception("La pasarela no pudo procesar el cargo correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar la compra: ' . $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/history",
     *     summary="Consultar el historial de compras del usuario autenticado",
     *     tags={"Órdenes y Pagos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Historial de compras devuelto exitosamente")
     * )
     */
    public function history()
    {
        $orders = Order::with(['items.product', 'payment'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders, 200);
    }
}
