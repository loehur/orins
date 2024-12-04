<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php
$input = ["grup", "tipe", "brand", "model", "varian1", "varian2"];
$required = ["required", "required", "required", "required", "", ""]
?>
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Gudang_Barang/add" method="POST">
            <div class="row mx-0 mb-4">
                <?php foreach ($input as $k => $i) { ?>
                    <div class="col-auto px-1 mb-2">
                        <div class="row mx-0 mb-1">
                            <div class="col px-0">
                                <textarea readonly id="<?= $i ?>_t" class="text-sm text-secondary border-0 w-100" style="height: 100px;"></textarea>
                            </div>
                        </div>
                        <div class="row mx-0">
                            <div class="col px-0">
                                <label class="text-sm"><?= strtoupper($i) ?></label>
                                <input <?= $required[$k] ?> name="<?= $i ?>_c" id="<?= $i ?>_c" minlength="2" class="float-end border-bottom border-0 mb-2 text-center" maxlength="2" style="width: 30px; text-transform:uppercase"><br>
                                <div class="autocomplete">
                                    <input <?= $required[$k] ?> name="<?= $i ?>" id="<?= $i ?>" class="ac border-bottom border-0 mb-1" style="text-transform:uppercase">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-auto mt-auto px-1 mb-2">
                    <label class="mb-2 text-sm">KODE BARANG PABRIK</label><br>
                    <input name="code_f" id="<?= $i ?>" class="border-bottom border-0 mb-1" style="text-transform:uppercase">
                </div>
                <div class="col mb-2 mt-auto">
                    <div class="mb-2">
                        <input name="sn" class="form-check-input" type="checkbox" value="1">
                        <label class="form-check-label" for="flexCheckDefault">
                            SN
                        </label>
                    </div>
                    <div class="mb-2">
                        <input name="pb" class="form-check-input" type="checkbox" value="1">
                        <label class="form-check-label" for="flexCheckDefault">
                            Publish
                        </label>
                    </div>
                    <button type="submit" class="btn btn-outline-success">Create<br>Update</button>
                </div>
            </div>
        </form>
        <table class="table table-sm">
            <?php foreach ($data['barang'] as $a) { ?>
                <tr>
                    <td>
                        <?= $a['code'] ?>
                    </td>
                    <td class="">
                        <?= strtoupper($a['grup'] . " " . $a['tipe']) ?>
                    </td>
                    <td>
                        <?= strtoupper($a['brand'] . " " . $a['model'] . " " . $a['varian1'] . " " . $a['varian2']) ?>
                    </td>
                    <td>
                        <?= $a['sn'] == 1 ? "SN-<b>YES</b>" : "SN-NO" ?>
                    </td>
                    <td>
                        <?= $a['pb'] == 1 ? "PB-<b>YES</b>" : "PB-NO" ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>

<script>
    var grup = JSON.parse('<?= json_encode($data['grup']) ?>');
    var tipe = JSON.parse('<?= json_encode($data['tipe']) ?>');
    var brand = JSON.parse('<?= json_encode($data['brand']) ?>');

    $(document).ready(function() {
        autocomplete(document.getElementById("grup"), grup);
        autocomplete(document.getElementById("tipe"), tipe);
        autocomplete(document.getElementById("brand"), brand);
        list_gtb();
    });

    var grup_name, tipe_name, brand_name, model_name, varian1_name, varian2_name;
    var gtb, model_c, varian1_c;
    var model = [];
    var varian1 = [];
    var varian2 = [];

    function list_gtb() {
        var grup_t = "";
        for (var key in grup) {
            if (grup.hasOwnProperty(key)) {
                grup_t += grup[key].id + ' ' + grup[key].nama + "\n";
            }
        }
        $('#grup_t').val(grup_t);

        var tipe_t = "";
        for (var key in tipe) {
            if (tipe.hasOwnProperty(key)) {
                tipe_t += tipe[key].id + ' ' + tipe[key].nama + "\n";
            }
        }
        $('#tipe_t').val(tipe_t);

        var brand_t = "";
        for (var key in brand) {
            if (brand.hasOwnProperty(key)) {
                brand_t += brand[key].id + ' ' + brand[key].nama + "\n";
            }
        }
        $('#brand_t').val(brand_t);
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
                    url: '<?= PV::BASE_URL ?>Gudang_Barang/load/' + gtb + '/master_model/code_gtb',
                    dataType: "json",
                    data: {},
                    success: function(res) {
                        model = res;
                        autocomplete(document.getElementById("model"), model);

                        var model_t = "";
                        for (var key in model) {
                            if (model.hasOwnProperty(key)) {
                                model_t += model[key].id + ' ' + model[key].nama + "\n";
                            }
                        }
                        $('#model_t').val(model_t);
                    }
                });
                console.log(gtb);
            } else {
                $('#model_t').val('');
            };
        }

        var new_model_c = gtb + $('#model_c').val();
        if (new_model_c != model_c) {
            model_c = new_model_c;
            if (model_c.length == 8) {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Gudang_Barang/load/' + model_c + '/master_varian1/code_model',
                    dataType: "json",
                    data: {},
                    success: function(res) {
                        varian1 = res;
                        autocomplete(document.getElementById("varian1"), varian1);

                        var varian1_t = "";
                        for (var key in varian1) {
                            if (varian1.hasOwnProperty(key)) {
                                varian1_t += varian1[key].id + ' ' + varian1[key].nama + "\n";
                            }
                        }
                        $('#varian1_t').val(varian1_t);
                    }
                });
                console.log(model_c);
            };
        }

        var new_varian1_c = model_c + $('#varian1_c').val();
        if (new_varian1_c != varian1_c) {
            varian1_c = new_varian1_c;
            if (varian1_c.length == 10) {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Gudang_Barang/load/' + varian1_c + '/master_varian2/code_varian1',
                    dataType: "json",
                    data: {},
                    success: function(res) {
                        varian2 = res;
                        autocomplete(document.getElementById("varian2"), varian2);

                        var varian2_t = "";
                        for (var key in varian2) {
                            if (varian2.hasOwnProperty(key)) {
                                varian2_t += varian2[key].id + ' ' + varian2[key].nama + "\n";
                            }
                        }
                        $('#varian2_t').val(varian2_t);
                    }
                });
                console.log(varian1_c);
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