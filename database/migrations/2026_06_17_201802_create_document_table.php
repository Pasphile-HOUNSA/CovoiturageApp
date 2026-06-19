<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document', function (Blueprint $table) {
            $table->id('idDocument');
            $table->unsignedBigInteger('idUser');
            $table->unsignedBigInteger('idCar')->nullable();
            $table->string('numPiece', 255);
            $table->string('scanPiece', 255);
            $table->string('numPermis', 255)->nullable();
            $table->date('permis_expiration')->nullable();
            $table->string('scanPermis', 255)->nullable();
            $table->string('certificatApt', 255)->nullable();
            $table->string('certificatApt_scan', 255)->nullable();
            $table->string('carte_grise_numero', 255)->nullable();
            $table->string('carte_grise_scan', 255)->nullable();
            $table->string('assurance_numero', 255)->nullable();
            $table->date('assurance_expiration')->nullable();
            $table->string('photoId', 255);
            $table->timestamps();

            $table->foreign('idUser')->references('idUser')->on('user')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idCar')->references('idCar')->on('car')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document');
    }
};
