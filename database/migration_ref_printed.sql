-- Tracking cetak order di tabel ref
-- printed: jumlah kali order dicetak (0 = belum pernah, 1 = cetak pertama, 2+ = cetak ulang)
-- reprint_reason: log alasan cetak ulang (ke-2 dan seterusnya)

ALTER TABLE `ref`
  ADD COLUMN `printed` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `tuntas_date`,
  ADD COLUMN `reprint_reason` TEXT NULL AFTER `printed`;
