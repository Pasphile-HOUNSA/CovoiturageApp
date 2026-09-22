<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('car', function (Blueprint $table) {
        $table->id('idCar');
        $table->unsignedBigInteger('idUser');
        $table->string('mark', 255);
        $table->string('model', 255);
        $table->string('registrationNumber', 255)->unique();
        $table->integer('nbrPlaces');
        $table->string('color', 255);
        $table->string('carte_grise_numero', 255)->nullable();
        $table->string('carte_grise_scan', 255)->nullable();
        $table->string('assurance_numero', 255)->nullable();
        $table->date('assurance_expiration')->nullable();
        $table->timestamps();
        $table->softDeletes();
        $table->foreign('idUser')->references('idUser')->on('users')->onDelete('cascade')->onUpdate('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car');
    }
};
