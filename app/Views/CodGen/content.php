<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php
$input = ["grup", "tipe", "brand", "c4", "model"];
$required = ["required", "required", "required", "required", "required"];
$name = ["Akun", "Grup", "Merk", "Tipe", "Detail"];
$max_length = [2, 2, 3, 3, 2];
?>
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>CodGen/add" method="POST">
            <div class="row mx-0 mb-4">
                <?php foreach ($input as $k => $i) { ?>
                    <div class="col px-1 mb-2">
                        <div class="row mx-0 mb-1">
                            <div class="col px-0 overflow-auto">
                                <div id="<?= $i ?>_t" class="text-sm text-secondary border-0 w-100" style="height: 105px;"></div>
                            </div>
                        </div>
                        <div class="row mx-0 mt-2">
                            <div class="col px-0">
                                <div style="min-width: 100px;">
                                    <label class="text-sm"><?= strtoupper($name[$k]) ?></label>
                                    <input <?= $required[$k] ?> name="<?= $i ?>_c" id="<?= $i ?>_c" minlength="2" class="float-end border-bottom border-0 mb-2 text-center" maxlength="<?= $max_length ?>" style="width: 50px; text-transform:uppercase">
                                </div>
                                <div class="autocomplete">
                                    <input data-tab="<?= $k ?>" <?= $required[$k] ?> name="<?= $i ?>" id="<?= $i ?>" class="ac border-bottom border-0 mb-1 w-100" style="text-transform:uppercase">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="col mb-2 mt-auto">
                    <button type="submit" class="btn btn-outline-success">Create</button>
                </div>
            </div>
        </form>
        <div class="overflow-auto" style="height: 500px;">
            <table id="tb_barang">
                <thead>
                    <tr>
                        <td>Code</td>
                        <td>Item</td>
                    </tr>
                </thead>
                <?php foreach ($data['barang'] as $a) { ?>
                    <tr>
                        <td>D<?= $a['code'] ?></span></td>
                        <td class="">
                            <span class="text-sm"><?= strtoupper($a['grup'] . " " . $a['tipe']) ?></span> <span class="text-sm"><?= strtoupper($a['brand']) ?> <?= strtoupper($a['brand']) ?></span><br>
                            <span class="cell_edit_name" data-code="<?= $a['code'] ?>" data-id="<?= $a['id'] ?>" data-mode="M"><?= strtoupper($a['model']) ?></span>
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
    $(".cell_edit_name").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var mode = $(this).attr('data-mode');
        var code = $(this).attr('data-code');

        var value = $(this).html();
        var value_before = value;
        if (value == "[ ]") {
            value = "";
        }
        var el = $(this);
        var width = el.parent().width();
        var align = "left";

        el.css("width", width);
        el.html("<input required type=" + tipe + " style='text-transform:uppercase;outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

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
                    url: '<?= PV::BASE_URL ?>CodGen/update_name',
                    data: {
                        'id': id,
                        'value': value_after,
                        'mode': mode,
                        'code': code
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        click = 0;
                        if (res == 0) {
                            el.html(value_after);
                        } else {
                            alert(res);
                            content();
                        }
                    },
                });
            }
        });
    });

    var grup = JSON.parse('<?= json_encode($data['grup']) ?>');
    var tipe = JSON.parse('<?= json_encode($data['tipe']) ?>');
    var brand = JSON.parse('<?= json_encode($data['brand']) ?>');
    var c4 = JSON.parse('<?= json_encode($data['c4']) ?>');

    $(document).ready(function() {
        autocomplete(document.getElementById("grup"), grup);
        autocomplete(document.getElementById("tipe"), tipe);
        autocomplete(document.getElementById("brand"), brand);
        autocomplete(document.getElementById("c4"), c4);
        list_gtb();

        $('#tb_barang').dataTable({
            "order": [],
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": 30,
            "scrollY": 400,
            "dom": "lfrti"
        });
    });

    var grup_name, tipe_name, brand_name, c4_name, model_name;
    var gtb, model_c;
    var model = [];

    function list_gtb() {
        var grup_t = "";
        for (var key in grup) {
            if (grup.hasOwnProperty(key)) {
                grup_t += grup[key].id + ' ' + grup[key].nama + "<br>";
            }
        }
        $('#grup_t').html(grup_t);

        var tipe_t = "";
        for (var key in tipe) {
            if (tipe.hasOwnProperty(key)) {
                tipe_t += tipe[key].id + ' ' + tipe[key].nama + "<br>";
            }
        }
        $('#tipe_t').html(tipe_t);

        var brand_t = "";
        for (var key in brand) {
            if (brand.hasOwnProperty(key)) {
                brand_t += brand[key].id + ' ' + brand[key].nama + "<br>";
            }
        }
        $('#brand_t').html(brand_t);

        var c4_t = "";
        for (var key in c4) {
            if (c4.hasOwnProperty(key)) {
                c4_t += c4[key].id + ' ' + c4[key].nama + "<br>";
            }
        }
        $('#c4_t').html(c4_t);
    }

    setInterval(function() {
        $(".ac").each(function() {
            if ($(this).val() == "") {
                $(this).removeAttr('data-value');
            }

            var val = $(this).attr('data-value');
            if (typeof val !== "undefined") {
                if (val != "") {
                    $("#" + this.id + "_c").val(val);
                }
            }
        })
    }, 200);

    setInterval(function() {
        var new_gtb = $('#grup_c').val() + $('#tipe_c').val() + $('#brand_c').val() + $('#c4_c').val();

        if (new_gtb != gtb) {
            gtb = new_gtb;
            if (gtb.length == 10) {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>CodGen/load/' + gtb + '/master_model/code_gtb',
                    dataType: "json",
                    data: {},
                    success: function(res) {
                        model = res;
                        autocomplete(document.getElementById("model"), model);

                        var model_t = "";
                        for (var key in model) {
                            if (model.hasOwnProperty(key)) {
                                model_t += model[key].id + ' ' + model[key].nama + "<br>";
                            }
                        }
                        $('#model_t').html(model_t);
                    }
                });
                console.log(gtb);
            } else {
                $('#model_t').html('');
            };
        }
    }, 1000);

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
    $(".cell_edit").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var col = $(this).attr('data-col');
        var parent = $(this).attr('data-parent');
        var value = $(this).html();
        var value_before = value;
        if (value == "[ ]") {
            value = "";
        }
        var el = $(this);
        var width = el.parent().width();
        var align = "center";

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
            if (value_after === value_before || value_after == "") {
                el.html(value);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>CodGen/update_code',
                    data: {
                        'id': id,
                        'value': value_after,
                        'value_before': value_before,
                        'col': col,
                        'parent': parent,
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(res) {
                        click = 0;
                        if (res == 0) {
                            el.html(value_after);
                        } else {
                            alert(res);
                            content();
                        }
                    },
                });
            }
        });
    });
</script>