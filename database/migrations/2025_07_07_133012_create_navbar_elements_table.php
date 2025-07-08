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
        Schema::create('navbar_elements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('value')->nullable();
            $table->integer('position')->default(0);
            $table->enum('type', [
                'home', 'module', 'external_link', 'page', 'posts', 'posts_list', 'dropdown'
            ]);
            $table->foreignId('parent_id')->nullable()->constrained('navbar_elements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navbar_elements');
    }
};
