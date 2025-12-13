-- Script untuk memperbaiki price_locker di paket_mutasi yang sudah ada
-- Jalankan script ini sekali saja untuk memperbaiki data existing

-- Step 1: Reset semua price_locker di paket_mutasi menjadi 0
UPDATE paket_mutasi SET price_locker = 0;

-- Step 2: Reset semua price_locker di paket_order menjadi 0
UPDATE paket_order SET price_locker = 0;

-- Step 3: Set price_locker = 1 untuk item pertama di setiap paket_ref (dari paket_mutasi)
UPDATE paket_mutasi pm1
INNER JOIN (
    SELECT paket_ref, MIN(id) as min_id
    FROM paket_mutasi
    WHERE paket_ref != ''
    GROUP BY paket_ref
) pm2 ON pm1.id = pm2.min_id
SET pm1.price_locker = 1;

-- Step 4: Set price_locker = 1 untuk item pertama di paket_order (hanya jika tidak ada di paket_mutasi)
UPDATE paket_order po1
INNER JOIN (
    SELECT paket_ref, MIN(id_order_data) as min_id
    FROM paket_order
    WHERE paket_ref != ''
    AND paket_ref NOT IN (SELECT DISTINCT paket_ref FROM paket_mutasi WHERE price_locker = 1)
    GROUP BY paket_ref
) po2 ON po1.id_order_data = po2.min_id
SET po1.price_locker = 1;

-- Verifikasi hasil
SELECT 'paket_mutasi dengan price_locker=1:' as info, COUNT(*) as jumlah FROM paket_mutasi WHERE price_locker = 1
UNION ALL
SELECT 'paket_order dengan price_locker=1:' as info, COUNT(*) as jumlah FROM paket_order WHERE price_locker = 1;
