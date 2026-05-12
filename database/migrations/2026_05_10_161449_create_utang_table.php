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
        Schema::create('utang', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('person_id');
            $table->string('item')->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('price',10,2)->default(0);
            $table->decimal('total',10,2)->default(0);
            $table->string('status')->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utang');
    }
};
