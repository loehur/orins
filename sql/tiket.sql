-- ============================================================
-- Fitur Tiket — jalankan di database Orins (copy-paste)
-- ============================================================

CREATE TABLE IF NOT EXISTS `tiket` (
  `id_tiket` int(11) NOT NULL AUTO_INCREMENT,
  `id_karyawan` int(11) NOT NULL DEFAULT 0,
  `id_user` int(11) NOT NULL DEFAULT 0 COMMENT 'user pembuat tiket',
  `id_toko` int(11) NOT NULL DEFAULT 0,
  `judul` varchar(255) NOT NULL DEFAULT '',
  `tipe` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Perbaikan, 2=Fitur Baru, 3=Usulan',
  `isi` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Proses, 1=Selesai',
  `selesai_oleh` int(11) NOT NULL DEFAULT 0,
  `insertTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `selesai_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id_tiket`),
  KEY `idx_tiket_status` (`status`),
  KEY `idx_tiket_toko` (`id_toko`),
  KEY `idx_tiket_user` (`id_user`),
  KEY `idx_tiket_selesai` (`selesai_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tiket_reply` (
  `id_reply` int(11) NOT NULL AUTO_INCREMENT,
  `id_tiket` int(11) NOT NULL DEFAULT 0,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `isi` text NOT NULL,
  `insertTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reply`),
  KEY `idx_reply_tiket` (`id_tiket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
