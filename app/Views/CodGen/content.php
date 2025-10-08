<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php
$input = ["c1", "c2", "c3", "c4", "c5"];
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
                                    <label class="text-sm"><?= strtoupper($name[$k]) ?> - <?= $input[$k] ?></label>
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
        <table id="tb_barang">
            <thead>
                <tr>
                    <td>Code</td>
                    <td>Item</td>
                    <td></td>
                </tr>
            </thead>
            <?php foreach ($data['barang'] as $a) { ?>
                <tr>
                    <td class="align-top">D<?= $a['code'] ?></span></td>
                    <td class="">
                        <small>
                            <span class="cell_edit_name text-danger" data-code_s="<?= $a['code_s'] ?>" data-mode="c1"><?= strtoupper($a['c1']) ?></span>
                            <span class="cell_edit_name text-primary" data-code_s="<?= $a['code_s'] ?>" data-mode="c2"><?= strtoupper($a['c2']) ?></span>
                            <span class="cell_edit_name text-success" data-code_s="<?= $a['code_s'] ?>" data-mode="c3"><?= strtoupper($a['c3']) ?></span>
                            <span class="cell_edit_name text-info" data-code_s="<?= $a['code_s'] ?>" data-mode="c4"><?= strtoupper($a['c4']) ?></span>
                        </small><br>
                        <span class="cell_edit_name text-dark fw-bold" data-code_s="<?= $a['code_s'] ?>" data-mode="c5"><?= strtoupper($a['c5']) ?></span>
                    </td>
                    <td class="text-danger text-center"><i data-id="<?= $a['id'] ?>" style="cursor: pointer;" class="fa-solid fa-trash apus"></i></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>

<script>
    $(".apus").dblclick(function() {
        if (!confirm("Yakin hapus? cius?")) {
            return;
        }

        var id = $(this).attr('data-id');
        var el = $(this);

        $.ajax({
            url: '<?= PV::BASE_URL ?>CodGen/delete/' + id,
            data: {
                'id': id,
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    el.parent().parent().remove();
                } else {
                    alert(res);
                }
            },
        });
    });

    var click = 0;
    $(".cell_edit_name").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var mode = $(this).attr('data-mode');
        var code_s = $(this).attr('data-code_s');

        var value = $(this).html();
        var value_before = value;
        if (value == "[ ]") {
            value = "";
        }
        var el = $(this);
        var width = el.parent().width();
        var align = "left";

        el.css("width", width);
        el.html("<input required type=" + c2 + " style='text-transform:uppercase;outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

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
                        'value': value_after,
                        'mode': mode,
                        'code_s': code_s
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

    var c1 = JSON.parse('<?= json_encode($data['c1']) ?>');
    var c2 = JSON.parse('<?= json_encode($data['c2']) ?>');
    var c3 = JSON.parse('<?= json_encode($data['c3']) ?>');
    var c4 = JSON.parse('<?= json_encode($data['c4']) ?>');

    $(document).ready(function() {
        autocomplete(document.getElementById("c1"), c1);
        autocomplete(document.getElementById("c2"), c2);
        autocomplete(document.getElementById("c3"), c3);
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

    var c1_name, c2_name, c3_name, c4_name, c5_name;
    var gtb, model_c;
    var c5 = [];

    function list_gtb() {
        var c1_t = "";
        for (var key in c1) {
            if (c1.hasOwnProperty(key)) {
                c1_t += c1[key].id + ' ' + c1[key].nama + "<br>";
            }
        }
        $('#c1_t').html(c1_t);

        var c2_t = "";
        for (var key in c2) {
            if (c2.hasOwnProperty(key)) {
                c2_t += c2[key].id + ' ' + c2[key].nama + "<br>";
            }
        }
        $('#c2_t').html(c2_t);

        var c3_t = "";
        for (var key in c3) {
            if (c3.hasOwnProperty(key)) {
                c3_t += c3[key].id + ' ' + c3[key].nama + "<br>";
            }
        }
        $('#c3_t').html(c3_t);

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
                    url: '<?= PV::BASE_URL ?>CodGen/load/' + gtb + '/master_c5/code_gtb',
                    dataType: "json",
                    data: {},
                    success: function(res) {
                        c5 = res;
                        autocomplete(document.getElementById("c5"), c5);

                        var c5_t = "";
                        for (var key in c5) {
                            if (c5.hasOwnProperty(key)) {
                                c5_t += c5[key].id + ' ' + c5[key].nama + "<br>";
                            }
                        }
                        $('#c5_t').html(c5_t);
                    }
                });
                console.log(gtb);
            } else {
                $('#c5_t').html('');
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
</script>