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
        Schema::create('opinion', function (Blueprint $table) {
            $table->id('idOpinion');
            $table->unsignedBigInteger('idTrajet');
            $table->integer('note');
            $table->text('comment')->nullable();
            $table->dateTime('dateOpinion');
            $table->unsignedBigInteger('idEvaluateur');
            $table->unsignedBigInteger('idEvalue');
            $table->timestamps();

            $table->foreign('idTrajet')->references('idTrajet')->on('trajet')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idEvaluateur')->references('idUser')->on('user')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idEvalue')->references('idUser')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opinion');
    }
};
