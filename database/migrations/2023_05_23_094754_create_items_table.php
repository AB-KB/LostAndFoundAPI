<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->enum("type", ["found", "lost"]);
            $table->enum("status", ["pending", "processed"])->default("pending");
            $table->unsignedBigInteger("cell_id");
            $table->foreign("cell_id")
                ->on("cells")
                ->references("id");
            $table->unsignedBigInteger("category_id");
            $table->foreign("category_id")
                ->on("categories")
                ->references("id");
            $table->unsignedBigInteger("added_by");
            $table->foreign("added_by")
                ->on("users")
                ->references("id");
            $table->json("additional_info")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
