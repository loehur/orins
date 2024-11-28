<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Master_Barang/add" method="POST">
            <div class="row mx-0 mb-4">
                <div class="col-auto autocomplete px-1 mb-2">
                    <div class="row mx-0 mb-1">
                        <div class="col px-0">
                            <textarea readonly id="grup_t" class="text-sm text-secondary border-0 w-100" style="height: 100px;"></textarea>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col px-0">
                            <label class="text-sm">Grup</label>
                            <input name="grup_c" id="grup_c" class="float-end border-bottom border-0 mb-2 text-center" maxlength="2" style="width: 30px; text-transform:uppercase"><br>
                            <input name="grup" id="grup" class="border-bottom border-0 mb-1" style="text-transform:uppercase">
                        </div>
                    </div>
                </div>
                <div class="col-auto autocomplete px-1 mb-2">
                    <div class="row mx-0 mb-1">
                        <div class="col px-0">
                            <textarea readonly id="tipe_t" class="text-sm text-secondary border-0 w-100" style="height: 100px;"></textarea>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col px-0">
                            <label class="text-sm">Tipe</label>
                            <input name="tipe_c" id='tipe_c' class="float-end border-bottom border-0 mb-2 text-center" maxlength="2" style="width: 30px; text-transform:uppercase"><br>
                            <input name="tipe" id="tipe" class="border-bottom border-0 mb-1" style="text-transform:uppercase">
                        </div>
                    </div>
                </div>
                <div class="col-auto autocomplete px-1 mb-2">
                    <div class="row mx-0 mb-1">
                        <div class="col px-0">
                            <textarea readonly id="brand_t" class="text-sm text-secondary border-0 w-100" style="height: 100px;"></textarea>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col px-0">
                            <label class="text-sm">Brand</label>
                            <input name="brand_c" id="brand_c" class="float-end border-bottom border-0 mb-2 text-center" maxlength="2" style="width: 30px; text-transform:uppercase"><br>
                            <input name="brand" id="brand" class="border-bottom border-0 mb-1" style="text-transform:uppercase">
                        </div>
                    </div>
                </div>
                <div class="col-auto autocomplete px-1 mb-2">
                    <div class="row mx-0 mb-1">
                        <div class="col px-0">
                            <textarea readonly id="model_t" class="text-sm text-secondary border-0 w-100" style="height: 100px;"></textarea>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col px-0">
                            <label class="text-sm">Model</label>
                            <input name="model_c" id="model_c" class="float-end border-bottom border-0 mb-2 text-center" maxlength="2" style="width: 30px; text-transform:uppercase"><br>
                            <input name="model" id="model" class="border-bottom border-0" style="text-transform:uppercase">
                        </div>
                    </div>
                </div>
                <div class="col-auto autocomplete px-1 mb-2">
                    <div class="row mx-0 mb-1">
                        <div class="col px-0">
                            <textarea readonly id="varian1_t" class="text-sm text-secondary border-0 w-100" style="height: 100px;"></textarea>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col px-0">
                            <label class="text-sm">Varian 1</label>
                            <input name="varian1_c" id="varian1_c" class="float-end border-bottom border-0 mb-2 text-center" maxlength="2" style="width: 30px; text-transform:uppercase"><br>
                            <input name="varian1" id="varian1" class="border-bottom border-0" style="text-transform:uppercase">
                        </div>
                    </div>
                </div>
                <div class="col-auto autocomplete px-1 mb-2">
                    <div class="row mx-0 mb-1">
                        <div class="col px-0">
                            <textarea readonly id="varian2_t" class="text-sm text-secondary border-0 w-100" style="height: 100px;"></textarea>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col px-0">
                            <label class="text-sm">Varian 2</label>
                            <input name="varian2_c" id="varian2_c" class="float-end border-bottom border-0 mb-2 text-center" maxlength="2" style="width: 30px; text-transform:uppercase"><br>
                            <input name="varian2" id="varian2" class="border-bottom border-0" style="text-transform:uppercase">
                        </div>
                    </div>
                </div>
                <div class="col mb-2 mt-auto">
                    <div class="mb-2">
                        <input name="sn" class="form-check-input" type="checkbox" value="1">
                        <label class="form-check-label" for="flexCheckDefault">
                            SN
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
                    <td class="text-secondary">
                        <?= strtoupper($a['grup'] . " " . $a['tipe']) ?>
                    </td>
                    <td>
                        <?= strtoupper($a['brand'] . " " . $a['model'] . " " . $a['varian1'] . " " . $a['varian2']) ?>
                    </td>
                    <td>
                        <?= $a['sn'] == 1 ? "SN-<b>YES</b>" : "SN-NO" ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/autocomplete.js"></script>

<script>
    var grup = JSON.parse('<?= json_encode($data['grup']) ?>');
    var grup_list = JSON.parse('<?= json_encode(array_column($data['grup'], 'nama')) ?>');
    var tipe = JSON.parse('<?= json_encode($data['tipe']) ?>');
    var tipe_list = JSON.parse('<?= json_encode(array_column($data['tipe'], 'nama')) ?>');
    var brand = JSON.parse('<?= json_encode($data['brand']) ?>');
    var brand_list = JSON.parse('<?= json_encode(array_column($data['brand'], 'nama')) ?>');

    $(document).ready(function() {
        autocomplete(document.getElementById("grup"), grup_list);
        autocomplete(document.getElementById("tipe"), tipe_list);
        autocomplete(document.getElementById("brand"), brand_list);
        list_gtb();
    });

    var grup_name, tipe_name, brand_name, model_name, varian1_name;
    var gtb, model_c;
    var model = [];
    var varian1 = [];

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
        var new_grup_name = $('#grup').val().toUpperCase();
        if (new_grup_name != grup_name) {
            grup_name = new_grup_name;
            $('#grup_c').val('');
            for (var key in grup) {
                if (grup[key].nama == grup_name) {
                    console.log(grup_name);
                    $('#grup_c').val(grup[key].id)
                }
            }
        }

        var new_tipe_name = $('#tipe').val().toUpperCase();
        if (new_tipe_name != tipe_name) {
            tipe_name = new_tipe_name;
            $('#tipe_c').val('');
            for (var key in tipe) {
                if (tipe[key].nama == tipe_name) {
                    console.log(tipe_name);
                    $('#tipe_c').val(tipe[key].id)
                }
            }
        }

        var new_brand_name = $('#brand').val().toUpperCase();
        if (new_brand_name != brand_name) {
            brand_name = new_brand_name;
            $('#brand_c').val('');
            for (var key in brand) {
                if (brand[key].nama == brand_name) {
                    console.log(brand_name);
                    $('#brand_c').val(brand[key].id)
                }
            }
        }

        var new_model_name = $('#model').val().toUpperCase();
        if (new_model_name != model_name) {
            model_name = new_model_name;
            $('#model_c').val('');
            for (var key in model) {
                if (model[key].nama == model_name) {
                    console.log(model_name);
                    $('#model_c').val(model[key].id)
                }
            }
        }

        var new_varian1_name = $('#varian1').val().toUpperCase();
        if (new_varian1_name != varian1_name) {
            varian1_name = new_varian1_name;
            $('#varian1_c').val('');
            for (var key in varian1) {
                if (varian1[key].nama == varian1_name) {
                    console.log(varian1_name);
                    $('#varian1_c').val(model[key].id)
                }
            }
        }
    }, 500);

    setInterval(function() {
        var new_gtb = $('#grup_c').val() + $('#tipe_c').val() + $('#brand_c').val();

        if (new_gtb != gtb) {
            gtb = new_gtb;
            if (gtb.length == 6) {
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Master_Barang/load/' + gtb + '/master_model/code_gtb',
                    dataType: "json",
                    data: {},
                    success: function(res) {
                        model = res;
                        const arrayColumn = (array, column) => {
                            return array.map(item => item[column]);
                        };
                        const model_list = arrayColumn(model, 'nama');
                        autocomplete(document.getElementById("model"), model_list);

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
                    url: '<?= PV::BASE_URL ?>Master_Barang/load/' + model_c + '/master_varian1/code_model',
                    dataType: "json",
                    data: {},
                    success: function(res) {
                        varian1 = res;
                        const arrayColumn = (array, column) => {
                            return array.map(item => item[column]);
                        };
                        const varian1_list = arrayColumn(varian1, 'nama');
                        autocomplete(document.getElementById("varian1"), varian1_list);

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