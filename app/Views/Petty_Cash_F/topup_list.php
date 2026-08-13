<?php if (empty($data['topup'])) { ?>
    <div class="pcf-empty">Belum ada topup.</div>
<?php } else { ?>
    <table class="table table-sm text-sm">
        <tbody>
            <?php foreach ($data['topup'] as $a) {
                $st = (int)$a['st'];
                ?>
                <tr class="pcf-row-topup">
                    <td>
                        <div class="fw-semibold"><?= date('d/m/y H:i', strtotime($a['insertTime'])) ?></div>
                        <div class="pcf-meta"><?= htmlspecialchars($a['ref']) ?></div>
                    </td>
                    <td class="text-end">
                        <div class="amt fw-semibold">
                            <?= number_format((int)$a['jumlah']) ?>
                            <?php if ($st === 0) { ?>
                                <i class="fa-solid fa-clock text-warning ms-1" title="Checking"></i>
                            <?php } elseif ($st === 1) { ?>
                                <i class="fa-solid fa-circle-check text-success ms-1" title="Verified"></i>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
