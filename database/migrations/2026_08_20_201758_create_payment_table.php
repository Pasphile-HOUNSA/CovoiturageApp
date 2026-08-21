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
        Schema::create('payment', function (Blueprint $table) {
            $table->id('idPayment');
            $table->unsignedBigInteger('idRes');
            $table->decimal('amount', 10, 2);
            $table->dateTime('datePayment');
            $table->enum('method', ['Espèce','Mobile money','Carte bancaire']);
            $table->enum('statut', ['en attente','validée','refusée']);
            $table->timestamps();

            $table->foreign('idRes')
                  ->references('idRes')
                  ->on('reservation')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
