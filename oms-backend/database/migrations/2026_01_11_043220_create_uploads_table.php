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
    Schema::create('uploads', function (Blueprint $table) {
        $table->id();

        // Public upload identifier (UUID)
        $table->string('upload_id')->unique();

        // Author / logged-in user
        $table->foreignId('created_by')
              ->constrained()
              ->cascadeOnDelete();

        // Related order
        $table->foreignId('order_id')
              ->constrained()
              ->cascadeOnDelete();

        // File info
        $table->string('file_name');
        $table->string('file_path'); // storage/app/uploads/...

        // Chunk info
        $table->integer('total_chunks');
        $table->integer('uploaded_chunks')->default(0);

        // Upload status
        $table->enum('status', ['pending', 'completed'])->default('pending');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
