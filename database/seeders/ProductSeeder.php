<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\ProductVariant::truncate();
        \App\Models\Product::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
        $products = [
            [
                'category_id' => 1,
                'name' => 'Mini Dress Bunga Cantik',
                'description' => 'Dress cantik dengan motif bunga yang elegan dan nyaman dipakai.',
                'price' => 150000,
                'discount_price' => 120000,
                'stock' => 50,
            ],
            [
                'category_id' => 2,
                'name' => 'Dress Pesta Mewah',
                'description' => 'Dress pesta yang elegan dan mewah untuk acara spesial.',
                'price' => 350000,
                'discount_price' => 300000,
                'stock' => 30,
            ],
            [
                'category_id' => 3,
                'name' => 'Casual Dress Harian',
                'description' => 'Dress casual yang nyaman untuk aktivitas sehari-hari.',
                'price' => 120000,
                'discount_price' => null,
                'stock' => 100,
            ],
            [
                'category_id' => 4,
                'name' => 'Batik Modern',
                'description' => 'Batik modern dengan desain kontemporer yang stylish.',
                'price' => 200000,
                'discount_price' => 175000,
                'stock' => 40,
            ],
            [
                'category_id' => 5,
                'name' => 'Blouse Polos Elegan',
                'description' => 'Blouse polos yang elegan cocok untuk berbagai kesempatan.',
                'price' => 95000,
                'discount_price' => null,
                'stock' => 75,
            ],
        ];

        $sizes = ['S', 'M', 'L', 'XL'];
        $colors = [
            ['color' => 'Merah', 'color_hex' => '#FF0000'],
            ['color' => 'Biru', 'color_hex' => '#0000FF'],
            ['color' => 'Hitam', 'color_hex' => '#000000'],
            ['color' => 'Putih', 'color_hex' => '#FFFFFF'],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'category_id' => $productData['category_id'],
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'discount_price' => $productData['discount_price'],
                'stock' => $productData['stock'],
            ]);

            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => $color['color'],
                        'color_hex' => $color['color_hex'],
                        'stock' => 10,
                    ]);
                }
            }
        }
    }
}