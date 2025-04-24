<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('file_type');
            $table->string('file_size');
            $table->unsignedInteger('total_download')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->unsignedBigInteger('google_account_id');
            $table->unsignedBigInteger('file_id');
            $table->string('google_file_id');
            $table->unsignedInteger('total_download')->default(0);
            $table->timestamps();

            $table->foreign('google_account_id')->references('id')->on('google_accounts')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploaded_files');
        Schema::dropIfExists('files');
    }
}
