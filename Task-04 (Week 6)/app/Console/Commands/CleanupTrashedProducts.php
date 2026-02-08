<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupTrashedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:cleanup-trashed {--days=30 : Number of days after which trashed products are permanently deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete products that have been in the trash for more than N days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        $products = Product::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days))
            ->get();

        if ($products->isEmpty()) {
            $this->info("No trashed products older than {$days} days found.");
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($products as $product) {
            // Delete image if it exists
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->forceDelete();
            $count++;
        }

        $this->info("Permanently deleted {$count} trashed product(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
