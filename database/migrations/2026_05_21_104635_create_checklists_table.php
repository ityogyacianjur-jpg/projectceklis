<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->string('status')->nullable(); // 'Ya', 'Tidak', atau null
            $table->text('komentar')->nullable();
            // longText digunakan karena foto disimpan dalam format base64 seperti script aslinya
            $table->longText('foto')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};