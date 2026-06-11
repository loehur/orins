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
                                <input type="number" style="width: 50px;" min="1" value="1" name="qty" class="border-0 h-100 rounded text-center" <?= (!empty($data['limited']) && $ds['qty'] > 0) ? 'max="' . $ds['qty'] . '"' : '' ?> <?= (!empty($data['limited']) && $ds['qty'] == 0) ? 'disabled' : '' ?>> <button type="submit" class="btn btn-sm btn-primary" <?= (!empty($data['limited']) && $ds['qty'] == 0) ? 'disabled' : '' ?>>Tambah</button>
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

    $("#detail_barang").off("submit.barangDetail", "form").on("submit.barangDetail", "form", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $form = $(this);
        if (typeof window.beginBukaOrderSubmit === 'function') {
            if (!window.beginBukaOrderSubmit($form)) {
                return false;
            }
        } else if ($form.data('ajaxSubmitting')) {
            return false;
        } else {
            $form.data('ajaxSubmitting', true);
            $form.find('button[type="submit"]').prop('disabled', true);
        }
        var id_paket = $("#paket_barang").val();
        if (typeof id_paket == "undefined") {
            id_paket = "";
        }
        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize() + "&id_paket=" + id_paket,
            type: $form.attr("method"),
            success: function(res) {
                if (res == 0) {
                    var modalEl = $form.closest('.modal')[0];
                    if (modalEl) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    }
                    formPickLoaded = false;
                    $('#form-pick-modals').empty();
                    content();
                    if (typeof window.resetBukaOrderSubmit === 'function') {
                        setTimeout(function() {
                            window.resetBukaOrderSubmit($form);
                        }, 1200);
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(res, 'danger');
                    } else {
                        alert(res);
                    }
                    if (typeof window.resetBukaOrderSubmit === 'function') {
                        window.resetBukaOrderSubmit($form);
                    } else {
                        $form.data('ajaxSubmitting', false);
                        $form.find('button[type="submit"]').prop('disabled', false);
                    }
                }
            },
            error: function() {
                if (typeof showToast === 'function') {
                    showToast('Gagal menambahkan barang. Coba lagi.', 'danger');
                }
                if (typeof window.resetBukaOrderSubmit === 'function') {
                    window.resetBukaOrderSubmit($form);
                } else {
                    $form.data('ajaxSubmitting', false);
                    $form.find('button[type="submit"]').prop('disabled', false);
                }
            }
        });
    });
</script>