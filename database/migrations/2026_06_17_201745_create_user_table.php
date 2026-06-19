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
        Schema::create('user', function (Blueprint $table) {
            $table->id('idUser');
            $table->string('firstName', 255);
            $table->string('lastName', 255);
            $table->char('sex', 10);
            $table->date('birthday');
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('phoneNumber', 255);
            $table->string('address', 255);
            $table->dateTime('registrationDate');
            $table->dateTime('lastLogin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
