<div class="mb-1">
    <div class="row px-2">
        <div class="col">
            <?php
            print_r($data['stok']);
            ?>
            <table class="table table-sm m-0 p-0">
                <?php if (count($data['stok']) == 0) { ?>
                    <tr>
                        <td><span class="text-danger">Stok Kosong</span></td>
                    </tr>
                <?php } ?>
                <?php foreach ($data['stok'] as $ds) {
                    if ($ds['qty'] > 0) { ?>
                        <tr>
                            <td class="text-end">
                                <form action="<?= PV::BASE_URL ?>Paket/add_barang/<?= $data['ref'] ?>" class="mb-0" method="POST">
                                    <input type="hidden" name="kode" value="<?= $ds['id_barang'] ?>">
                                    <input type="number" style="width: 50px;" value="1" name="qty" class="border-0 h-100 rounded text-center"> <button data-bs-dismiss="modal" type="submit" class="btn btn-sm btn-primary">Tambah</button>
                                </form>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td><span class="text-danger">Stok Kosong</span></td>
                        </tr>
                <?php }
                } ?>
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
    $("form").on("submit", function(e) {
        $(".modal").hide();
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content(<?= $data['id_pelanggan_jenis'] ?>, <?= $data['ref'] ?>);
                } else {
                    alert(res);
                }
            }
        });
    });
</script>