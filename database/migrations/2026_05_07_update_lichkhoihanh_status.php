<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('lichkhoihanh')
            ->where('trangthai', 'khong_hoat_dong')
            ->update(['trangthai' => 'huy']);

        DB::statement("ALTER TABLE `lichkhoihanh` CHANGE `trangthai` `trangthai` ENUM('con_cho','het_cho','sap_dien_ra','hoan_thanh','huy') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'con_cho' COMMENT 'Trạng thái'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `lichkhoihanh` CHANGE `trangthai` `trangthai` ENUM('con_cho','het_cho','huy','khong_hoat_dong') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'con_cho' COMMENT 'Trạng thái'");

        DB::table('lichkhoihanh')
            ->where('trangthai', 'huy')
            ->update(['trangthai' => 'khong_hoat_dong']);
    }
};