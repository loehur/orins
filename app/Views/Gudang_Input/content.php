<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Gudang_Input/add" method="POST">
            <div class="row mb-2">
                <div class="col-auto text-center px-1 mb-2">
                    <label>Code Suppiler</label><br>
                    <input name="supplier_c" id="supplier_c" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
                </div>
                <div class="col-auto px-1 mb-2">
                    <div class="autocomplete">
                        <label>Suppiler</label><br>
                        <input name="supplier" required class="ac border-bottom border-0" id="supplier" style="text-transform: uppercase;">
                    </div>
                </div>
                <div class="col-auto px-1 mb-2 text-center">
                    <label>Tanggal</label><br>
                    <input type="date" name="tanggal" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
                </div>
                <div class="col-auto px-1 mb-2 text-end">
                    <label>No. DO</label><br>
                    <input class="text-end border-bottom border-0" required name="no_po" style="text-transform: uppercase;">
                </div>
                <div class="col-auto px-1 mb-2 text-end">
                    <label>No. Faktur</label><br>
                    <input class="text-end border-bottom border-0" required name="no_fak" style="text-transform: uppercase;">
                </div>
                <div class="col-auto px-1 mb-2">
                    <div class="pt-4">
                        <input name="sds" class="form-check-input" type="checkbox" value="1">
                        <label class="form-check-label" for="flexCheckDefault">
                            SDS
                        </label>
                    </div>
                </div>
                <div class="col mt-auto mb-2">
                    <button type="submit" class="btn btn-outline-success">Create</button>
                </div>
            </div>
        </form>

        <table class="table table-sm text-sm">
            <?php foreach ($data['input'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <a href="<?= PV::BASE_URL ?>Gudang_Input/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                    </td>
                    <td>
                        <?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= $data['supplier'][$a['id_sumber']]['nama'] ?>
                    </td>
                    <td>
                        <?php if ($a['cek'] == 0) { ?>
                            <span data-id="<?= $a['id'] ?>" data-col="no_faktur" data-tipe="text" data-primary="id" data-tb="master_input" class="cell_edit"><?= $a['no_faktur'] ?></span>
                        <?php } else { ?>
                            <?= $a['no_faktur'] ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($a['cek'] == 0) { ?>
                            <span data-id="<?= $a['id'] ?>" data-col="no_po" data-tipe="text" data-primary="id" data-tb="master_input" class="cell_edit"><?= $a['no_po'] ?></span>
                        <?php } else { ?>
                            <?= $a['no_po'] ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?= $a['sds'] == 1 ? "SDS" : "ABF" ?>
                    </td>
                    <td class="align-middle">
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-warning"><i class="fa-regular fa-circle"></i> Checking</span>
                        <?php } else { ?>
                            <span class="text-success"><i class="fa-solid fa-check"></i></span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>

<script>
    var supplier = JSON.parse('<?= json_encode($data['supplier']) ?>');

    $(document).ready(function() {
        autocomplete(document.getElementById("supplier"), supplier);
    });

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result)
                }
            },
        });
    });

    setInterval(function() {
        $(".ac").each(function() {
            var val = $(this).attr('data-value');
            if (val != "") {
                $("#" + this.id + "_c").val(val);
            }
        })
    }, 200);

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
                        'tb': tb
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
                            el.html("REJECTED");
                        }
                    },
                });
            }
        });
    });
</script>