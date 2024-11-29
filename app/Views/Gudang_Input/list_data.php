<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php $d = $data['input']; ?>

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="row mb-2">
            <div class="col-auto mt-auto px-1 mb-2">
                <a href="<?= PV::BASE_URL ?>Gudang_Input"><button class="btn btn-outline pb-0 border-0"><i class="fa-solid fa-chevron-left"></i> <small>Back</small></button></a>
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>Code Suppiler</label><br>
                <input name="supplier_c" id="supplier_c" value="<?= $d['id_supplier'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto px-1 mb-2 autocomplete">
                <div class="autocomplete">
                    <label>Suppiler</label><br>
                    <input name="supplier" value="<?= $d['supplier'] ?>" class="border-bottom border-0" id="supplier" readonly style="text-transform: uppercase;">
                </div>
            </div>
            <div class="col-auto px-1 mb-2 text-center">
                <label>Tanggal</label><br>
                <input type="date" name="tanggal" readonly class="text-center border-bottom border-0" value="<?= $d['tanggal'] ?>">
            </div>
            <div class="col-auto px-1 mb-2 text-end">
                <label>No. Faktur</label><br>
                <input class="text-end border-bottom border-0" value="<?= $d['no_faktur'] ?>" name="no_fak" readonly style="text-transform: uppercase;">
            </div>
            <div class="col-auto px-1 mb-2 text-end">
                <label>No. PO</label><br>
                <input class="text-end border-bottom border-0" value="<?= $d['no_po'] ?>" name="no_po" readonly style="text-transform: uppercase;">
            </div>
            <div class="col-auto px-1 mb-2">
                <div class="pt-4">
                    <input name="sds" class="form-check-input" type="checkbox" <?= $d['sds'] == 1 ? "checked" : "" ?> disabled>
                    <label class="form-check-label" for="flexCheckDefault">
                        SDS
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <form action="<?= PV::BASE_URL ?>Gudang_Input/add_mutasi" method="POST">
            <div class="row mb-2 mx-0">
                <input type="hidden" name="head_id" value="<?= $data['id'] ?>">
                <div class="col-auto text-center px-1 mb-2">
                    <label>Kode Barang</label><br>
                    <input readonly name="barang_" id="barang_c" required class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
                </div>
                <div class="col-auto px-1 mb-2 autocomplete">
                    <div class="autocomplete">
                        <label>Barang</label><br>
                        <input name="barang" class="ac border-bottom border-0" required id="barang" style="text-transform: uppercase;">
                    </div>
                </div>
                <div class="col-auto px-1 mb-2 text-end" id="col_qty">
                    <label>Qty</label><br>
                    <input id="qty" required type="number" min="1" class="text-end border-bottom border-0" name="qty" style="text-transform: uppercase;">
                </div>
                <div class="col mt-auto mb-2">
                    <button type="submit" class="btn btn-outline-success">Add</button>
                </div>
            </div>
        </form>

        <table class="table table-sm mx-1">
            <?php
            $no = 0;
            foreach ($data['mutasi'] as $a) {
                $no++; ?>
                <tr id="tr<?= $a['id'] ?>">
                    <td class="text-end">
                        <?= $no ?>
                    </td>
                    <td class="">
                        <?= $a['nama'] ?>
                    </td>
                    <td class="text-end">
                        <?= $a['qty'] ?>
                    </td>
                    <td>
                        <?php if ($a['sn_c'] == 1) { ?>
                            <span data-id="<?= $a['id'] ?>" data-col="sn" data-tipe="text" data-primary="id" data-tb="master_mutasi" class="cell_edit"><?= strlen($a['sn']) == 0 ? "[ ]" : $a['sn'] ?></span>
                        <?php } ?>
                    </td>
                    <td class="align-middle text-end">
                        <span data-id="<?= $a['id'] ?>" data-primary="id" data-tb="master_mutasi" class="cell_delete text-danger" style="cursor: pointer;"><i class="fa-regular fa-trash-can"></i></span>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/autocomplete.js"></script>

<script>
    var barang = JSON.parse('<?= json_encode($data['barang']) ?>');
    $(document).ready(function() {
        autocomplete(document.getElementById("barang"), barang);
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
            var value_after = $(this).val();
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
                        if (res == "") {
                            el.html("[ ]");
                        } else {
                            el.html(res);
                        }
                    },
                });
            }
        });
    });

    $(".cell_delete").dblclick(function() {
        var id = $(this).attr('data-id');
        var primary = $(this).attr('data-primary');
        var tb = $(this).attr('data-tb');

        console.log(id, primary, tb);

        $.ajax({
            url: '<?= PV::BASE_URL ?>Functions/deleteCell',
            data: {
                'id': id,
                'primary': primary,
                'tb': tb
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    $("#tr" + id).remove();
                }
            },
        });
    });

    setInterval(function() {
        $(".ac").each(function() {
            var get = $(this).attr('data-value');
            if (typeof barang[get] != "undefined") {
                var val = barang[get].code;
                $("#" + this.id + "_c").val(val);
            } else {
                $("#" + this.id + "_c").val("");
            }
        })
    }, 200);
</script>