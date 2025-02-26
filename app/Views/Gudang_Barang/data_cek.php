<div class="p-2">
    <table class="table table-sm mb-0 text-sm">
        <?php foreach ($data['stok'] as $key => $d) {
            if ($d['qty'] == 0) {
                continue;
            } ?>
            <tr>
                <td><?= $d['sds'] == 1 ? "SDS" : "ABF" ?></td>
                <td class="text-end"><?= $d['sn'] ?></td>
            </tr>
        <?php } ?>
    </table>
</div>