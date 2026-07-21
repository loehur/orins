<?php
$fmt = function ($n) {
    return 'Rp' . number_format((int) $n);
};
$yn = function ($ok) {
    return $ok
        ? '<span class="badge bg-success">OK</span>'
        : '<span class="badge bg-danger">BELUM</span>';
};
$ref = htmlspecialchars($data['ref'] ?? '');
$dRef = $data['dRef'] ?? [];
?>
<style>
    .analisa-section { margin-bottom: 1rem; }
    .analisa-section h6 {
        font-size: 12px;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: .4rem;
    }
    .analisa-kv td { padding: .15rem .4rem; vertical-align: top; }
    .analisa-kv td:first-child { color: #6c757d; white-space: nowrap; width: 140px; }
    .analisa-flag {
        border-left: 3px solid #dc3545;
        background: #fff5f5;
        padding: .5rem .75rem;
        margin-bottom: .35rem;
        border-radius: .2rem;
    }
    .analisa-flag.ok {
        border-left-color: #198754;
        background: #f1f9f4;
    }
    .analisa-flag.warn {
        border-left-color: #fd7e14;
        background: #fff8f0;
    }
    .analisa-flag.info {
        border-left-color: #0d6efd;
        background: #f0f6ff;
    }
    .analisa-table th, .analisa-table td {
        padding: .25rem .4rem;
        font-size: 12px;
    }
</style>

<div class="analisa-section">
    <h6>Ringkasan Diagnosa</h6>
    <?php foreach (($data['flags'] ?? []) as $flag) {
        $level = $flag['level'] ?? 'info';
        $cls = $level === 'ok' ? 'ok' : ($level === 'warn' ? 'warn' : ($level === 'error' ? '' : 'info'));
        ?>
        <div class="analisa-flag <?= $cls ?>"><?= htmlspecialchars($flag['text'] ?? '') ?></div>
    <?php } ?>
</div>

<?php if (!empty($data['system_checks'])) { ?>
<div class="analisa-section">
    <h6>Sistem / Cron</h6>
    <?php foreach ($data['system_checks'] as $sc) {
        $level = $sc['level'] ?? 'info';
        $cls = $level === 'ok' ? 'ok' : ($level === 'warn' ? 'warn' : ($level === 'error' ? '' : 'info'));
        ?>
        <div class="analisa-flag <?= $cls ?>">
            <div class="fw-bold"><?= htmlspecialchars($sc['title'] ?? '') ?></div>
            <div><?= htmlspecialchars($sc['text'] ?? '') ?></div>
            <?php if (!empty($sc['fix'])) { ?>
                <div class="mt-1"><small><b>Perbaikan:</b> <?= htmlspecialchars($sc['fix']) ?></small></div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<?php } ?>

<div class="analisa-section">
    <h6>Identitas Nota</h6>
    <table class="analisa-kv w-100">
        <tr><td>Ref</td><td><b><?= $ref ?></b></td></tr>
        <tr><td>Dibuat</td><td><?= !empty($data['insertTime']) ? date('d/m/Y H:i:s', strtotime($data['insertTime'])) : '-' ?></td></tr>
        <tr><td>Toko</td><td><?= htmlspecialchars($data['toko']) ?></td></tr>
        <tr><td>Pelanggan</td><td><?= htmlspecialchars($data['pelanggan']) ?></td></tr>
        <tr><td>CS</td><td><?= htmlspecialchars($data['cs']) ?></td></tr>
        <tr><td>Creator</td><td><?= htmlspecialchars($data['creator']) ?></td></tr>
        <tr><td>Afiliasi</td><td><?= htmlspecialchars($data['afiliasi']) ?></td></tr>
        <tr><td>CS Afiliasi</td><td><?= htmlspecialchars($data['cs_aff']) ?></td></tr>
        <tr><td>Mark</td><td><?= htmlspecialchars(strtoupper($dRef['mark'] ?? '-')) ?></td></tr>
        <tr><td>Printed</td><td><?= (int)($dRef['printed'] ?? 0) ?></td></tr>
        <tr><td>Tuntas Induk</td><td><?= (int)$data['tuntas_induk'] === 1 ? 'Ya' . (!empty($dRef['tuntas_date']) ? ' (' . $dRef['tuntas_date'] . ')' : '') : 'Belum' ?></td></tr>
        <tr><td>Ready CS</td><td><?= (int)($dRef['ready_cs'] ?? 0) ?: '-' ?></td></tr>
        <tr><td>Expedisi</td><td><?= (int)($dRef['expedisi'] ?? 0) ?: '-' ?></td></tr>
    </table>
</div>

<div class="analisa-section">
    <h6>Keuangan</h6>
    <table class="analisa-kv w-100 mb-2">
        <tr><td>Tagihan (Bill)</td><td><b><?= $fmt($data['bill']) ?></b></td></tr>
        <tr><td>Dibayar (UI)</td><td><?= $fmt($data['dibayar_ui']) ?> <small class="text-muted">(status checking + OK)</small></td></tr>
        <tr><td>Sisa (UI)</td><td><b class="<?= $data['sisa_ui'] > 0 ? 'text-danger' : 'text-success' ?>"><?= $fmt($data['sisa_ui']) ?></b></td></tr>
        <tr><td>Verify Payment</td><td><?= $fmt($data['verify_payment']) ?> <small class="text-muted">(syarat cron tuntas)</small></td></tr>
        <tr><td>Sisa Verify</td><td><b class="<?= $data['sisa_verify'] == 0 ? 'text-success' : 'text-warning' ?>"><?= $fmt($data['sisa_verify']) ?></b> <?= $yn($data['payment_ok']) ?></td></tr>
        <tr><td>Xtra Diskon</td><td><?= $fmt($data['diskon_total']) ?></td></tr>
        <tr><td>Refund</td><td><?= $fmt($data['refund_total']) ?></td></tr>
        <tr><td>Ambil Semua</td><td><?= $yn($data['ambil_all']) ?></td></tr>
        <tr><td>Kas Kecil</td><td><?= $yn($data['verify_kas_kecil']) ?></td></tr>
        <tr><td>Siap Tuntas</td><td><?= $yn($data['ready_to_tuntas']) ?></td></tr>
    </table>

    <?php if (count($data['kas_lines']) > 0) { ?>
        <div class="fw-bold mb-1">Pembayaran / Refund</div>
        <table class="table table-sm table-bordered analisa-table mb-2">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Jenis</th>
                    <th>Metode</th>
                    <th class="text-end">Jumlah</th>
                    <th>Status</th>
                    <th>Setor</th>
                    <th>Verify</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['kas_lines'] as $k) { ?>
                    <tr>
                        <td><?= (int)$k['id'] ?></td>
                        <td><?= htmlspecialchars($k['jenis']) ?></td>
                        <td><?= htmlspecialchars($k['metode']) ?></td>
                        <td class="text-end"><?= $fmt($k['jumlah']) ?></td>
                        <td><?= htmlspecialchars($k['status']) ?></td>
                        <td><?= (int)$k['status_setoran'] === 1 ? 'Ya' : 'Tidak' ?></td>
                        <td><?= !empty($k['counts_verify']) ? '✓' : '-' ?></td>
                        <td><?= !empty($k['insertTime']) ? date('d/m/y H:i', strtotime($k['insertTime'])) : '-' ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="text-muted small mb-2">Belum ada pembayaran.</div>
    <?php } ?>

    <?php if (count($data['charge_lines']) > 0) { ?>
        <div class="fw-bold mb-1">Surcharge</div>
        <ul class="small mb-2">
            <?php foreach ($data['charge_lines'] as $c) { ?>
                <li>#<?= (int)$c['id'] ?> <?= htmlspecialchars($c['note'] ?? '') ?> — <?= $fmt($c['jumlah']) ?>
                    <?= (int)($c['cancel'] ?? 0) === 1 ? '<span class="text-danger">CANCEL</span>' : '' ?>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <?php if (count($data['diskon_lines']) > 0) { ?>
        <div class="fw-bold mb-1">Extra Diskon</div>
        <ul class="small mb-2">
            <?php foreach ($data['diskon_lines'] as $d) { ?>
                <li>#<?= (int)($d['id'] ?? 0) ?> — <?= $fmt($d['jumlah'] ?? 0) ?>
                    <?= (int)($d['cancel'] ?? 0) === 1 ? '<span class="text-danger">CANCEL</span>' : '' ?>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>

<div class="analisa-section">
    <h6>Item Produksi (Order)</h6>
    <?php if (count($data['order_lines']) === 0) { ?>
        <div class="text-muted small">Tidak ada item produksi.</div>
    <?php } else { ?>
        <table class="table table-sm table-bordered analisa-table">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th class="text-end">Nilai</th>
                    <th>Ambil</th>
                    <th>SPK</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['order_lines'] as $o) { ?>
                    <tr class="<?= $o['cancel'] ? 'table-secondary' : '' ?>">
                        <td><?= $o['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($o['produk']) ?>
                            <?php if ($o['note'] !== '') { ?><div class="text-muted"><?= htmlspecialchars($o['note']) ?></div><?php } ?>
                        </td>
                        <td><?= $o['jumlah'] ?></td>
                        <td class="text-end"><?= $fmt($o['line_bill']) ?></td>
                        <td><?= $o['id_ambil'] > 0 ? 'Ya #' . $o['id_ambil'] : '<span class="text-warning">Belum</span>' ?></td>
                        <td>
                            <?php if (count($o['spk']) === 0) { ?>
                                -
                            <?php } else { ?>
                                <?php foreach ($o['spk'] as $s) { ?>
                                    <div><?= htmlspecialchars($s['divisi']) ?>:
                                        <?= $s['done'] ? '<span class="text-success">done</span>' : '<span class="text-danger">pending</span>' ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </td>
                        <td>
                            <?= $o['cancel'] ? '<span class="text-danger">Cancel</span>' : 'Aktif' ?>
                            <?= $o['tuntas'] ? ' / Tuntas' : '' ?>
                            <?= $o['stok'] ? ' / Stok' : '' ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

<div class="analisa-section">
    <h6>Item Barang (Mutasi)</h6>
    <?php if (count($data['mutasi_lines']) === 0) { ?>
        <div class="text-muted small">Tidak ada item barang.</div>
    <?php } else { ?>
        <table class="table table-sm table-bordered analisa-table">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th>SN</th>
                    <th class="text-end">Nilai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['mutasi_lines'] as $m) { ?>
                    <tr class="<?= $m['stat'] === 2 ? 'table-secondary' : '' ?>">
                        <td><?= $m['id'] ?></td>
                        <td><?= htmlspecialchars($m['barang']) ?></td>
                        <td><?= $m['qty'] ?></td>
                        <td><?= htmlspecialchars($m['sn'] !== '' ? $m['sn'] : '-') ?></td>
                        <td class="text-end"><?= $fmt($m['line_bill']) ?></td>
                        <td>
                            <?= $m['stat'] === 2 ? '<span class="text-danger">Cancel</span>' : 'Aktif' ?>
                            <?= $m['tuntas'] ? ' / Tuntas' : '' ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

<?php if (count($data['kas_kecil']) > 0) { ?>
<div class="analisa-section">
    <h6>Kas Kecil</h6>
    <ul class="small mb-0">
        <?php foreach ($data['kas_kecil'] as $kk) { ?>
            <li>#<?= (int)($kk['id'] ?? 0) ?> st=<?= (int)($kk['st'] ?? 0) ?> jumlah=<?= $fmt($kk['jumlah'] ?? 0) ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<div class="text-muted small border-top pt-2">
    Analisa ini read-only. Digunakan untuk menelusuri kenapa nota belum tuntas / anomali pembayaran.
</div>
