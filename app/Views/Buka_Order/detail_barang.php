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
                    <?php if ($ds['qty'] == 0 && $ds['sn'] <> "") {
                        continue;
                    } ?>
                    <tr>
                        <td class="fw-bold"><?= $ds['qty'] ?></td>
                        <td><?= $ds['sds'] == 1 ? "<span class='text-danger'>SDS</span>" : $this->dToko[$this->userData['id_toko']]['inisial'] ?></td>
                        <td><?= $ds['sn'] ?></td>
                        <td class="text-end">
                            <form action="<?= PV::BASE_URL ?>Buka_Order/add_barang/<?= $data['id_pelanggan_jenis'] ?>" class="mb-0" method="POST">
                                <input type="hidden" name="sds" value="<?= $ds['sds'] ?>">
                                <input type="hidden" name="sn" value="<?= $ds['sn'] ?>">
                                <input type="hidden" name="kode" value="<?= $ds['id_barang'] ?>">
                                <input type="number" style="width: 50px;" min="1" value="1" name="qty" class="border-0 h-100 rounded text-center"> <button data-bs-dismiss="modal" type="submit" class="btn btn-sm btn-primary">Tambah</button>
                            </form>
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
<?php foreach ($data as $key => $d) { ?>
    <div class="row mx-0">
        <div class="col px-0 mb-2" style="min-width: 200px;">

        </div>
    </div>
<?php  } ?>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $("form").on("submit", function(e) {
        $(".modal").hide();
        var id_paket = $("#paket_barang").val();
        if (typeof id_paket == "undefined") {
            id_paket = "";
        }
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize() + "&id_paket=" + id_paket,
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    });
</script>