<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer les CHECK incompatibles avec le chiffrement
        DB::statement('ALTER TABLE carte_bancaire DROP CONSTRAINT IF EXISTS carte_bancaire_numero_carte_check');
        DB::statement('ALTER TABLE carte_bancaire DROP CONSTRAINT IF EXISTS carte_bancaire_date_expiration_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Réajouter les CHECK (format en clair) — optionnel
        DB::statement("
            ALTER TABLE carte_bancaire
            ADD CONSTRAINT carte_bancaire_numero_carte_check
            CHECK (numero_carte ~ '^[0-9]{16}$')
        ");

        DB::statement("
            ALTER TABLE carte_bancaire
            ADD CONSTRAINT carte_bancaire_date_expiration_check
            CHECK (date_expiration ~ '^(0[1-9]|1[0-2])/[0-9]{2}$')
        ");
    }
};
    