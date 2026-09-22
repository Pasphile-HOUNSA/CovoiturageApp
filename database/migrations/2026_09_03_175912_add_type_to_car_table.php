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
        Schema::table('car', function (Blueprint $table) {
            // Vous pouvez utiliser 'car' comme valeur par défaut
            // ou définir une liste d'options si c'est un enum
            $table->string('type')->default('Voiture')->after('idUser');

            // Si vous préférez restreindre les types (ex: voiture, moto, etc.) :
            // $table->enum('type', ['car', 'moto', 'van'])->default('car')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
