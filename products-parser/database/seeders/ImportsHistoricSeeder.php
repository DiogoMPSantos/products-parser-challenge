<?php

namespace Database\Seeders;

use App\Models\ImportHistoric;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportsHistoricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('imports_historic')->insert([
            [
                'name' => 'products_01.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_02.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_03.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_04.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_05.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_06.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_07.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_08.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'products_09.json.gz',
                'status' => 'processing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

    ]);
    }
}
