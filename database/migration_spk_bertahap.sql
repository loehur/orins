-- SPK Bertahap: pecah pengerjaan order_data berdasarkan qty
-- Jalankan di VPS (jangan di local).

CREATE TABLE IF NOT EXISTS `spk_bertahap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_order_data` int(11) NOT NULL COMMENT 'FK order_data induk',
  `tahap` int(11) NOT NULL DEFAULT 1 COMMENT 'Nomor tahap 1,2,3...',
  `qty` int(11) NOT NULL COMMENT 'Qty tahap ini',
  `spk_dvs` text NOT NULL COMMENT 'Copy struktur SPK induk, status independen',
  `spk_lanjutan` varchar(255) NOT NULL DEFAULT '',
  `id_user` int(11) NOT NULL DEFAULT 0,
  `insertTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_id_order_data` (`id_order_data`),
  KEY `idx_order_tahap` (`id_order_data`, `tahap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
