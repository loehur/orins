<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mt-n10">
            <div class="card-header ">Produk
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
                                        <span class="text-success"><b><?= $a['produk'] ?></b></span>

                                        <?php if ($c_spk == 0 && $c_detail == 0) { ?>
                                            <span style="cursor: pointer;" data-id="<?= $id_produk ?>" class="deleteProduk text-danger"><i class="fa-regular fa-circle-xmark"></i></span>
                                        <?php } ?>
                                        <br>
                                        <span onclic k="chgActionEdit(<?= $id_produk ?>,'<?= $a['produk'] ?>')" data-bs-toggle="modal" data-bs-target="#exampleModalEdit" class="text-primary" style="cursor: pointer;"><i class="fa-regular fa-pen-to-square"></i></span> <?= $detail ?>
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
                                            echo "<b>" . $this->model("Arr")->get($this->dDvs, "id_divisi", "divisi", $sd['id_divisi']) . "</b> " . $sd['cm'] + 1 . " Step";
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
    </div>
</main>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $this->BASE_URL ?>Produk/add" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="produk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Detail yang diperlukan</label>
                        <select class="form-select" name="detail[]" multiple aria-label="multiple select example" required>
                            <?php foreach ($data['detail'] as $d) { ?>
                                <option value="<?= $d['id_index'] ?>"><?= $d['detail_group'] ?></option>
                            <?php  }
                            ?>
                        </select>
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
            <form id="produk_edit" action="<?= $this->BASE_URL ?>Produk/edit" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="produk" id="input_produk_edit" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Detail yang diperlukan</label>
                        <select class="form-select" name="detail[]" multiple aria-label="multiple select example" required>
                            <?php foreach ($data['detail'] as $d) { ?>
                                <option value="<?= $d['id_index'] ?>"><?= $d['detail_group'] ?></option>
                            <?php  }
                            ?>
                        </select>
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
            <form id="addSPK" action="<?= $this->BASE_URL ?>Produk/add_spk" method="POST">
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
                                    Complete Marker
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
            <form action="<?= $this->BASE_URL ?>Produk/add_componen_harga" method="POST">
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

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    var addItemAction = $("form#addSPK").attr('action');
    var actionEdit = $("form#produk_edit").attr('action');

    function chgAction(id_produk) {
        var newAction = addItemAction + "/" + id_produk;
        $('form#addSPK').attr('action', newAction);
        $("div.cekDetail").load('<?= $this->BASE_URL ?>Produk/load_detail/' + id_produk);
    }

    $(".addHarga").click(function() {
        var id_produk = $(this).attr("data-id");
        $("input[name=id_produk_harga]").val(id_produk);
        $("div.cekDetail").load('<?= $this->BASE_URL ?>Produk/load_detail/' + id_produk);
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
                url: "<?= $this->BASE_URL ?>Produk/delete_item",
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
                url: "<?= $this->BASE_URL ?>Produk/delete_detail",
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
                url: "<?= $this->BASE_URL ?>Produk/delete_produk",
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
</script>