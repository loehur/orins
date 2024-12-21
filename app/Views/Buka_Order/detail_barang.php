<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<div class="mb-1">
    <div class="row px-2">
        <div class="col">
            <table class="table table-sm m-0 p-0">
                <?php if (count($data['stok']) == 0) { ?>
                    <tr>
                        <td><span class="text-danger">Stok Kosong</span></td>
                    </tr>
                <?php } ?>
                <?php foreach ($data['stok'] as $ds) { ?>
                    <tr>
                        <td class="fw-bold"><?= $ds['qty'] ?></td>
                        <td><?= $ds['sds'] == 1 ? "<span class='text-danger'>S</span>" : "" ?></td>
                        <td><?= $ds['sn'] ?></td>
                        <td class="text-end">
                            <form action="<?= PV::BASE_URL ?>Buka_Order/add_barang/<?= $data['id_pelanggan_jenis'] ?>" class="mb-0" method="POST">
                                <input type="hidden" name="sds" value="<?= $ds['sds'] ?>">
                                <input type="hidden" name="sn" value="<?= $ds['sn'] ?>">
                                <input type="hidden" name="kode" value="<?= $ds['kode_barang'] ?>">
                                <input type="number" style="width: 50px;" max="<?= $ds['qty'] ?>" value="1" name="qty" class="border-0 h-100 rounded text-center"> <button data-bs-dismiss="modal" type="submit" class="btn btn-sm btn-primary">Tambah</button>
                            </form>
                        </td>
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