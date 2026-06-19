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
        Schema::create('trajet', function (Blueprint $table) {
            $table->id('idTrajet');
            $table->unsignedBigInteger('idCar');
            $table->date('dateDepart');
            $table->time('heureDepart');
            $table->decimal('price', 10, 2);
            $table->integer('placesDispo');
            $table->enum('statut', ['en attente','en cours','annulé','terminé']);
            $table->timestamps();

            $table->foreign('idCar')
                  ->references('idCar')->on('car')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajet');
    }
};
