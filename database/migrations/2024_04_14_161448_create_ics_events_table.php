<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('prompt');
            $table->text('timezone')->nullable()->default(null);
            $table->text('error')->nullable()->default(null);
            $table->text('ics')->nullable()->default(null);
            $table->integer('token_usage')->nullable()->default(null);
            $table->string('secret')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ics_events');
    }
};
