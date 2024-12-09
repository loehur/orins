<main>
    <!-- Main page content-->
    <div class="container">
        <table class="table table-sm">
            <thead>
                <th>Kode</th>
                <th>Head</th>
                <th>Nama</th>
                <th class="text-end d-none">Modal</th>
                <th class="text-end">Harga Umum</th>
                <th class="text-end">Harga Dealer</th>
                <th class="text-end">Harga Olshop</th>
            </thead>
            <?php foreach ($data['barang'] as $a) { ?>
                <tr>
                    <td>
                        <?= $a['code'] ?>
                    </td>
                    <td class="">
                        <?= strtoupper($a['grup'] . " " . $a['tipe']) ?>
                    </td>
                    <td>
                        <?= strtoupper($a['brand'] . " " . $a['model']) ?>
                    </td>
                    <td class="text-end d-none">
                        <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga" data-tb="master_barang"><?= $a['harga'] ?></span>
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
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    var click = 0;
    $(".cell_edit").on('dblclick', function() {
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
        el.html("<input required type=" + tipe + " style='text-transform:uppercase;outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

        $("#value_").focus();
        $('#value_').keypress(function(e) {
            if (e.which == 13) {
                $(this).blur();
            }
        });
        $("#value_").focusout(function() {
            var value_after = $(this).val().toUpperCase();
            if (value_after === value_before) {
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