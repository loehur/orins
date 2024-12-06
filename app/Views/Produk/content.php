<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<main class="container">
    <div class="card shadow-sm">
        <div class="card-header ">
            <button type="button" class="float-end btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
        </div>
        <div class="card-body">
            <small>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Komponen Harga</th>
                            <th>SPK Divisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data['produk'] as $a) {
                            $id_produk = $a['id_produk'];
                            $detail = "";
                            foreach (unserialize($a['produk_detail']) as $key => $pd) {
                                foreach ($data['detail'] as $d) {
                                    if ($pd == $d['id_index'])
                                        $detail .= "<span class='text-secondary'>" . $key . "</span>#" . $d['detail_group'] . ", ";
                                }
                            }
                            $spk_dvs = $a['spk_dvs'];
                            $produk_detail = $a['detail'];
                            $c_spk = count($spk_dvs);
                            $c_detail = count($produk_detail);
                        ?>
                            <tr>
                                <td>
                                    <span class="text-success fw-bold cell_edit" data-id="<?= $a['id_produk'] ?>" data-col="produk" data-tipe="text" data-primary="id_produk" data-tb="produk"><?= $a['produk'] ?></span>

                                    <?php if ($c_spk == 0 && $c_detail == 0) { ?>
                                        <span style="cursor: pointer;" data-id="<?= $id_produk ?>" class="deleteProduk text-danger"><i class="fa-regular fa-circle-xmark"></i></span>
                                    <?php } ?>
                                    <br>
                                    <span onclick="chgActionEdit(<?= $id_produk ?>,'<?= $a['produk'] ?>')" data-bs-toggle="modal" data-bs-target="#exampleModalEdit" class="text-primary" style="cursor: pointer;"><i class="fa-regular fa-pen-to-square"></i></span> <?= $detail ?>
                                </td>
                                <td>
                                    <button type="button" class="border rounded bg-white addHarga" data-id="<?= $id_produk ?>" data-bs-toggle="modal" data-bs-target="#add_harga">+</button>
                                    <br>
                                    <?php
                                    foreach ($produk_detail as $sd) { ?>
                                        <span style="cursor: pointer;" data-id="<?= $sd['id_produk_detail'] ?>" class="deleteDetail text-danger"><i class=" fa-regular fa-circle-xmark"></i></span>
                                    <?php
                                        $details = unserialize($sd['detail']);
                                        foreach ($details as $dg) {
                                            echo $this->model("Arr")->get($this->dDetailGroup, "id_index", "detail_group", $dg) . ", ";
                                        }
                                        echo "<br>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button onclick="chgAction(<?= $id_produk ?>)" type="button" class="border rounded bg-white" data-bs-toggle="modal" data-bs-target="#setSPK">+ / Edit</button>
                                    <br>
                                    <?php
                                    foreach ($spk_dvs as $sd) { ?>
                                        <span style="cursor: pointer;" data-id="<?= $sd['id_spk_dvs'] ?>" class="deleteItem text-danger"><i class=" fa-regular fa-circle-xmark"></i></span>
                                    <?php
                                        echo "<b>" . $this->model("Arr")->get($data['divisi'], "id_divisi", "divisi", $sd['id_divisi']) . "</b> " . $sd['cm'] + 1 . " Step";
                                        $detailGroups = unserialize($sd['detail_groups']);
                                        echo " - ";
                                        foreach ($detailGroups as $dg) {
                                            echo $this->model("Arr")->get($this->dDetailGroup, "id_index", "detail_group", $dg) . ", ";
                                        }
                                        echo "<br>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php }
                        ?>
                    </tbody>
                </table>
            </small>
        </div>
    </div>
</main>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Produk/add/<?= $data['parse'] ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="produk" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label">Detail yang diperlukan</label>
                        <div class="col">
                            <select name="detail[]" multiple class="tize border-0 w-100" required id="barang">
                                <option></option>
                                <?php foreach ($data['detail'] as $br) { ?>
                                    <option value="<?= $br['id_index'] ?>"><?= $br['detail_group'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Merubah Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="produk_edit" action="<?= PV::BASE_URL ?>Produk/edit" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="produk" id="input_produk_edit" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label">Detail yang diperlukan</label>
                        <div class="col">
                            <select name="detail[]" multiple class="tize border-0 w-100" required id="barang">
                                <option></option>
                                <?php foreach ($data['detail'] as $br) { ?>
                                    <option value="<?= $br['id_index'] ?>"><?= $br['detail_group'] ?> <?= $br['note'] <> "" ? "(" . $br['note'] . ")" : "" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="setSPK" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah SPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSPK" action="<?= PV::BASE_URL ?>Produk/add_spk" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Divisi</label>
                        <select class="form-select" name="divisi" aria-label="select example" required>
                            <option></option>
                            <?php foreach ($this->dDvs as $dv) { ?>
                                <option value="<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></option>
                            <?php  }
                            ?>
                        </select>
                    </div>
                    <div class="row cekDetail"></div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" name="cm" type="checkbox" value="1">
                                <label class="form-check-label">
                                    2 Steps
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah/Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add_harga" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah Komponen Harga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Produk/add_componen_harga" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_produk_harga" value="">
                    <div class="row cekDetail"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    var addItemAction = $("form#addSPK").attr('action');
    var actionEdit = $("form#produk_edit").attr('action');

    function chgAction(id_produk) {
        var newAction = addItemAction + "/" + id_produk;
        $('form#addSPK').attr('action', newAction);
        $("div.cekDetail").load('<?= PV::BASE_URL ?>Produk/load_detail/' + id_produk);
    }

    $(".addHarga").click(function() {
        var id_produk = $(this).attr("data-id");
        $("input[name=id_produk_harga]").val(id_produk);
        $("div.cekDetail").load('<?= PV::BASE_URL ?>Produk/load_detail/' + id_produk);
    })

    function chgActionEdit(id_produk, name) {
        var newAction = actionEdit + "/" + id_produk;
        $('form#produk_edit').attr('action', newAction);
        $("#input_produk_edit").val(name);
    }

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            },
        });
    });

    $("span.deleteItem").click(function() {
        if (confirm("Yakin Hapus?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL ?>Produk/delete_item",
                data: {
                    id: id
                },
                type: "POST",
                success: function(res) {
                    if (res == 0) {
                        content();
                    } else {
                        alert(res);
                    }
                },
            });
        } else {
            return false;
        }
    });

    $("span.deleteDetail").click(function() {
        if (confirm("Yakin Hapus?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL ?>Produk/delete_detail",
                data: {
                    id: id
                },
                type: "POST",
                success: function(res) {
                    if (res == 0) {
                        content();
                    } else {
                        alert(res);
                    }
                },
            });
        } else {
            return false;
        }
    });

    $("span.deleteProduk").click(function() {
        if (confirm("Yakin Hapus?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL ?>Produk/delete_produk",
                data: {
                    id: id
                },
                type: "POST",
                success: function(res) {
                    if (res == 0) {
                        content();
                    } else {
                        alert(res);
                    }
                },
            });
        } else {
            return false;
        }
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
        el.html("<input required type=" + tipe + " style='outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

        $("#value_").focus();
        $('#value_').keypress(function(e) {
            if (e.which == 13) {
                $(this).blur();
            }
        });
        $("#value_").focusout(function() {
            var value_after = $(this).val();
            if (value_after == "") {
                value_after = "[ ]";
            }
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