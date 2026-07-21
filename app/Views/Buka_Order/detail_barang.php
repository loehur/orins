<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<div class="mb-1">
    <div class="row px-2">
        <div class="col">
            <table class="table table-sm m-0 p-0">
                <?php if (count($data['stok']) == 0) { ?>
                    <tr>
                        <td><span class="text-danger">Stok Toko Kosong</span></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td colspan="10">Toko</td>
                    </tr>
                <?php } ?>
                <?php foreach ($data['stok'] as $ds) { ?>
                    <?php
                    if (isset($ds['cart'])) { ?>
                        <tr>
                            <td class="fw-bold"><?= $ds['cart'] ?></td>
                            <td><?= $ds['sds'] == 1 ? "<span class='text-danger'>SDS</span>" : $this->dToko[$this->userData['id_toko']]['inisial'] ?></td>
                            <td><?= $ds['sn'] ?></td>
                            <td class="text-end">
                                <?php foreach ($ds['cart_list'] as $cs) { ?>
                                    <?= $data['user'][$cs['user_id']]['nama'] ?>:<?= $cs['qty'] ?>&nbsp;
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php
                    $stockDisabled = !empty($data['limited']) && (int)$ds['qty'] === 0;
                    if ($ds['qty'] == 0 && $ds['sn'] <> "") {
                        continue;
                    }
                    ?>
                    <tr>
                        <td class="fw-bold"><?= $ds['qty'] ?></td>
                        <td><?= $ds['sds'] == 1 ? "<span class='text-danger'>SDS</span>" : $this->dToko[$this->userData['id_toko']]['inisial'] ?></td>
                        <td><?= $ds['sn'] ?></td>
                        <td class="text-end">
                            <input type="number"
                                style="width: 50px;"
                                min="1"
                                value="1"
                                class="border-0 h-100 rounded text-center barang-qty-input"
                                <?= (!empty($data['limited']) && $ds['qty'] > 0) ? 'max="' . (int)$ds['qty'] . '"' : '' ?>
                                <?= $stockDisabled ? 'disabled data-stock-disabled="1"' : '' ?>>
                            <button type="button"
                                class="btn btn-sm btn-primary btnAddBarangRow"
                                data-action="<?= PV::BASE_URL ?>Buka_Order/add_barang/<?= (int)$data['id_pelanggan_jenis'] ?>"
                                data-kode="<?= (int)$ds['id_barang'] ?>"
                                data-sds="<?= (int)$ds['sds'] ?>"
                                data-sn="<?= htmlspecialchars($ds['sn'] ?? '', ENT_QUOTES) ?>"
                                <?= $stockDisabled ? 'disabled data-stock-disabled="1"' : '' ?>>Tambah</button>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="border-0" colspan="10"></td>
                </tr>
                <?php if (count($data['stok_gudang']) == 0) { ?>
                    <tr>
                        <td><span class="text-danger">Stok Gudang Kosong</span></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td colspan="10">Gudang</td>
                    </tr>
                <?php } ?>
                <?php foreach ($data['stok_gudang'] as $ds) { ?>
                    <?php if ($ds['qty'] == 0 && $ds['sn'] <> "") {
                        continue;
                    } ?>
                    <tr>
                        <td class="fw-bold"><?= $ds['qty'] ?></td>
                        <td><?= $ds['sds'] == 1 ? "<span class='text-danger'>SDS</span>" : $this->dToko[$this->userData['id_toko']]['inisial'] ?></td>
                        <td colspan="10"><?= $ds['sn'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <div class="ok"></div>
    </div>
</div>
