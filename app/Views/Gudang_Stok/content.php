<?php
$canEditHarga = in_array($this->userData['user_tipe'], PV::PRIV[2]);
$canSnStok = in_array($this->userData['user_tipe'], PV::PRIV[101])
   || in_array($this->userData['user_tipe'], PV::PRIV[102]);
?>
<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main>
    <div class="container text-sm">
        <a href="<?= PV::BASE_URL ?>Gudang_Stok/print" target="_blank"><span class="btn btn-sm btn-primary">Print Stok</span></a>
        <table id="tb_barang" class="hover text-sm stripe">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th class="text-end">U</th>
                    <th class="text-end">D</th>
                    <th class="text-end">O</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
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
                    <td>
                        <small><?= strtoupper($a['grup'] . " " . $a['tipe']) ?></small><br>
                        <small class="fw-bold"><span class="text-success"><?= $a['id'] ?></span> <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?></small>
                    </td>
                    <?php foreach (['harga_1', 'harga_2', 'harga_3'] as $hCol) { ?>
                        <td class="text-end align-top">
                            <?php if ($canEditHarga) { ?>
                                <span class="cell_edit_harga" data-id="<?= $a['id'] ?>" data-primary="id" data-col="<?= $hCol ?>" data-tb="master_barang"><?= number_format((int)$a[$hCol], 0, ',', '.') ?></span>
                            <?php } else { ?>
                                <?= number_format((int)$a[$hCol]) ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td class="text-end align-top">
                        <?php if ($canSnStok && (int)$a['sn'] === 1 && $snQty > 0) { ?>
                            <i class="fa-solid fa-magnifying-glass text-primary cek-sn" data-id="<?= $a['id'] ?>" style="cursor: pointer;" title="Lihat SN"></i>
                        <?php } ?>
                        <?= number_format($qtyToko, 0) ?>/<?= number_format($qtyGudang, 0) ?><br>
                        <span class="text-success"><?= isset($data['stok_sds'][$a['id'] . "#0"]) ? number_format($data['stok_sds'][$a['id'] . "#0"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#0"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#0"]['qty'], 0) : 0 ?></span>&nbsp;
                        <span class="text-primary"><?= isset($data['stok_sds'][$a['id'] . "#1"]) ? number_format($data['stok_sds'][$a['id'] . "#1"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#1"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#1"]['qty'], 0) : 0 ?></span>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<?php if ($canSnStok) { ?>
<div class="modal fade" id="modalSnStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2 px-2">
                <h6 class="modal-title text-sm mb-0">Detail SN</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-0 py-2" id="snStokLoad"></div>
        </div>
    </div>
</div>
<?php } ?>

<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>

<script>
(function() {
    function showSnModal() {
        var modalEl = document.getElementById('modalSnStok');
        if (!modalEl || typeof bootstrap === 'undefined') {
            return null;
        }
        return bootstrap.Modal.getOrCreateInstance(modalEl);
    }

    $(document).ready(function() {
        var $table = $('#tb_barang');
        if (!$table.length || !$.fn.dataTable) {
            return;
        }

        if ($.fn.dataTable.isDataTable($table[0])) {
            $table.DataTable().destroy();
        }

        $table.dataTable({
            order: [],
            bLengthChange: false,
            bFilter: true,
            bInfo: false,
            bAutoWidth: false,
            pageLength: 50,
            scrollY: 600,
            dom: 'lfrti',
            columnDefs: [
                { searchable: false, targets: [1, 2, 3, 4] }
            ]
        });
    });

    <?php if ($canSnStok) { ?>
    $(document).off('click.gudangStokSn', '.cek-sn').on('click.gudangStokSn', '.cek-sn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var id = $(this).data('id');
        var modal = showSnModal();
        if (!modal) {
            return;
        }

        $('#snStokLoad').html('<div class="text-center text-muted small py-3">Memuat...</div>');
        modal.show();
        $('#snStokLoad').load('<?= PV::BASE_URL ?>Gudang_Stok/cek_barang/' + id);
    });
    <?php } ?>

    <?php if ($canEditHarga) { ?>
    var hargaEditClick = 0;

    function parseHargaNum(str) {
        return parseInt(String(str).replace(/\D/g, ''), 10) || 0;
    }

    function formatHargaNum(num) {
        var n = parseInt(num, 10) || 0;
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    $(document).off('click.gudangStokHarga', '.cell_edit_harga').on('click.gudangStokHarga', '.cell_edit_harga', function() {
        if (hargaEditClick !== 0) {
            return;
        }
        hargaEditClick = 1;

        var $el = $(this);
        var id = $el.data('id');
        var primary = $el.data('primary');
        var col = $el.data('col');
        var tb = $el.data('tb');
        var valueBefore = parseHargaNum($el.text());
        var displayValue = formatHargaNum(valueBefore);

        var width = $el.parent().width();
        $el.parent().css('width', width);
        $el.html('<input type="text" inputmode="numeric" class="harga-edit-input" style="outline:none;border:none;width:100%;text-align:right">');
        var $input = $el.find('input');
        $input.val(displayValue).focus().select();

        $input.on('input', function() {
            var raw = $(this).val().replace(/\D/g, '');
            $(this).val(raw === '' ? '' : formatHargaNum(raw));
        });

        $input.on('keydown', function(ev) {
            if (ev.which === 13) {
                ev.preventDefault();
                $(this).blur();
            }
        });

        $input.on('blur', function() {
            var valueAfter = parseHargaNum($(this).val());
            $input.off('input keydown blur');

            if (valueAfter === valueBefore) {
                $el.text(formatHargaNum(valueBefore));
                hargaEditClick = 0;
                return;
            }

            $.ajax({
                url: '<?= PV::BASE_URL ?>Functions/updateCell',
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                    value: valueAfter,
                    col: col,
                    primary: primary,
                    tb: tb
                },
                success: function(res) {
                    hargaEditClick = 0;
                    if (res == 0) {
                        $el.text(formatHargaNum(valueAfter));
                    } else {
                        $el.text(formatHargaNum(valueBefore));
                        alert(res);
                    }
                },
                error: function() {
                    hargaEditClick = 0;
                    $el.text(formatHargaNum(valueBefore));
                }
            });
        });
    });
    <?php } ?>
})();
</script>
