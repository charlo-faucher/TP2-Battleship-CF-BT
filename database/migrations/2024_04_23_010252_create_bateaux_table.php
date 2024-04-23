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
        Schema::create('bateaux', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('partie_id');
            $table->boolean('est_coule');
            $table->timestamps();

            $table->foreign('type_id')
                ->references('id')
                ->on('types_bateaux')
                ->onDelete('cascade');

            $table->foreign('partie_id')
                ->references('id')
                ->on('parties')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bateaux');
    }
};
