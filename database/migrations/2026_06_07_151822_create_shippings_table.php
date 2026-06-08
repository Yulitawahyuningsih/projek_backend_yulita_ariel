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
    Schema::create('shippings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->string('courier');
        $table->string('tracking_number')->nullable();
        $table->string('service')->nullable();
        $table->decimal('cost', 10, 2)->default(0);
        $table->date('estimated_arrival')->nullable();
        $table->enum('status', ['Pending', 'Dikirim', 'Sampai'])->default('Pending');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
