<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_common')->create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('摘要');
            $table->string('status')->comment('ステータス');
            $table->string('type')->comment('発注タイプ');
            $table->integer('ordered_user_id')->index('index_ordered_user_id')->comment('発注者');
            $table->integer('target_user_id')->nullable(true)->index('index_target_user_id')->comment('納品先ユーザー');
            $table->integer('amount')->comment('数量');
            $table->integer('unit_price')->comment('単価');
            $table->string('item_type')->comment('品タイプ');
            $table->integer('place_id')->nullable(true)->comment('場所ID');
            $table->string('orderable_type')->nullable(true)->comment('発注リレーションモデル');
            $table->integer('orderable_id')->nullable(true)->comment('発注リレーションID');
            $table->string('remarks',10000)->nullable(true)->comment('備考');
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
        Schema::connection('mysql_common')->dropIfExists('orders');
    }
}
