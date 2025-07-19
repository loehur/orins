    <div class="overflow-auto" style="height: 600px;">
        <table class="table table-sm text-sm">
            <?php foreach ($data['mutasi'] as $d) {
                $dp = $data['barang'][$d['id_barang']] ?>
                <?php
                $txt = "";
                switch ($d['jenis']) {
                    case 0:
                        $txt = "success";
                        if (isset($data['supplier'][$d['id_sumber']]['nama'])) {
                            $sumber = $data['supplier'][$d['id_sumber']]['nama'];
                        } else {
                            $sumber = "GUDANG";
                        }

                        $target = "GUDANG";
                        break;
                    case 1:
                        $txt = "warning";
                        $sumber = "GUDANG";
                        $target = $data['toko'][$d['id_target']]['nama_toko'];
                        break;
                    case 2:
                        $txt = "danger";
                        if ($d['id_sumber'] == 0) {
                            $sumber = 'GUDANG';
                        } else {
                            $sumber = $data['toko'][$d['id_sumber']]['nama_toko'];
                        }
                        if (isset($data['pelanggan'][$d['id_target']]['nama'])) {
                            $target = $data['pelanggan'][$d['id_target']]['nama'];
                        } else {
                            $target = "UNDEFINED ID " . $d['id_target'];
                        }
                        break;
                    case 3:
                        $txt = "primary";
                        if ($d['id_target'] == 0) {
                            $sumber = $data['toko'][$d['id_sumber']]['nama_toko'];
                            $target = "GUDANG";
                        } else {
                            $sumber = "GUDANG";
                            $target = $data['supplier'][$d['id_target']]['nama'];
                        }
                        break;
                    case 4:
                        $txt = "purple";
                        $sumber = 'GUDANG';
                        $target = "INVENTARIS";
                        break;
                }

                switch ($d['stat']) {
                    case 0:
                        $stat = '<i class="fa-solid fa-spinner text-warning"></i>';
                        break;
                    case 1:
                        $stat = '<i class="fa-solid fa-check text-success"></i>';
                        break;
                    case 2:
                        $stat = '<i class="fa-solid fa-xmark text-danger"></i>';
                        break;
                } ?>

                <tr>
                    <td class="">#<?= $d['id'] ?></td>
                    <td class=""><?= date('d/m/y H:i', strtotime($d['insertTime'])) ?></td>
                    <td><?= $d['sn'] ?></td>
                    <td class="text-end"><?= strtoupper($sumber) ?></td>
                    <td class="align-middle" style="width: 20px;">
                        <span class='fw-bold text-<?= $txt ?>'><i class='fa-solid fa-arrow-right'></i></span>
                    </td>
                    <td><?= strtoupper($target) ?></td>
                    <td><?= $d['sds'] == 1 ? "#S" : "" ?></td>
                    <td class="text-end"><?= $d['qty'] ?></td>
                    <td class="align-middle" style="width: 20px;">
                        <?= $stat ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>