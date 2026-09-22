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
            $table->string('numPermis', 255)->nullable();
            $table->date('permis_expiration')->nullable();
            $table->string('scanPermis', 255)->nullable();
            $table->string('certificatApt', 255)->nullable();
            $table->string('certificatApt_scan', 255)->nullable();
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
        Schema::dropIfExists('document');
    }
};
