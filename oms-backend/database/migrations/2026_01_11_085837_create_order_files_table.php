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
    Schema::create('order_files', function (Blueprint $table) {
        $table->id();

        // Relation to orders
        $table->foreignId('order_id')
              ->constrained()
              ->cascadeOnDelete();

        // File info
        $table->string('file_name');
        $table->string('file_path'); // relative storage path
        $table->string('mime_type')->nullable();

        // Who uploaded the file
        $table->foreignId('uploaded_by')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_files');
    }
};
