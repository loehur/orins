<?php
$canKasirStok = in_array($this->userData['user_tipe'], PV::PRIV[2]);
?>
<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container text-sm">
        <a href="<?= PV::BASE_URL ?>Gudang_Stok/print" target="_blank"><span class="btn btn-sm btn-primary">Print Stok</span></a>
        <table id="tb_barang" class="hover text-sm stripe">
            <thead>
                <th>Barang</th>
                <th class="text-end">U</th>
                <th class="text-end">D</th>
                <th class="text-end">O</th>
                <th>Stok</th>
            </thead>
            <?php foreach ($data['barang'] as $a) {
                $qtyToko = isset($data['stok'][$a['id']]) ? (int)$data['stok'][$a['id']]['qty'] : 0;
                $qtyGudang = isset($data['stok_gudang'][$a['id']]) ? (int)$data['stok_gudang'][$a['id']]['qty'] : 0;
                $showRow = isset($data['stok'][$a['id']]) || (isset($data['stok_gudang'][$a['id']]) && $this->userData['id_toko'] == 1);
                if (!$showRow) {
                    continue;
                }
                $snQty = max($qtyToko, $qtyGudang);
            ?>
                <tr>
                    <td class="">
                        <small><?= strtoupper($a['grup'] . " " . $a['tipe']) ?></small><br>
                        <small class="fw-bold"><span class="text-success"><?= $a['id'] ?></span> <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?></small>
                    </td>
                    <?php foreach (['harga_1', 'harga_2', 'harga_3'] as $hCol) { ?>
                        <td class="text-end align-top">
                            <?php if ($canKasirStok) { ?>
                                <span class="cell_edit_harga" data-id="<?= $a['id'] ?>" data-primary="id" data-col="<?= $hCol ?>" data-tb="master_barang"><?= $a[$hCol] ?></span>
                            <?php } else { ?>
                                <?= number_format((int)$a[$hCol]) ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td class="text-end align-top">
                        <?php if ($canKasirStok && (int)$a['sn'] === 1 && $snQty > 0) { ?>
                            <i class="fa-solid fa-magnifying-glass text-primary cek-sn" data-id="<?= $a['id'] ?>" data-bs-target="#modalSnStok" data-bs-toggle="modal" style="cursor: pointer;"></i>
                        <?php } ?>
                        <?= number_format($qtyToko, 0) ?>/<?= number_format($qtyGudang, 0) ?><br>
                        <span class="text-success"><?= isset($data['stok_sds'][$a['id'] . "#0"]) ? number_format($data['stok_sds'][$a['id'] . "#0"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#0"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#0"]['qty'], 0) : 0 ?></span>&nbsp;
                        <span class="text-primary"><?= isset($data['stok_sds'][$a['id'] . "#1"]) ? number_format($data['stok_sds'][$a['id'] . "#1"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#1"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#1"]['qty'], 0) : 0 ?></span>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<?php if ($canKasirStok) { ?>
<div class="modal" id="modalSnStok">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2 px-2">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-0 py-2" id="snStokLoad"></div>
        </div>
    </div>
</div>
<?php } ?>

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
            "dom": "lfrti",
            "columnDefs": [
                { "searchable": false, "targets": [1, 2, 3, 4] }
            ]
        });
    });

    <?php if ($canKasirStok) { ?>
    var hargaEditClick = 0;

    $(document).on('click', '.cek-sn', function() {
        var id = $(this).attr('data-id');
        $('#snStokLoad').load('<?= PV::BASE_URL ?>Load/spinner/2', function() {
            $('#snStokLoad').load('<?= PV::BASE_URL ?>Gudang_Stok/cek_barang/' + id);
        });
    });

    $(document).on('click', '.cell_edit_harga', function() {
        hargaEditClick += 1;
        if (hargaEditClick !== 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var primary = $(this).attr('data-primary');
        var col = $(this).attr('data-col');
        var tb = $(this).attr('data-tb');
        var value = $(this).html();
        var value_before = value;
        if (value === '') {
            value = 0;
        }
        var el = $(this);
        var width = el.parent().width();
        var align = 'right';

        el.parent().css('width', width);
        el.html("<input required type='number' style='outline:none;border:none;width:" + width + "px;text-align:" + align + "' id='value_harga_edit' value=''>");

        $('#value_harga_edit').val(value).focus();
        $('#value_harga_edit').on('keypress', function(e) {
            if (e.which === 13) {
                $(this).blur();
            }
        });
        $('#value_harga_edit').on('focusout', function() {
            var value_after = $(this).val();
            if (value_after === value_before || value_after === '') {
                el.html(value);
                hargaEditClick = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Functions/updateCell',
                    data: {
                        id: id,
                        value: value_after,
                        col: col,
                        primary: primary,
                        tb: tb
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        hargaEditClick = 0;
                        if (res == 0) {
                            el.html(value_after);
                        } else {
                            el.html(value);
                            alert(res);
                        }
                    },
                });
            }
        });
    });
    <?php } ?>

    var click = 0;
</script>
