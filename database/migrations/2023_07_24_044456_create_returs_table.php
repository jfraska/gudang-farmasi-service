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
        Schema::create('returs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_retur')->unique();
            $table->unsignedBigInteger('receive_id'); // foreign dar receive 
            $table->foreign('receive_id')
                ->references('id')
                ->on('receives')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->dateTime('tanggal_retur')->nullable();
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
        Schema::dropIfExists('returs');
    }
    
};
