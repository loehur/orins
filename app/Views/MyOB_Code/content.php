<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container">
        <div>
            <table id="tb_barang" class="hover text-sm">
                <thead>
                    <tr>
                        <td>Orins/MyOB</td>
                        <td>Item</td>
                    </tr>
                </thead>
                <?php foreach ($data['barang'] as $a) { ?>
                    <tr>
                        <td>
                            <small><?= $a['code'] ?></small><br>
                            <span class="cell_edit text-success" data-id="<?= $a['id'] ?>" data-col="code_myob" data-tipe="text" data-primary="id" data-tb="master_barang"><?= strlen($a['code_myob']) == 0 ? "[ ]" : $a['code_myob'] ?></span>
                        </td>
                        <td class="">
                            <span class="text-sm"><?= strtoupper($a['grup'] . " " . $a['tipe']) ?></span>
                            <br>
                            <span class="text-success"><?= strtoupper($a['brand']) ?> <?= strtoupper($a['model']) ?></span>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tb_barang').dataTable({
            "order": [],
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": 30,
            "scrollY": 700,
            "dom": "lfrti"
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
        var tipe = $(this).attr('data-tipe');
        var value = $(this).html();
        var value_before = value;
        if (value == "[ ]") {
            value = "";
        }
        var el = $(this);
        var width = el.parent().width();
        var align = "left";
        if (tipe == "number") {
            align = "right";
        }

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
                        'tb': tb,
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        click = 0;
                        if (res == 0) {
                            if (value_after == "") {
                                el.html("[ ]");
                            } else {
                                el.html(value_after);
                            }
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });
</script>