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
        Schema::create('reservation', function (Blueprint $table) {
            $table->id('idRes');
            $table->unsignedBigInteger('idTrajet');
            $table->unsignedBigInteger('idUser');
            $table->dateTime('dateRes');
            $table->integer('placesRes');
            $table->decimal('totalAmount', 10, 2);
            $table->enum('statut', ['confirmée','annulée','en_attente']);
            $table->dateTime('dateAnnulation')->nullable();
            $table->text('raisonAnnulation')->nullable();
            $table->enum('auteurAnnulation', ['Passager','Conducteur','Administrateur'])->nullable();
            $table->timestamps();

            $table->foreign('idTrajet')->references('idTrajet')->on('trajet')->onDelete('cascade') ->onUpdate('cascade');
            $table->foreign('idUser')->references('idUser')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation');
    }
};
