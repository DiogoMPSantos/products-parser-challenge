<?php

namespace Tests\Feature;

use App\Models\Product;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test test_list_products.
     *
     * @return void
     */
    public function test_list_paginate_products()
    {
        $response = $this->getJson('/api/products');
        $response->assertJsonCount(20, 'data');    
    }

    public function test_crontab_api_details()
    {
        $response = $this->getJson('/api/');
        $response->assertJsonStructure([
                'api_version',
                'crontab_log',
                'crontab_last_execution'
            ]);
    }

    public function test_find_product()
    {
        $product = Product::where('status', 'draft')->first();
        $response = $this->getJson("/api/products/{$product->code}");
        $response->assertJsonFragment(['code' => $product->code]);
        $response->assertJsonCount(1, 'product'); 
        
        $response->assertJsonStructure([
                'product' => [[
                    'id',
                    'code',
                    'url',
                    'creator',            
                    'imported_t',
                    'status',
                    'product_name',
                    'quantity',
                    'brands',
                    'categories',
                    'labels',
                    'cities',
                    'purchase_places',
                    'stores',
                    'ingredients_text',
                    'traces',
                    'serving_size',
                    'serving_quantity',
                    'nutriscore_score',
                    'nutriscore_grade',
                    'main_category',
                    'image_url',
                    'created_at',
                    'updated_at'
                ]]
            ]);
    }

    public function test_not_find_product()
    {
        $code = 0;
        $response = $this->getJson("/api/products/$code");
        $response->assertJsonFragment(['message' => "no results found for code {$code}"]);
    }

    public function test_delete_product()
    {
        $product = Product::where('status', 'draft')->first();
        $response = $this->deleteJson("/api/products/{$product->code}");
        $response->assertJsonFragment(['message' => 'Product with code '.$product->code. ' has been successfully deleted']);

        $product = Product::where('code', $product->code)->first();
        $this->assertSame('trash', $product->status);
    }

    public function test_not_delete_product()
    {
        $code = 0;
        $response = $this->deleteJson("/api/products/$code");
        $response->assertJsonFragment(['message' => "no results found for code {$code}"]);
    }

    public function test_update_product()
    {
        $product = Product::where('status', 'draft')->first();

        $body= [
            "url" => "https://learning.postman.com/docs/sending-requests/authorization/#api-key",
            "creator" => "Diogo Teste PUT",
            "imported_t" => "2023-04-14",
            "status" => "draft",
            "product_name" => "Produto Teste",
            "quantity" => "2",
            "brands" => "brandTeste",
            "categories" => "CatTeste",
            "labels" => "LTeste",
            "cities" => "CTeste",
            "purchase_places" => "ppTeste",
            "stores" => "sTeste",
            "ingredients_text" => "itTeste",
            "traces" => "tTeste",
            "serving_size" => "ssTeste",
            "serving_quantity" => 0,
            "nutriscore_score" => 0,
            "nutriscore_grade" => "ngTeste",
            "main_category" => "mcTest",
            "image_url" => "https://github.com/DiogoMPSantos/products-parser-challenge"
        ];

        $response = $this->putJson("/api/products/{$product->code}", $body);
        $response->assertJsonFragment(['message' => 'Product with code '.$product->code. ' has been successfully updated']);

        $product = Product::where('code', $product->code)->first();

        $updated = collect($product)->except(['id', 'code', 'created_at', 'updated_at'])->toJson();
        $body = collect($body)->toJson();
        $this->assertJsonStringEqualsJsonString($body, $updated);
    }

    public function test__invalid_fields_in_update_product()
    {
        $product = Product::where('status', 'draft')->first();

        $body= [
            "url" => "Invalid Url",
            "creator" => "Diogo Teste PUT",
            "imported_t" => "2023-04-14",
            "status" => "draft",
            "image_url" => "https://github.com/DiogoMPSantos/products-parser-challenge"
        ];

        $response = $this->putJson("/api/products/{$product->code}", $body);
        $response->assertJsonFragment(['message' => 'The url must be a valid URL.']);

        $body["url"] = "https://github.com/DiogoMPSantos/products-parser-challenge";
        $body["status"] = "invalid status";
        $response = $this->putJson("/api/products/{$product->code}", $body);
        $response->assertJsonFragment(['message' => 'The selected status is invalid.']);
    }
}
