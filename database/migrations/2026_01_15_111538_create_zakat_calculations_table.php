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
        Schema::create('zakat_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('zakat_year_id')->constrained('zakat_years')->onDelete('cascade');
            $table->decimal('total_assets', 15, 2)->default(0);
            $table->decimal('total_debts', 15, 2)->default(0);
            $table->decimal('net_zakatable_wealth', 15, 2)->default(0);
            $table->decimal('nisab', 15, 2)->default(0);
            $table->decimal('zakat_due', 15, 2)->default(0);
            $table->decimal('zakat_paid', 15, 2)->default(0);
            $table->decimal('zakat_remaining', 15, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'zakat_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zakat_calculations');
    }
};
