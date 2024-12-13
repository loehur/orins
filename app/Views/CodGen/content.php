<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php
$input = ["grup", "tipe", "brand", "model", "c5"];
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
                        <div class="row mx-0">
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
            <table class="table table-sm">
                <?php foreach ($data['barang'] as $a) { ?>
                    <tr>
                        <td>
                            <table class="p-0 m-0">
                                <tr>
                                    <?php
                                    $no = 0;
                                    for ($i = 0; $i <= 6; $i += 2) {
                                        $no += 1;
                                        if ($i == 6) { ?>
                                            <td>
                                                <span class="cell_edit" data-parent="<?= substr($a['code'], 0, $i) ?>" data-id="<?= $a['code'] ?>" data-col="<?= $no ?>"><?= substr($a['code'], $i, 3) ?></span><br>
                                            </td>
                                        <?php } else { ?>
                                            <td>
                                                <span class="cell_edit" data-parent="<?= substr($a['code'], 0, $i) ?>" data-id="<?= $a['code'] ?>" data-col="<?= $no ?>"><?= substr($a['code'], $i, 2) ?></span><br>
                                            </td>
                                        <?php } ?>
                                    <?php
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td><?= $a['pb'] == 1 ? "<b>PB</b>" : "" ?> </td>
                                    <td><?= $a['sn'] == 1 ? "<b>SN</b>" : "" ?></td>
                                </tr>
                            </table>
                        </td>
                        <td class="">
                            <span class="text-sm"><?= strtoupper($a['grup'] . " " . $a['tipe']) ?></span>
                            <br>
                            <?= strtoupper($a['brand']) ?>
                            <span class="cell_edit_name" data-code="<?= $a['code'] ?>" data-id="<?= $a['id'] ?>" data-mode="M"><?= strtoupper($a['model']) ?></span>
                            <br>
                            <?= $a['code_f'] ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>

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

    $(document).ready(function() {
        autocomplete(document.getElementById("grup"), grup);
        autocomplete(document.getElementById("tipe"), tipe);
        autocomplete(document.getElementById("brand"), brand);
        list_gtb();
    });

    var grup_name, tipe_name, brand_name, model_name;
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
        var new_gtb = $('#grup_c').val() + $('#tipe_c').val() + $('#brand_c').val();

        if (new_gtb != gtb) {
            gtb = new_gtb;
            if (gtb.length == 6) {
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
        var width = el.parent().width() + 5;
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