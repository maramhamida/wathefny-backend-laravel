<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل الحالة وافتراضياً يكون 'pending' (قيد الانتظار)
            // اللغات المتاحة سنعتمدها كـ: 'pending', 'approved', 'rejected'
            $table->string('status')->default('pending')->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};