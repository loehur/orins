<div class="p-2">
    <label class="border border-bottom-0 bg-success text-white px-2">Toko</label>
    <div class="border px-1 py-2 text-sm">
        <?php if (count($data['stok']) == 0) { ?>
            <div class="w-100 text-center text-sm">Kosong</div>
        <?php } else { ?>
            <table class="table table-sm mb-0 text-sm">
                <?php foreach ($data['stok'] as $key => $d) {
                    if ($d['qty'] == 0) {
                        continue;
                    } ?>
                    <tr>
                        <td><?= $d['sds'] == 1 ? "SDS" : "" ?></td>
                        <td class="text-end"><?= $d['sn'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
    <br>
    <label class="border border-bottom-0 bg-danger text-white px-2">Gudang</label>
    <div class="border px-1 py-2">
        <?php if (count($data['stok']) == 0) { ?>

        <?php } else { ?>

        <?php } ?>
        <table class="table table-sm mb-0 text-sm">
            <?php foreach ($data['stok_gudang'] as $key => $d) {
                if ($d['qty'] == 0) {
                    continue;
                } ?>
                <tr>
                    <td><?= $d['sds'] == 1 ? "SDS" : "" ?></td>
                    <td class="text-end"><?= $d['sn'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>