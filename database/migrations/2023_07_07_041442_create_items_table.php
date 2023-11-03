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
        Schema::create('items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('kode')->unique();
            $table->string('name');
            $table->string('sediaan'); // foreign untuk sediaan
            $table->foreign('sediaan')
                ->references('id')
                ->on('sediaans')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->string('kategori'); // foreign untuk jenis surat
            $table->foreign('kategori')
                ->references('kode')
                ->on('potypes')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->boolean('status')->default(true);
            $table->integer('minimum_stok');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
