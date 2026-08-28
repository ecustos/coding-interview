<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table): void {
            $table->id();
            $table->string('description');
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stages', function (Blueprint $table): void {
            $table->id();
            $table->string('description');
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('compositions', function (Blueprint $table): void {
            $table->id();
            $table->string('description');
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('inputs', function (Blueprint $table): void {
            $table->id();
            $table->string('description');
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('budget_components', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary();
            $table->string('description');
            $table->string('type');
            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->foreignId('composition_id')->nullable()->constrained('compositions')->cascadeOnDelete();
            $table->foreignId('input_id')->nullable()->constrained('inputs')->cascadeOnDelete();
            $table->foreignId('parent_stage_id')->nullable()->constrained('stages')->nullOnDelete();
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_components');
        Schema::dropIfExists('inputs');
        Schema::dropIfExists('compositions');
        Schema::dropIfExists('stages');
        Schema::dropIfExists('budgets');
    }
};
