<?php if (empty($data['pakai'])) { ?>
    <div class="pc-empty">Belum ada pemakaian di bulan ini.</div>
<?php } else { ?>
    <table class="table table-sm text-sm" id="pcPakaiTable">
        <tbody>
            <?php foreach ($data['pakai'] as $a) {
                $st = (int)$a['st'];
                $jenis = isset($data['jkeluar'][$a['id_target']])
                    ? $data['jkeluar'][$a['id_target']]['nama']
                    : ('#' . $a['id_target']);
                $waktu = ($a['tanggal'] ?? '') === ''
                    ? date('d/m/y H:i', strtotime($a['insertTime']))
                    : $a['tanggal'];
                ?>
                <tr class="pc-row-pakai" data-id="<?= (int)$a['id'] ?>" id="tr<?= (int)$a['id'] ?>">
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($waktu) ?></div>
                        <div class="pc-note">
                            <i class="fa-regular fa-note-sticky"></i>
                            <?php if (!empty($data['can_ops']) && $st === 0 && (int)$a['tipe'] === 2) { ?>
                                <span class="cell_edit" data-id="<?= (int)$a['id'] ?>" data-col="note"><?= htmlspecialchars($a['note']) ?></span>
                            <?php } else { ?>
                                <?= htmlspecialchars($a['note']) ?>
                            <?php } ?>
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="pc-jenis"><?= htmlspecialchars($jenis) ?></div>
                        <div class="amt fw-semibold">
                            <?= number_format((int)$a['jumlah']) ?>
                            <?php if ($st === 0) { ?>
                                <i class="fa-solid fa-clock text-warning ms-1" title="Checking"></i>
                            <?php } elseif ($st === 1) { ?>
                                <i class="fa-solid fa-circle-check text-success ms-1" title="Verified"></i>
                            <?php } ?>
                            <?php if (!empty($data['can_ops']) && $st === 0 && (int)$a['tipe'] === 2) { ?>
                                <button type="button"
                                    class="btn btn-link btn-sm text-danger p-0 ms-2 pc-del-pakai"
                                    title="Hapus pemakaian"
                                    data-id="<?= (int)$a['id'] ?>"
                                    data-jumlah="<?= (int)$a['jumlah'] ?>"
                                    data-jenis="<?= htmlspecialchars($jenis, ENT_QUOTES) ?>"
                                    data-note="<?= htmlspecialchars($a['note'], ENT_QUOTES) ?>">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
