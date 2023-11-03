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
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_batch')->unique();
            $table->string('gudang');
            $table->unsignedBigInteger('receive_id'); // foreign untuk receive detail
            $table->foreign('receive_id')
                ->references('id')
                ->on('receives')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->integer('stok');
            $table->integer('harga_beli_satuan')->nullable();
            $table->integer('harga_jual_satuan')->nullable();
            $table->string('item'); // foreign untuk barang
            $table->foreign('item')
                ->references('id')
                ->on('items')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->date('tanggal_ed')->nullable();
            $table->integer('diskon')->nullable();
            $table->integer('margin')->nullable();
            $table->integer('total_pembelian')->nullable();    
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
        Schema::dropIfExists('gudangs');
    }
};
