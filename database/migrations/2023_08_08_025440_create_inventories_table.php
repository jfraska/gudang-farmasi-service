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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gudang'); // foreign dari gudang    
            $table->foreign('gudang')
                ->references('id')
                ->on('gudangs')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->string('pos_inventory'); // foreign untuk pos inventory
            $table->foreign('pos_inventory')
                ->references('id')
                ->on('pos_inventories')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
            $table->integer('stok');
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
        Schema::dropIfExists('inventories');
    }
};
