<?php if (empty($data['pakai'])) { ?>
    <div class="pcf-empty">Tidak ada pemakaian menunggu verify.</div>
<?php } else { ?>
    <table class="table table-sm text-sm" id="pcfPendingTable">
        <tbody>
            <?php foreach ($data['pakai'] as $a) {
                $jenis = isset($data['jkeluar'][$a['id_target']])
                    ? $data['jkeluar'][$a['id_target']]['nama']
                    : ('#' . $a['id_target']);
                $waktu = ($a['tanggal'] ?? '') === ''
                    ? date('d/m/y H:i', strtotime($a['insertTime']))
                    : $a['tanggal'];
                ?>
                <tr class="pcf-row-pend" data-id="<?= (int)$a['id'] ?>">
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($waktu) ?></div>
                        <?php if (!empty($a['note'])) { ?>
                            <div class="pcf-note"><i class="fa-regular fa-note-sticky"></i> <?= htmlspecialchars($a['note']) ?></div>
                        <?php } ?>
                    </td>
                    <td class="text-end">
                        <div>
                            <span class="text-danger fw-bold"><i class="fa-solid fa-arrow-right"></i></span>
                            <?= htmlspecialchars($jenis) ?>
                        </div>
                        <div class="amt fw-semibold"><?= number_format((int)$a['jumlah']) ?></div>
                    </td>
                    <td class="text-end" style="width:88px">
                        <a class="ajax-verify btn btn-sm btn-success bg-gradient py-0 px-2"
                           href="<?= PV::BASE_URL ?>Petty_Cash_F/verify/<?= (int)$a['id'] ?>/1">Verify</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
