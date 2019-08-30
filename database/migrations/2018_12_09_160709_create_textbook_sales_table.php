<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextbookSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textbook_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_id')->index('index_supplier_id')->nullable(false)->comment('販売会社ID');
            $table->integer('textbook_id')->index('index_textbook_id')->nullable(false)->comment('教科書マスタのID');
            $table->integer('price')->default(0)->comment('販売価格');
            $table->integer('list_price')->default(0)->comment('定価');
            $table->string('url')->default('')->comment('販売元ページ');
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
        Schema::dropIfExists('textbook_sales');
    }
}
