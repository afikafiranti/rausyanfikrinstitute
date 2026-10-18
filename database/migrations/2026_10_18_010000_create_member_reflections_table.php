<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah')->nullOnDelete();
            $table->string('title', 180);
            $table->string('context', 20);
            $table->string('activity_name', 180)->nullable();
            $table->mediumText('body');
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['wilayah_id', 'status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_reflections');
    }
};
