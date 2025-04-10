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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., 'update'
            $table->string('model_type'); // e.g., 'Contact'
            $table->unsignedBigInteger('model_id'); // e.g., contact ID
            $table->text('changed_data')->nullable(); // JSON of changed fields
            $table->string('performed_by'); // admin name
            $table->timestamp('performed_at'); // date and time
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
        Schema::dropIfExists('activity_logs');
    }
};
