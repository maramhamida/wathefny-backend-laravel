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
        Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('company_name');
        $table->string('company_email');
        $table->string('company_code');
        $table->string('company_address');
        $table->text('services')->nullable();
        $table->text('bio')->nullable();
        $table->string('accreditation_certificate')->nullable();
        $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
