<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $catalogPath = __DIR__.'/menu_items.json';
        $catalog = json_decode(file_get_contents($catalogPath), true, flags: JSON_THROW_ON_ERROR);
        $missingImages = 0;

        foreach ($catalog as $item) {
            if (!file_exists(public_path($item['image_url']))) {
                $item['image_url'] = null;
                $missingImages++;
            }

            MenuItem::updateOrCreate(
                ['product_code' => $item['product_code']],
                $item
            );
        }

        if ($missingImages > 0) {
            $this->command?->warn("Imported the catalog without {$missingImages} image(s) that were not present in the source files.");
        }
    }
}
