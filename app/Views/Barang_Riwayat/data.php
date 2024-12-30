    <table class="table table-sm">
        <?php foreach ($data['mutasi'] as $d) {
            $dp = $data['barang'] ?>
            <tr>
                <td class=""><?= date('d/m/y H:i', strtotime($d['insertTime'])) ?></td>
                <td><?= $d['ref'] ?></td>
                <td><?= $d['sn'] ?></td>
                <td class=""><?= $d['id_target'] == 0 ? "<span class='fw-bold text-success'>Masuk</span>" : "<span class='fw-bold text-danger'>Keluar</span>" ?></td>
                <td class=""><?= $d['id_sumber'] == 0 ? $d['id_target'] : $d['id_sumber'] ?></td>
                <td class="text-end"><?= $d['qty'] ?></td>
            </tr>
        <?php } ?>
    </table>