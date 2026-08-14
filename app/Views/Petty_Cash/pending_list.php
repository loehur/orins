<?php if (empty($data['pending'])) { ?>
    <div class="pc-empty">Tidak ada topup menunggu verify.</div>
<?php } else { ?>
    <table class="table table-sm text-sm" id="pcPendingTable">
        <tbody>
            <?php foreach ($data['pending'] as $a) {
                $tgl = trim($a['tanggal'] ?? '');
                $waktu = $tgl !== ''
                    ? date('d/m/y', strtotime($tgl))
                    : date('d/m/y H:i', strtotime($a['insertTime']));
                ?>
                <tr class="pc-row-pend" data-id="<?= (int)$a['id'] ?>">
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($waktu) ?></div>
                        <div class="pc-meta"><?= htmlspecialchars($a['ref']) ?></div>
                    </td>
                    <td class="text-end">
                        <div class="amt fw-semibold"><?= number_format((int)$a['jumlah']) ?></div>
                    </td>
                    <td class="text-end" style="width:88px">
                        <?php if (!empty($data['can_ops'])) { ?>
                            <a class="ajax-verify btn btn-sm btn-success bg-gradient py-0 px-2"
                               href="<?= PV::BASE_URL ?>Petty_Cash/verify/<?= (int)$a['id'] ?>/1">Verify</a>
                        <?php } else { ?>
                            <span class="text-warning">Checking</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
