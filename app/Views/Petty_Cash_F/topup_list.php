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
                        <div class="amt fw-semibold"><?= number_format((int)$a['jumlah']) ?></div>
                    </td>
                    <td class="text-end" style="width:88px">
                        <?php if ($st === 0) { ?>
                            <span class="text-warning">Checking</span>
                        <?php } elseif ($st === 1) { ?>
                            <span class="text-success">Verified</span>
                        <?php } else { ?>
                            <span class="text-secondary">—</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
