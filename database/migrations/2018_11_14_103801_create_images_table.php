<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false)->comment('保存ファイル名');
            $table->string('type')->nullable(false)->comment('mimetype');
            $table->integer('size')->nullable(false)->comment('ファイルサイズ');
            $table->string('s3_url')->nullable(false)->comment('S3ダウンロードURL');
            $table->string('alias')->comment('エイリアス');
            $table->date('publiced_at')->comment('公開日');
            $table->integer('create_user_id')->index('index_create_user_id')->comment('作成ユーザーID');
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
        Schema::dropIfExists('images');
    }
}
