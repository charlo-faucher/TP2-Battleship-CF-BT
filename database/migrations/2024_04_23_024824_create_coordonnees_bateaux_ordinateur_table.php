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
        Schema::create('coordonnees_bateaux_ordinateur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bateau_id');
            $table->string('coordonnee', 4);
            $table->timestamps();

            $table->foreign('bateau_id')
                ->references('id')
                ->on('bateaux_ordinateur')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordonnees_bateaux_ordinateur');
    }
};
