<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop Gamer Pro 15',
            'description' => 'Procesador Intel i7, 16GB RAM, 512GB SSD, Tarjeta gráfica RTX 4060.',
            'price' => 1299.99,
            'stock' => 10
        ]);

        Product::create([
            'name' => 'Audífonos Inalámbricos ANC',
            'description' => 'Cancelación activa de ruido, batería de 40 horas, sonido de alta fidelidad.',
            'price' => 149.50,
            'stock' => 25
        ]);

        Product::create([
            'name' => 'Teclado Mecánico RGB',
            'description' => 'Switches mecánicos customizables, retroiluminación RGB completa, distribución en español.',
            'price' => 89.99,
            'stock' => 15
        ]);

        Product::create([
            'name' => 'Mouse Ergonómico Inalámbrico',
            'description' => 'Sensor óptico de alta precisión, botones laterales programables, batería recargable.',
            'price' => 59.99,
            'stock' => 30
        ]);
    }
}
