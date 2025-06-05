<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container text-sm">
        <a href="<?= PV::BASE_URL ?>Barang_Harga/print" target="_blank"><span class="btn btn-sm btn-primary">Print Stok</span></a>
        <table id="tb_barang" class="hover text-sm">
            <thead>
                <th>Head</th>
                <th>Nama</th>
                <th class="text-end">Umum</th>
                <th class="text-end">R/D</th>
                <th class="text-end">Olshop</th>
                <th>Stok</th>
            </thead>
            <?php foreach ($data['barang'] as $a) {
                if (isset($data['stok'][$a['id']])) {
                    $dstok = isset($data['stok'][$a['id']]) ? $data['stok'][$a['id']]['qty'] : 0; ?>
                    <tr>
                        <td class="">
                            <?= strtoupper($a['grup'] . " " . $a['tipe']) ?>
                        </td>
                        <td>
                            <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?>
                        </td>
                        <td class="text-end">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_1" data-tb="master_barang"><?= $a['harga_1'] ?></span>
                        </td>
                        <td class="text-end">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_2" data-tb="master_barang"><?= $a['harga_2'] ?></span>
                        </td>
                        <td class="text-end">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_3" data-tb="master_barang"><?= $a['harga_3'] ?></span>
                        </td>
                        <td class="text-end">
                            <?php if ($a['sn'] == 1 && $dstok > 0) { ?>
                                <i class="fa-solid fa-magnifying-glass text-primary cek" data-id="<?= $a['id'] ?>" data-bs-target="#exampleModal" data-bs-toggle="modal" style="cursor: pointer;"></i>
                            <?php } ?>
                            <?= $data['stok'][$a['id']]['qty'] ?>/<?= isset($data['stok_gudang'][$a['id']]['qty']) ? $data['stok_gudang'][$a['id']]['qty'] : 0 ?>
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php if (isset($data['stok_gudang'][$a['id']]) && $this->userData['id_toko'] == 1) {
                        $dstok = isset($data['stok_gudang'][$a['id']]) ? $data['stok_gudang'][$a['id']]['qty'] : 0; ?>
                        <tr>
                            <td class="">
                                <?= strtoupper($a['grup'] . " " . $a['tipe']) ?>
                            </td>
                            <td>
                                <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?>
                            </td>
                            <td class="text-end">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_1" data-tb="master_barang"><?= $a['harga_1'] ?></span>
                            </td>
                            <td class="text-end">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_2" data-tb="master_barang"><?= $a['harga_2'] ?></span>
                            </td>
                            <td class="text-end">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_3" data-tb="master_barang"><?= $a['harga_3'] ?></span>
                            </td>
                            <td class="text-end">
                                <?php if ($a['sn'] == 1 && $dstok > 0) { ?>
                                    <i class="fa-solid fa-magnifying-glass text-primary cek" data-id="<?= $a['id'] ?>" data-bs-target="#exampleModal" data-bs-toggle="modal" style="cursor: pointer;"></i>
                                <?php } ?>
                                <?= isset($data['stok'][$a['id']]['qty']) ? $data['stok'][$a['id']]['qty'] : 0 ?>/<?= isset($data['stok_gudang'][$a['id']]['qty']) ? $data['stok_gudang'][$a['id']]['qty'] : 0 ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </table>
    </div>
</main>

<div class="modal" id="exampleModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2 px-2">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-0 py-2" id="load"></div>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tb_barang').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": -1,
            "scrollY": 600,
            "dom": "lfrti"
        });
    })

    $(".cek").click(function() {
        var id = $(this).attr("data-id");

        $("#load").load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
            $("#load").load("<?= PV::BASE_URL ?>Barang_Harga/cek_barang/" + id);
        });
    })

    var click = 0;
    $(".cell_edit").on('click', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var primary = $(this).attr('data-primary');
        var col = $(this).attr('data-col');
        var tb = $(this).attr('data-tb');
        var tipe = "number";
        var value = $(this).html();
        var value_before = value;
        if (value == "") {
            value = 0;
        }
        var el = $(this);
        var width = el.parent().width();
        var align = "right";

        el.parent().css("width", width);
        el.html("<input required type=" + tipe + " style='text-transform:uppercase;outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value=''>");

        $("#value_").focus();
        $('#value_').keypress(function(e) {
            if (e.which == 13) {
                $(this).blur();
            }
        });
        $("#value_").focusout(function() {
            var value_after = $(this).val().toUpperCase();
            if (value_after === value_before || value_after == "") {
                el.html(value);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Functions/updateCell',
                    data: {
                        'id': id,
                        'value': value_after,
                        'col': col,
                        'primary': primary,
                        'tb': tb
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        click = 0;
                        if (res == 0) {
                            el.html(value_after);
                        } else {
                            el.html(res);
                        }
                    },
                });
            }
        });
    });
</script>