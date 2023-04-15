<?php

namespace App\Http\Controllers;

use App\Models\ImportsHistoric;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    const MAX_INSERTED_ROWS = 100;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::simplePaginate(20);
        return response()->json($products);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $product = Product::where('code', $code)->get();

        if (!$product->isEmpty()) {
            return response()->json([
                'product' => $product
            ]);
        }else{
            return response()->json([
                'message' => 'no results found for code '.$code
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        $product = Product::where('code', $code)->get();

        try {
            if (!$product->isEmpty()) {

                $request->validate([
                    // 'code' => 'required|unique:App\Models\Product',
                    'status' => 'required|in:draft,trash,published',
                    'url' => 'url',
                    'image_url' => 'url',
                    'imported_t' => 'date'
                ]);
                
                $updated = Product::where('code', $code)->update($request->except('code', 'api_token'));

                if ($updated) {
                    $product = Product::where('code', $code)->get();
                    return response()->json([
                        'message' => 'Product with code '.$code. ' has been successfully updated',
                        'product' => $product
                    ]);
                }

            }else{
                return response()->json([
                    'message' => 'no results found for code '.$code
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($code)
    {
        $product = Product::where('code', $code)->get();

        try {
            if (!$product->isEmpty()) {

                $deleted = Product::where('code', $code)->update([
                    'status' => 'trash'
                ]);

                if ($deleted) {
                    return response()->json([
                        'message' => 'Product with code '.$code. ' has been successfully deleted'
                    ]);
                }

            }else{
                return response()->json([
                    'message' => 'no results found for code '.$code
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Import json data from api Open Food Facts into database.
    */
    public function import()
    {        

        $fileToDownload = ImportsHistoric::select('name')->where('status', 'processing')->firstOrFail();

        $url = "https://challenges.coode.sh/food/data/json/{$fileToDownload->name}";

        // Baixa o arquivo caso nao exista
        if (! Storage::disk('public')->exists("data/{$fileToDownload->name}")) {
            try {
                Storage::disk('public')->put("data/{$fileToDownload->name}", file_get_contents($url));
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        $path = Storage::disk('public')->path("data/{$fileToDownload->name}");
        $file = fopen("compress.zlib://{$path}", "r");
        
        $count_inserted_rows = 0;
        while (!feof($file)) {
            if ($count_inserted_rows == self::MAX_INSERTED_ROWS) {
                break;
            }else{
                $row = json_decode(fgets($file));
                try {
                    $code = preg_replace("/[^0-9]/", "",$row->code);
                    $product = Product::where('code', $code)->get();
                    if ($product->isEmpty()) {
                        $created = Product::create([
                            'code' => $code,
                            'url' => $row->url,
                            'creator' => $row->creator,            
                            'imported_t' => Carbon::now()->format('Y-m-d'),
                            'status' => 'draft',
                            'product_name' => $row->product_name,
                            'quantity' => $row->quantity,
                            'brands' => $row->brands,
                            'categories' => $row->categories,
                            'labels' => $row->labels,
                            'cities' => $row->cities,
                            'purchase_places' => $row->purchase_places,
                            'stores' => $row->stores,
                            'ingredients_text' => $row->ingredients_text,
                            'traces' => $row->traces,
                            'serving_size' => $row->serving_size,
                            'serving_quantity' => floatval($row->serving_quantity),
                            'nutriscore_score' => intval(preg_replace("/[^0-9]/", "", $row->nutriscore_score)),
                            'nutriscore_grade' => $row->nutriscore_grade,
                            'main_category' => $row->main_category,
                            'image_url' => $row->image_url
                        ]);

                        if ($created) {
                            $count_inserted_rows++;
                        }

                        if (feof($file)) {
                            $fileToDownload = ImportsHistoric::where('name', $fileToDownload->name)->update([
                                'status' => 'completed'
                            ]);
                        }                        
                    }
                } catch (\Exception $e) {
                    return $e->getMessage();
                }

            }
        }

        //Remove files completed from storage
        $filesCompleted = ImportsHistoric::select('name')->where('status', 'completed')->get();

        foreach ($filesCompleted as $key => $file) {
            if (Storage::disk('public')->exists("data/{$file->name}")) {
                Storage::disk('public')->delete("data/{$file->name}");
            }
        }

        //Utilizo o campo Log para salvar os dados de execução da crontab
        ImportsHistoric::where('name', $fileToDownload->name)->update([
            'log' => 'Memory Used: ' . memory_get_usage() . ' Runtime: ' . microtime(true) - LARAVEL_START . ' seconds',
        ]);
                            

        return 'Importação de Linhas do arquivo'.$fileToDownload->name.' foi concluída';
    }
}
