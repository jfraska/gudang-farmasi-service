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
        Schema::create('receives', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_faktur')->unique();
            $table->unsignedBigInteger('purchase_order_id')->nullable(); // foreign untuk PO
            $table->foreign('purchase_order_id')
                ->references('id')
                ->on('purchase_orders')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->dateTime('tanggal_pembelian');
            $table->dateTime('tanggal_jatuh_tempo')->nullable();
            $table->integer('ppn')->nullable();
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
        Schema::dropIfExists('receives');
    }
};
