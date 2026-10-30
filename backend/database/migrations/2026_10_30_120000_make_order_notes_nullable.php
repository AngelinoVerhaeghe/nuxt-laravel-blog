<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notes are optional in the order form, so an order without notes couldn't be saved.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->longText('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->whereNull('notes')->update(['notes' => '']);

        Schema::table('orders', function (Blueprint $table) {
            $table->longText('notes')->nullable(false)->change();
        });
    }
};
