-- ============================================================
-- Samakan collation kolom `ref` (fix Illegal mix of collations)
-- Backup DB dulu sebelum jalankan.
-- Target: utf8mb4_unicode_ci
-- ============================================================

-- 1) Cek collation saat ini
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, CHARACTER_SET_NAME, COLLATION_NAME, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'ref'
  AND TABLE_NAME IN ('ref', 'order_data', 'master_mutasi');

-- 2) Samakan collation (sesuaikan COLUMN_TYPE jika berbeda dari hasil cek di atas)
-- Contoh umum VARCHAR(20) — ganti tipe jika cek menunjukkan lain.

ALTER TABLE `ref` MODIFY `ref` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `order_data` MODIFY `ref` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `master_mutasi` MODIFY `ref` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- 3) Verifikasi ulang (harus semua utf8mb4_unicode_ci)
SELECT TABLE_NAME, COLLATION_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'ref'
  AND TABLE_NAME IN ('ref', 'order_data', 'master_mutasi');
