<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->text('url');
            $table->text('creator');            
            $table->date('imported_t');
            $table->enum('status', ['draft', 'trash', 'published']);
            $table->text('product_name');
            $table->text('quantity');
            $table->text('brands');
            $table->text('categories');
            $table->text('labels');
            $table->text('cities');
            $table->text('purchase_places');
            $table->text('stores');
            $table->text('ingredients_text');
            $table->text('traces');
            $table->text('serving_size');
            $table->float('serving_quantity', 8, 2);
            $table->integer('nutriscore_score');
            $table->char('nutriscore_grade');
            $table->text('main_category');
            $table->text('image_url');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
