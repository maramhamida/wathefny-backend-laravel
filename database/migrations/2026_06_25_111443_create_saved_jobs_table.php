<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('saved_jobs', function (Blueprint $table) {
        $table->id();
        // هذه الأعمدة هي التي كانت مفقودة عندك
        $table->unsignedBigInteger('user_id'); 
        $table->unsignedBigInteger('job_id');
        
        // ربط العلاقات (Foreign Keys)
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('job_id')->references('id')->on('job_posts')->onDelete('cascade');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
    }
};
