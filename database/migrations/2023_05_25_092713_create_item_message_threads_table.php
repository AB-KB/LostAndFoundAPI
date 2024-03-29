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
        Schema::create('item_message_threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("item_id");
            $table->foreign("item_id")
                ->on("items")
                ->references("id");
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")
                ->on("users")
                ->references("id");
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
        Schema::dropIfExists('item_message_threads');
    }
};
