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
        Schema::table('commande', function (Blueprint $table) {
            $table->string('point_relais_nom', 255)->nullable()->after('id_mode_livraison');
            $table->string('point_relais_adresse', 255)->nullable()->after('point_relais_nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commande', function (Blueprint $table) {
            $table->dropColumn('point_relais_nom');
            $table->dropColumn('point_relais_adresse');
        });
    }
};
