# Sistem Edit Order - Dokumentasi

## Ringkasan Perubahan

Sistem edit order telah diperbarui dari sistem **localStorage client-side** menjadi **snapshot server-side** yang lebih reliable dan robust.

## Cara Kerja Sistem Baru

### 1. Saat Masuk Edit Mode (Edit_order)

Ketika user klik Edit dari Data_Operasi:

```
User Click Edit → Edit_order() dipanggil
  ↓
1. Buat snapshot semua data order_data & master_mutasi (JSON)
2. Simpan snapshot ke tabel edit_sessions dengan status 'active'
3. Generate session_key unik: 'edit_{user_id}_{ref}_{timestamp}'
4. Simpan session_key ke $_SESSION['edit']
5. Clean up temporary items (ref = '')
6. Tampilkan halaman Buka_Order dalam mode edit
```

### 2. Saat User Melakukan Perubahan

Semua perubahan (delete item, update qty, update paket qty) langsung dikirim ke database melalui AJAX:

```
User Double Click Qty → Edit inline → Focusout
  ↓
AJAX: Buka_Order/updateCell_N
  ↓
Update database langsung
  ↓
Reload content dengan data terbaru
```

**TIDAK ADA** localStorage yang digunakan. Semua perubahan real-time ke database.

### 3. Saat User Klik Cancel

```
User Click Cancel → Konfirmasi modal
  ↓
AJAX: Buka_Order/cancel_edit
  ↓
1. Ambil snapshot dari edit_sessions
2. Delete semua item dengan ref tersebut
3. Restore item dari snapshot (INSERT)
4. Mark session sebagai 'cancelled'
5. Clear $_SESSION['edit']
6. Reload halaman
```

**Hasil:** Order kembali ke kondisi persis seperti saat pertama kali masuk edit mode.

### 4. Saat User Klik Update

```
User Click Update → Loading indicator
  ↓
AJAX: Buka_Order/commit_edit_session
  ↓
1. Mark session sebagai 'committed'
2. Clear $_SESSION['edit']
  ↓
AJAX: Buka_Order/proses (normal update flow)
  ↓
Redirect ke Data_Operasi
```

**Hasil:** Perubahan yang sudah dilakukan di database menjadi final.

## Database Schema

### Tabel: edit_sessions

```sql
CREATE TABLE `edit_sessions` (
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
```

## Keuntungan Sistem Baru

### 1. **Data Consistency**
- Tidak ada mismatch antara localStorage dan database
- Semua perubahan langsung tersimpan di database
- Refresh halaman tidak akan hilangkan data

### 2. **Reliable Cancel**
- Cancel benar-benar restore ke kondisi awal
- Menggunakan snapshot yang disimpan di server
- Tidak bergantung pada browser storage

### 3. **Simpler Code**
- Tidak perlu tracking changes di client-side
- Tidak perlu sync localStorage dengan database
- Lebih mudah di-debug

### 4. **Better UX**
- User bisa lihat perubahan langsung (real-time)
- Tidak perlu menunggu sampai klik Update
- Loading indicator yang jelas
- Modal konfirmasi yang informatif

### 5. **Multi-device Compatible**
- Tidak bergantung pada localStorage browser
- Session tersimpan di server
- Bisa lanjutkan edit dari device berbeda (dengan session yang sama)

## File yang Diubah

### 1. Database Migration
- **File:** `database/migration_edit_sessions.sql`
- **Isi:** CREATE TABLE edit_sessions + EVENT cleanup

### 2. Controller
- **File:** `app/Controllers/Buka_Order.php`
- **Fungsi Baru:**
  - `Edit_order()` - Updated dengan snapshot system
  - `cancel_edit()` - Restore dari snapshot
  - `commit_edit_session()` - Finalize changes
- **Fungsi Existing:** Tidak diubah (updateCell_N, update_paket_qty, dll tetap sama)

### 3. View
- **File:** `app/Views/Buka_Order/content.php`
- **Perubahan:**
  - Hapus semua localStorage logic (150+ baris)
  - Ganti dengan simple Cancel & Update handler (60 baris)
  - Tombol Cancel tetap ada di line 89
  - JavaScript lebih simple dan clean

## Cara Install

### 1. Run Database Migration

**PENTING:** Jalankan SQL migration ini terlebih dahulu sebelum menggunakan fitur edit!

```bash
mysql -u username -p database_name < database/migration_edit_sessions.sql
```

Atau copy-paste SQL dari file `database/migration_edit_sessions.sql` dan jalankan di phpMyAdmin atau MySQL client Anda.

### 2. Test Flow

1. Masuk ke Data_Operasi
2. Klik Edit pada salah satu order
3. Lakukan perubahan (delete item, edit qty, tambah item)
4. Klik Cancel → Cek apakah order kembali ke kondisi awal
5. Lakukan perubahan lagi
6. Klik Update → Cek apakah order berhasil diupdate

### 3. Monitor

Cek tabel `edit_sessions` untuk melihat history edit:

```sql
-- Lihat active sessions
SELECT * FROM edit_sessions WHERE status = 'active';

-- Lihat history
SELECT session_key, user_id, ref, status, created_at
FROM edit_sessions
ORDER BY created_at DESC
LIMIT 20;
```

## Troubleshooting

### Problem: "Edit session not found"

**Penyebab:** Session di database sudah expired atau dihapus

**Solusi:**
```sql
-- Cek apakah session masih ada
SELECT * FROM edit_sessions WHERE session_key LIKE 'edit_%' AND status = 'active';
```

Jika tidak ada, user harus keluar dari edit mode dan masuk lagi.

### Problem: Cancel tidak restore semua data

**Penyebab:** Ada foreign key constraint atau trigger yang mencegah INSERT

**Solusi:**
1. Cek error log di browser console
2. Cek error di `cancel_edit()` response
3. Pastikan snapshot_data berisi semua field yang diperlukan

### Problem: Data hilang setelah refresh

**Penyebab:** Mungkin masih ada kode lama yang menggunakan localStorage

**Solusi:**
1. Clear localStorage browser: `localStorage.clear()`
2. Pastikan tidak ada kode localStorage di content.php
3. Hard refresh browser (Ctrl+Shift+R)

## Maintenance

### Clean Up Old Sessions

Event MySQL sudah dibuat untuk auto-cleanup sessions > 24 jam:

```sql
-- Manual cleanup jika diperlukan
DELETE FROM edit_sessions
WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
AND status != 'active';
```

### Monitor Session Size

```sql
-- Cek ukuran snapshot (dalam bytes)
SELECT
    session_key,
    LENGTH(snapshot_data) as data_size,
    LENGTH(snapshot_mutasi) as mutasi_size,
    (LENGTH(snapshot_data) + LENGTH(snapshot_mutasi)) as total_size
FROM edit_sessions
ORDER BY total_size DESC
LIMIT 10;
```

## Best Practices

1. **Selalu test Cancel sebelum Update**
   - Pastikan Cancel bekerja dengan benar
   - Cek apakah semua item ter-restore

2. **Jangan edit terlalu lama**
   - Snapshot bisa jadi outdated jika edit > 1 jam
   - Sistem akan auto-cancel session > 24 jam

3. **Handle concurrent edits**
   - Sistem akan cancel session lama saat create session baru
   - User hanya bisa punya 1 active edit session

4. **Monitor database size**
   - Snapshot bisa besar jika order punya banyak item
   - Event cleanup akan delete old sessions otomatis

## Perbedaan dengan Sistem Lama

| Aspek | Sistem Lama (localStorage) | Sistem Baru (snapshot) |
|-------|---------------------------|------------------------|
| Penyimpanan | Browser localStorage | Database server |
| Cancel | Reload page (data hilang) | Restore dari snapshot |
| Reliability | Bisa hilang jika clear cache | Persistent di server |
| Multi-device | Tidak support | Support (dengan session) |
| Complexity | High (sync logic) | Low (direct AJAX) |
| Lines of code | ~250 lines | ~60 lines |
| Performance | Fast (client-side) | Good (server AJAX) |

## Kesimpulan

Sistem edit baru menggunakan **snapshot server-side** yang lebih reliable, simple, dan mudah di-maintain. User experience lebih baik dengan Cancel yang benar-benar restore ke kondisi awal, dan tidak ada lagi masalah dengan localStorage yang hilang atau tidak sync.
