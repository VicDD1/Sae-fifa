<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // ID de l'utilisateur connecté
            $table->unsignedBigInteger('user_id');

            // Produit
            $table->unsignedBigInteger('id_produit');

            // Variante (taille & couleur)
            $table->unsignedBigInteger('id_taille')->nullable();
            $table->unsignedBigInteger('id_colori')->nullable();

            // Quantité
            $table->integer('quantite')->default(1);

            // Image (optionnelle)
            $table->string('image')->nullable();

            $table->timestamps();

            // Clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
