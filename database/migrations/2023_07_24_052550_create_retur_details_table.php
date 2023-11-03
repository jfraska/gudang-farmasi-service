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
        Schema::create('retur_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gudang'); // foreign dari gudang    
            $table->foreign('gudang')
                ->references('id')
                ->on('gudangs')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->unsignedBigInteger('retur_id'); // foreign untuk pembelian
            $table->foreign('retur_id')
                ->references('id')
                ->on('returs')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->integer('jumlah')->nullable();
            $table->string('alasan')->nullable();
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
        Schema::dropIfExists('retur_details');
    }
};
