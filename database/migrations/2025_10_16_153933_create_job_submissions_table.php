<?php

use App\Enums\JobState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('ip', 64)->index();
            $table->json('parameters')->nullable();
            $table->string('slurm_id')->index()->nullable();
            $table
                ->enum('status', JobState::values())
                ->default(JobState::Started->value)
                ->index();
            $table->text('stdout')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_submissions');
    }
};
