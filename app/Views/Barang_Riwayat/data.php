    <table class="table table-sm text-sm">
        <?php foreach ($data['mutasi'] as $d) {
            $dp = $data['barang'];
            $target_link = "Home";

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
                <td class="align-middle"><?= $d['id_target'] == 0 ? '<i class="fa-solid fa-arrow-down text-success"></i>' : '<i class="fa-solid fa-arrow-up text-danger"></i>' ?></td>
                <td class="">
                    <?php switch ($d['jenis']) {
                        case 0:
                            $target = isset($data['supplier'][$d['id_sumber']]['nama']) ? $data['supplier'][$d['id_sumber']]['nama'] : "UNDEFINED " . $d['id_sumber'];
                            break;
                        case 1:
                            $target = $this->dToko[$d['id_target']]['inisial'];
                            break;
                        case 2:
                            $target = $data['pelanggan'][$d['id_target']]['nama'];
                            break;
                        case 3:
                            $target = $data['supplier'][$d['id_target']]['nama'];
                            break;
                    } ?>

                    <span><?= $target ?></span>
                </td>
                <td class="text-end"><?= $d['qty'] ?></td>
            </tr>
        <?php } ?>
    </table>