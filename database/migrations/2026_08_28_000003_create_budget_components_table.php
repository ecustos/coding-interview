<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_components', function (Blueprint $table): void {
            $table->id();
            $table->string('description');
            $table->string('type');
            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->foreignId('composition_id')->nullable()->constrained('compositions')->cascadeOnDelete();
            $table->foreignId('input_id')->nullable()->constrained('inputs')->cascadeOnDelete();
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_components');
    }
};
