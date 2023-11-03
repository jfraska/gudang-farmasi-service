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
        Schema::create('purchase_order_details', function (Blueprint $table)
        {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id'); // foreign untuk PO
            $table->foreign('purchase_order_id')
                ->references('id')
                ->on('purchase_orders')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->string('item'); // foreign untuk barang
            $table->foreign('item')
                ->references('id')
                ->on('items')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->string('sediaan'); // foreign untuk sediaan
            $table->foreign('sediaan')
                ->references('id')
                ->on('sediaans')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->integer('jumlah');
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
        Schema::dropIfExists('purchase_order_details');
    }
};