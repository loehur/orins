<?php if (empty($data['topup'])) { ?>
    <div class="pc-empty">Belum ada topup di tahun ini.</div>
<?php } else { ?>
    <table class="table table-sm text-sm" id="pcTopupTable">
        <tbody>
            <?php foreach ($data['topup'] as $a) {
                $st = (int)$a['st'];
                $tgl = trim($a['tanggal'] ?? '');
                $waktu = $tgl !== ''
                    ? date('d/m/y', strtotime($tgl))
                    : date('d/m/y H:i', strtotime($a['insertTime']));
                ?>
                <tr class="pc-row-topup">
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($waktu) ?></div>
                        <div class="pc-meta"><?= htmlspecialchars($a['ref']) ?></div>
                    </td>
                    <td class="text-end">
                        <div class="amt fw-semibold">
                            <?= number_format((int)$a['jumlah']) ?>
                            <?php if ($st === 1) { ?>
                                <i class="fa-solid fa-circle-check text-success ms-1" title="Verified"></i>
                            <?php } else { ?>
                                <i class="fa-solid fa-circle-xmark text-secondary ms-1" title="Status <?= $st ?>"></i>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
