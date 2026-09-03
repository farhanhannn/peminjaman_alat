<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('available_stock')->default(0);
            $table->string('condition', 20)->default('baik');
            $table->decimal('daily_fine', 12, 2)->default(5000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void { Schema::dropIfExists('tools'); }
};
