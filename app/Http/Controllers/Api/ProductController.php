<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Productos",
 *     description="Operaciones para el catálogo de productos"
 * )
 */
class ProductController extends Controller
{
    public function __construct()
    {
        // Protege los métodos de escritura, dejando index y show públicos
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listado público de productos",
     *     tags={"Productos"},
     *     @OA\Response(response=200, description="Lista de productos obtenida exitosamente")
     * )
     */
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear un nuevo producto (Admin)",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "stock"},
     *             @OA\Property(property="name", type="string", example="Monitor 4K Pro"),
     *             @OA\Property(property="description", type="string", example="Monitor de 27 pulgadas IPS"),
     *             @OA\Property(property="price", type="number", format="float", example=349.99),
     *             @OA\Property(property="stock", type="integer", example=12)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Producto creado con éxito"),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obtener el detalle de un producto específico",
     *     tags={"Productos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalles del producto"),
     *     @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return response()->json($product, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Actualizar un producto existente (Admin)",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Monitor 4K Pro Modificado"),
     *             @OA\Property(property="price", type="number", format="float", example=329.99),
     *             @OA\Property(property="stock", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Producto actualizado"),
     *     @OA\Response(response=44),
     *     @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->update($request->all());
        return response()->json($product, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Eliminar un producto (Admin)",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Producto eliminado con éxito"),
     *     @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Producto eliminado con éxito'], 200);
    }
}
