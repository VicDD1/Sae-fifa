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
        $table->string('cryptogramme', 3)->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('carte_bancaire', function (Blueprint $table) {
        $table->string('cryptogramme', 3)->nullable(false)->change();
    });
}
};
