<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update tour table status enum to only 'hoat_dong' and 'khong_hoat_dong'
        DB::statement("ALTER TABLE `tour` CHANGE `trangthai` `trangthai` ENUM('hoat_dong','khong_hoat_dong') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hoat_dong' COMMENT 'Trạng thái tour'");
        
        // Update existing records: set all non-matching values to 'hoat_dong'
        DB::table('tour')->whereNotIn('trangthai', ['hoat_dong', 'khong_hoat_dong'])->update(['trangthai' => 'hoat_dong']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE `tour` CHANGE `trangthai` `trangthai` ENUM('con_cho','het_cho','huy','khong_hoat_dong') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'con_cho' COMMENT 'Trạng thái tour'");
    }
};
