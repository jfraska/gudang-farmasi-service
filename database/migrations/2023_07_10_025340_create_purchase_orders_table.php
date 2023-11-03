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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_po')->unique()->nullable();
            $table->dateTime('tanggal_po');
            $table->string('supplier'); // foreign masih di service rs
            $table->longText('keterangan')->nullable();
            $table->string('gudang');
            $table->string('potype'); // foreign untuk jenis surat
            $table->foreign('potype')
                ->references('kode')
                ->on('potypes')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
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
        Schema::dropIfExists('purchase_orders');
    }
};
