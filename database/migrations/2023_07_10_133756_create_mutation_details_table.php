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
        Schema::create('mutation_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gudang')->nullable(); // foreign dari gudang    
            $table->foreign('gudang')
                ->references('id')
                ->on('gudangs')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->unsignedBigInteger('mutation_id'); // foreign dari mutation
            $table->foreign('mutation_id')
                ->references('id')
                ->on('mutations')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->string('item'); // foreign untuk barang
            $table->foreign('item')
                ->references('id')
                ->on('items')
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
        Schema::dropIfExists('mutation_details');
    }
};
