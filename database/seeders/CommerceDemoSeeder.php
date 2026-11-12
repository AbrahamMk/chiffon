<?php

namespace Database\Seeders;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Catalog\Models\ProductImage;
use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Delivery\Models\DeliveryZone;
use App\Domains\Inventory\Models\InventoryLocation;
use App\Domains\Marketing\Models\Coupon;
use App\Domains\Payments\Models\PaymentMethod;
use App\Domains\Production\Models\ProductionStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommerceDemoSeeder extends Seeder
{
    public function run(): void
    {
        InventoryLocation::query()->firstOrCreate(
            ['code' => 'MAIN'],
            ['name' => 'Main Store', 'is_default' => true, 'is_active' => true]
        );

        foreach ([
            ['name' => 'Cash', 'code' => 'cash', 'requires_verification' => false, 'sort_order' => 1],
            ['name' => 'Bank Transfer', 'code' => 'bank_transfer', 'requires_verification' => true, 'sort_order' => 2],
            ['name' => 'Manual', 'code' => 'manual', 'requires_verification' => true, 'sort_order' => 3],
        ] as $method) {
            PaymentMethod::query()->firstOrCreate(['code' => $method['code']], $method + ['is_active' => true]);
        }

        $stages = [
            'Order Review', 'Fabric Preparation', 'Cutting', 'Sewing', 'Embroidery',
            'Finishing', 'Quality Inspection', 'Packaging', 'Completed',
        ];
        foreach ($stages as $i => $name) {
            ProductionStage::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i + 1, 'is_active' => true]
            );
        }

        // Demo zones — fees are sample placeholders, not real business rates.
        foreach ([
            ['name' => 'Addis Ababa Central (sample)', 'code' => 'AA-CENTRAL', 'fee' => 150],
            ['name' => 'Addis Ababa Outer (sample)', 'code' => 'AA-OUTER', 'fee' => 250],
            ['name' => 'Customer Pickup', 'code' => 'PICKUP', 'fee' => 0],
        ] as $zone) {
            DeliveryZone::query()->firstOrCreate(
                ['code' => $zone['code']],
                $zone + ['is_active' => true, 'description' => 'Sample demo zone — configure real fees in admin.']
            );
        }

        Coupon::query()->firstOrCreate(
            ['code' => 'WELCOME100'],
            [
                'name' => 'Sample welcome discount',
                'type' => 'fixed',
                'value' => 100,
                'min_order_amount' => 500,
                'usage_limit' => 100,
                'used_count' => 0,
                'is_active' => true,
            ]
        );

        $tibeb = ProductCategory::query()->firstOrCreate(
            ['slug' => 'tibeb'],
            ['name' => 'Tibeb', 'description' => 'Sample Tibeb collection', 'is_featured' => true, 'is_active' => true, 'sort_order' => 1]
        );
        $chiffon = ProductCategory::query()->firstOrCreate(
            ['slug' => 'chiffon'],
            ['name' => 'Chiffon', 'description' => 'Sample Chiffon collection', 'is_featured' => true, 'is_active' => true, 'sort_order' => 2]
        );

        $this->seedProduct($tibeb, 'Habesha Kemis Sample', 'habesha-kemis-sample', 'SAMPLE-HK', 4500, [
            ['sku' => 'SAMPLE-HK-W-M', 'name' => 'White / M', 'price' => 4500, 'stock' => 8],
            ['sku' => 'SAMPLE-HK-W-L', 'name' => 'White / L', 'price' => 4500, 'stock' => 5],
            ['sku' => 'SAMPLE-HK-C-M', 'name' => 'Cream / M', 'price' => 4700, 'stock' => 4],
        ]);

        $this->seedProduct($chiffon, 'Chiffon Dress Sample', 'chiffon-dress-sample', 'SAMPLE-CD', 3800, [
            ['sku' => 'SAMPLE-CD-S', 'name' => 'S', 'price' => 3800, 'stock' => 6],
            ['sku' => 'SAMPLE-CD-M', 'name' => 'M', 'price' => 3800, 'stock' => 10],
            ['sku' => 'SAMPLE-CD-L', 'name' => 'L', 'price' => 3900, 'stock' => 3],
        ]);
    }

    /**
     * @param  array<int, array{sku: string, name: string, price: float|int, stock: int}>  $variants
     */
    private function seedProduct(ProductCategory $category, string $name, string $slug, string $sku, float $price, array $variants): void
    {
        $product = Product::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'product_category_id' => $category->id,
                'name' => $name,
                'sku' => $sku,
                'description' => 'Demo sample product for development. Prices are placeholders.',
                'fabric_info' => 'Sample fabric info',
                'care_instructions' => 'Dry clean recommended (sample).',
                'base_price' => $price,
                'status' => 'active',
                'is_featured' => true,
                'track_inventory' => true,
                'published_at' => now(),
            ]
        );

        foreach ($variants as $variant) {
            ProductVariant::query()->firstOrCreate(
                ['sku' => $variant['sku']],
                [
                    'product_id' => $product->id,
                    'name' => $variant['name'],
                    'price' => $variant['price'],
                    'stock_quantity' => $variant['stock'],
                    'reserved_quantity' => 0,
                    'is_active' => true,
                ]
            );
        }

        if ($product->images()->doesntExist()) {
            $path = $this->createPlaceholderImage($product, '#7A1F2B', '#C6A75E');
            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $path,
                'alt_text' => $product->name.' (sample)',
                'sort_order' => 1,
                'is_primary' => true,
            ]);

            $pathSecondary = $this->createPlaceholderImage($product, '#1F1A17', '#F7F3EE', '2');
            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $pathSecondary,
                'alt_text' => $product->name.' detail (sample)',
                'sort_order' => 2,
                'is_primary' => false,
            ]);
        }
    }

    private function createPlaceholderImage(Product $product, string $fromHex, string $toHex, string $suffix = '1'): string
    {
        $directory = 'products/'.$product->id;
        Storage::disk('public')->makeDirectory($directory);

        $relative = $directory.'/sample-'.$suffix.'.jpg';
        $absolute = Storage::disk('public')->path($relative);

        $width = 900;
        $height = 1125;
        $image = imagecreatetruecolor($width, $height);

        [$r1, $g1, $b1] = sscanf($fromHex, '#%02x%02x%02x');
        [$r2, $g2, $b2] = sscanf($toHex, '#%02x%02x%02x');

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / max(1, $height - 1);
            $r = (int) round($r1 + ($r2 - $r1) * $ratio);
            $g = (int) round($g1 + ($g2 - $g1) * $ratio);
            $b = (int) round($b1 + ($b2 - $b1) * $ratio);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $width, $y, $color);
        }

        $white = imagecolorallocate($image, 255, 255, 255);
        $label = $product->name;
        imagestring($image, 5, 40, (int) ($height / 2) - 20, substr($label, 0, 40), $white);
        imagestring($image, 3, 40, (int) ($height / 2) + 10, 'Sample placeholder image', $white);

        imagejpeg($image, $absolute, 85);
        imagedestroy($image);

        return $relative;
    }
}
