<?php

namespace App\Console\Commands;

use App\Http\Controllers\ProductController;
use Illuminate\Console\Command;

class DailyProductImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import open food facts data into database every day midnight';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $product = new ProductController();
        $this->info($product->import());
    }
}
