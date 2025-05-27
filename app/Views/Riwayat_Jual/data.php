    <div class="overflow-auto py-1" style="height: 620px;">
        <table class="table table-sm text-sm">
            <?php foreach ($data['mutasi'] as $d) {
                $dp = $data['barang'][$d['id_barang']] ?>
                <tr>
                    <td class="">
                        <a href="<?= PV::BASE_URL ?>Cek/order/<?= $d['ref'] ?>/<?= $d['id_target'] ?>" target="_blank">#<?= $d['id'] ?></a>
                        <br><?= date('d/m/y', strtotime($d['insertTime'])) ?>
                    </td>
                    <td class="align-top">
                        <small><i class="fa-regular fa-user"></i> <?= $this->dPelanggan[$d['id_target']]['nama'] ?></small>
                        <br><?= trim($dp['brand'] . " " . $dp['model']) ?><?= $dp['product_name'] ?>
                        <br><small><?= $d['sn'] ?></small>
                    </td>
                    <td class="text-end"><?= $d['qty'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>