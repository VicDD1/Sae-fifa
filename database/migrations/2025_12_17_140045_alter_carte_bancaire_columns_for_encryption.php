<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('carte_bancaire', function (Blueprint $table) {
        $table->text('numero_carte')->change();
        $table->text('date_expiration')->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('carte_bancaire', function (Blueprint $table) {
        $table->char('numero_carte', 16)->change();
        $table->char('date_expiration', 5)->change();
    });
}
};
