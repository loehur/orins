    <table class="table table-sm text-sm">
        <?php foreach ($data['mutasi'] as $d) {
            $dp = $data['barang'];
            $target_link = "Home";

            if ($d['jenis'] == 0) continue;

            switch ($d['jenis']) {
                case 1:
                    $href = PV::BASE_URL . "Stok_Transfer/list/" . $d['ref'];
                    break;
                case 2:
                    if ($d['id_sumber'] == $this->userData['id_toko']) {
                        $href = PV::BASE_URL . "Cek/order/" . $d['ref'] . "/" . $d['id_target'];
                    } else {
                        $href = PV::BASE_URL . "Gudang_Penjualan/list/" . $d['ref'];
                    }
                    break;
                case 3:
                    if ($d['id_sumber'] == 0) {
                        $href = PV::BASE_URL . "Retur_Barang_G/list/" . $d['ref'];
                    } else {
                        $href = PV::BASE_URL . "Gudang_BMasuk/list/" . $d['ref'];
                    }
                    break;
                default:
                    $href = "#";
                    break;
            }

            $target = "UNDEFINED"; ?>

            <tr class="<?= $d['stat'] == 2 ? 'table-danger text-secondary' : '' ?>">
                <td>#<?= $d['id'] ?></td>
                <td class=""><?= date('d/m/y H:i', strtotime($d['insertTime'])) ?></td>
                <td><a target="_blank" href="<?= $href ?>"><?= $d['ref'] ?></a></td>
                <td><span><?= $d['sn'] ?></span></td>
                <td class="align-middle">
                    <?php
                    switch ($d['jenis']) {
                        case 1:
                            echo '<i class="fa-solid fa-arrow-down text-success"></i>';
                            break;
                        case 2:
                            if ($d['id_target'] == 0) {
                                echo '<i class="fa-solid fa-arrow-up text-danger"></i>';
                            } else {
                                echo '<i class="fa-solid fa-arrow-up text-purple"></i>';
                            }
                            break;
                        case 3: //retur
                            if ($d['id_target'] == 0) {
                                echo '<i class="fa-solid fa-arrow-down text-success"></i>';
                            } else {
                                echo '<i class="fa-solid fa-arrow-up text-primary"></i>';
                            }
                            break;
                        case 4:
                            echo '<i class="fa-solid fa-arrow-up text-warning"></i>';
                            break;
                    } ?>
                </td>
                <td>
                    <?php
                    switch ($d['jenis']) {
                        case 1:
                            echo 'Masuk';
                            break;
                        case 2:
                            if ($d['id_sumber'] == 0) {
                                echo 'Gudang Jual';
                            } else {
                                echo 'Toko Jual';
                            }
                            break;
                        case 3:
                            echo 'Retur';
                            break;
                        case 4:
                            if ($d['id_sumber'] == 0) {
                                echo 'Gudang Pakai';
                            } else {
                                echo 'Toko Pakai';
                            }
                            break;
                    } ?>
                </td>
                <td class="">
                    <?php switch ($d['jenis']) {
                        case 1: //transfer
                            $target = "Gudang";
                            break;
                        case 2: //jual
                            $target = $data['pelanggan'][$d['id_target']]['nama'];
                            break;
                        case 3: //retur
                            if ($d['id_sumber'] == 0) {
                                if ($d['id_target'] == 0) {
                                    $target = "GUDANG";
                                } else {
                                    $target = $data['supplier'][$d['id_target']]['nama'];
                                }
                            } else {
                                $target = $this->dToko[$d['id_sumber']]['inisial'];
                            }
                            break;
                        case 4: //pakai
                            $target = strtoupper($data['akun_pakai'][$d['id_target']]['nama']);
                            break;
                        default:
                            $target = "UNDEFINED";
                            break;
                    } ?>
                    <span><?= $target ?></span>
                </td>
                <td>S#<span><?= $d['sds'] == 1 ? "1" : "0" ?></span> <small class="fw-bold"><?= $d['sds'] == 1 ? "SDS" : "ABF" ?></small></td>
                <td class="text-end"><?= $d['qty'] ?></td>
            </tr>
        <?php } ?>
    </table>

    <script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>