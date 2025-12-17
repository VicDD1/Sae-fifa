<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    DB::statement('ALTER TABLE carte_bancaire DROP CONSTRAINT carte_bancaire_date_expiration_check');
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    DB::statement("
        ALTER TABLE carte_bancaire
        ADD CONSTRAINT carte_bancaire_date_expiration_check
        CHECK (date_expiration ~ '^(0[1-9]|1[0-2])/[0-9]{2}$')
    ");
}
};
