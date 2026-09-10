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
        Schema::table('trajet', function (Blueprint $table) {
            $table->string('lieuDepart')->after('idCar');
            $table->string('lieuArrivee')->after('lieuDepart');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajet', function (Blueprint $table) {
            $table->dropColumn('lieuDepart');
            $table->dropColumn('lieuArrivee');
        });
    }
};
