<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            // ربط الوظيفة بجدول الشركات (تأكدي إنو اسم جدول الشركات عندك companies)
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade'); 
            $table->string('title');            
            $table->string('location');         
            $table->string('salary');           
            $table->string('employment_type');   
            $table->text('description');        
            $table->timestamps();               
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};