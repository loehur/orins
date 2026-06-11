<?php if (count($data['riwayat']) === 0) { ?>
    <div class="text-muted small py-2">Tidak ada riwayat pakai untuk periode ini.</div>
<?php } else { ?>
    <div class="riwayat-pakai-scroll">
        <table class="table table-sm table-hover text-sm mb-0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Tujuan</th>
                    <th>Karyawan</th>
                    <th>SN</th>
                    <th class="text-end">Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['riwayat'] as $r) {
                    $db = $data['barang'][$r['id_barang']] ?? null;
                    $nama_barang = $db
                        ? strtoupper(trim($db['brand'] . ' ' . $db['model']) . $db['product_name'])
                        : '#' . $r['id_barang'];
                    $tujuan = isset($data['akun_pakai'][$r['id_target']]['nama'])
                        ? ucwords($data['akun_pakai'][$r['id_target']]['nama'])
                        : '-';
                    $karyawan = isset($data['karyawan'][$r['cs_id']]['nama'])
                        ? ucwords($data['karyawan'][$r['cs_id']]['nama'])
                        : '-';
                    $sn = strlen($r['sn'] ?? '') > 0 ? $r['sn'] : '-';
                    $sds = (int)($r['sds'] ?? 0) === 1 ? 'SDS' : 'ABF';
                ?>
                    <tr>
                        <td class="text-nowrap"><?= date('d/m/y H:i', strtotime($r['insertTime'])) ?></td>
                        <td><?= $nama_barang ?></td>
                        <td><?= $tujuan ?></td>
                        <td><?= $karyawan ?></td>
                        <td><small><?= $sds ?></small> <?= $sn ?></td>
                        <td class="text-end"><?= number_format((int)$r['qty']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
