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
        Schema::create('nisab_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('gold_price_per_gram', 15, 2);
            $table->decimal('silver_price_per_gram', 15, 2)->nullable();
            $table->decimal('nisab_value', 15, 2); // Calculated: gold_price_per_gram * 87.48
            $table->string('source')->nullable(); // Source of gold price
            $table->date('effective_from');
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nisab_settings');
    }
};
