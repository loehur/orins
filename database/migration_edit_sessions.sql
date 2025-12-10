-- Migration for Edit Sessions System
-- This table stores snapshots of orders when entering edit mode
-- Allows complete rollback functionality

CREATE TABLE IF NOT EXISTS `edit_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_key` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ref` varchar(50) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `jenis_pelanggan` int(11) NOT NULL,
  `dibayar` decimal(15,2) NOT NULL DEFAULT 0,
  `snapshot_data` longtext NOT NULL COMMENT 'JSON snapshot of order_data',
  `snapshot_mutasi` longtext NOT NULL COMMENT 'JSON snapshot of master_mutasi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','committed','cancelled') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_key` (`session_key`),
  KEY `user_id` (`user_id`),
  KEY `ref` (`ref`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clean up old sessions (older than 24 hours)
CREATE EVENT IF NOT EXISTS cleanup_old_edit_sessions
ON SCHEDULE EVERY 1 DAY
DO
  DELETE FROM edit_sessions
  WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
  AND status != 'active';
