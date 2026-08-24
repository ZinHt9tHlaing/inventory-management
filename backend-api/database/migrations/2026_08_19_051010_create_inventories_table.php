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
        Schema::create('inventories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid("product_id")->constrained("products")->cascadeOnDelete();
            $table->foreignUlid("warehouse_id")->constrained("warehouses")->cascadeOnDelete();
            $table->integer("quantity")->default(0);
            $table->timestamps();

            // Prevent duplicate product entries for the same warehouse
            // $table->unique(['product_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
