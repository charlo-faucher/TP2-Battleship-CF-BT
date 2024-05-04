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
        Schema::create('coordonnees_bateaux_adversaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partie_id');
            $table->string('coordonnee', 4);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->tinyInteger('resultat')->nullable();
            $table->timestamps();

            $table->foreign('partie_id')
                ->references('id')
                ->on('parties')
                ->onDelete('cascade');
            $table->foreign('source_id')
                ->references('id')
                ->on('coordonnees_bateaux_adversaires')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordonnees_bateaux_adversaires');
    }
};
