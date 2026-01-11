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
     Schema::create('upload_chunks', function (Blueprint $table) {
    $table->id();
    $table->string('upload_id');
    $table->integer('chunk_index');
    $table->boolean('is_uploaded')->default(false);
    $table->timestamps();

    $table->unique(['upload_id', 'chunk_index']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_chunks');
    }
};
