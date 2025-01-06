    <table class="table table-sm">
        <?php foreach ($data['mutasi'] as $d) {
            $dp = $data['barang'];
            $target_link = "";

            switch ($d['jenis']) {
                case 0:
                    $target_link = "Gudang_Input";
                    break;
                case 1:
                    $target_link = "Stok_Transfer";
                    break;
            }
        ?>
            <tr>
                <td class=""><?= date('d/m/y H:i', strtotime($d['insertTime'])) ?></td>
                <td><a href="<?= PV::BASE_URL . $target_link ?>/list/<?= $d['ref'] ?>"><?= $d['ref'] ?></a></td>
                <td><?= $d['sn'] ?></td>
                <td class=""><?= $d['id_target'] == 0 ? "<span class='fw-bold text-success'>Masuk</span>" : "<span class='fw-bold text-danger'>Keluar</span>" ?></td>
                <td class=""><?= $d['id_sumber'] == 0 ? $d['id_target'] : $d['id_sumber'] ?></td>
                <td class="text-end"><?= $d['qty'] ?></td>
            </tr>
        <?php } ?>
    </table>