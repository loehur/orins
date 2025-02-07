    <div class="overflow-auto" style="height: 600px;">
        <table class="table table-sm text-sm">
            <?php foreach ($data['mutasi'] as $d) {
                $dp = $data['barang'][$d['id_barang']] ?>
                <tr>
                    <td class=""><a href="<?= PV::BASE_URL ?>Cek/order/<?= $d['ref'] ?>/<?= $d['id_target'] ?>" target="_blank">#<?= $d['id'] ?></a></td>
                    <td class=""><?= date('d/m/y H:i', strtotime($d['insertTime'])) ?></td>
                    <td class="align-middle"><?= trim($dp['brand'] . " " . $dp['model']) ?><?= $dp['product_name'] ?></td>
                    <td><?= $d['sn'] ?></td>
                    <td class="align-middle"><?= $d['id_target'] == $this->userData['id_toko'] ? "<span class='fw-bold text-success'><i class='fa-solid fa-arrow-down'></i></span>" : "<span class='fw-bold text-danger'><i class='fa-solid fa-arrow-up'></i></span>" ?></td>
                    <td class="">
                        <?php if ($d['id_sumber'] == $this->userData['id_toko'] && $d['jenis'] == 2) {
                            echo "Terjual";
                        } else {
                            if ($d['id_sumber'] == 0) {
                                echo "Gudang";
                            } else {
                                echo "Undefined Source ID " . $d['id_sumber'];
                            }
                        }
                        ?>
                    </td>
                    <td class="text-end"><?= $d['qty'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>