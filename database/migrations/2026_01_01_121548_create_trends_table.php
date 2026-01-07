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
    Schema::create('trends', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('category');
        $table->string('post_count');
        $table->text('summary');
        $table->json('news_links');
        $table->string('fetched_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trends');
    }
};
