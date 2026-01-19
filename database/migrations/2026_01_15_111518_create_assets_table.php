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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('zakat_year_id')->nullable()->constrained('zakat_years')->onDelete('cascade');
            $table->string('type'); // zakatable or non_zakatable
            $table->string('category'); // cash, bank, gold, silver, business_inventory, money_owed, crypto, investments, house, car, furniture, clothes, phone, laptop, work_tools
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('quantity', 15, 4)->nullable(); // for gold/silver in grams
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
