<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/dataTables/jquery.dataTables.css" rel="stylesheet" />
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4">
        <div class="card mt-n10">
            <div class="card-header">
                Master Barang
                <button type="button" class="float-end btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
            </div>
            <div class="card-body">
                <table class="table table-sm" id="table_barang">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th class="text-end">Harga Umum</th>
                            <th class="text-end">Harga Dealer</th>
                            <th>SN</th>
                            <th>Grup</th>
                        </tr>
                    </thead>
                    <?php
                    foreach ($data['barang'] as $a) { ?>
                        <tr>
                            <td>
                                <?= $a['id'] ?>
                            </td>
                            <td>
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="kode" data-tb="master_barang" data-tipe="text"><?= strtoupper($a['kode']) ?></span>
                            </td>
                            <td>
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="nama" data-tb="master_barang" data-tipe="text"><?= strtoupper($a['nama']) ?></span>
                            </td>
                            <td class="text-end">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_1" data-tb="master_barang" data-tipe="number"><?= $a['harga_1'] ?></span>
                            </td>
                            <td class="text-end">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_2" data-tb="master_barang" data-tipe="number"><?= $a['harga_2'] ?></span>
                            </td>
                            <td>
                                <?= $a['sn'] ?>
                            </td>
                            <td>
                                <?php foreach ($data['grup'] as $dg) { ?>
                                    <?php if ($dg['id'] == $a['id_grup']) { ?>
                                        <?= $dg['nama'] ?>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </table>
            </div>
        </div>
    </div>
</main>


<div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Master_Barang/add" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" required>Kode Barang</label>
                                <input type="text" name="kode" style="text-transform:uppercase" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" required>Nama</label>
                                <input type="text" name="nama" style="text-transform:uppercase" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" required>Harga Umum</label>
                                <input type="number" name="harga_1" class="form-control text-end" min="0" value="0">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" required>Harga Dealer</label>
                                <input type="number" name="harga_2" class="form-control text-end" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label" required>Grup</label>
                                <select name="id_grup" class="form-select" required>
                                    <option value=""></option>
                                    <?php foreach ($data['grup'] as $dg) { ?>
                                        <option value="<?= $dg['id'] ?>"><?= $dg['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col pt-5">
                            <input class="form-check-input" name="sn" type="checkbox" value="1">
                            <label class="form-check-label" for="flexCheckDefault">
                                Serial Number
                            </label>
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

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/dataTables/jquery.dataTables.js"></script>

<script>
    $(document).ready(function() {
        new DataTable('#table_barang', {
            lengthChange: false,
            searching: true,
            ordering: false,
            pagingType: "simple",
            pageLength: 5
        });
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
        var el = $(this);
        var width = el.parent().width();
        var align = "left";
        if (tipe == "number") {
            align = "right";
        }

        el.parent().css("width", width);
        el.html("<input required type=" + tipe + " style='outline:none;border:none;width:" + width + ";text-align:" + align + "' id='value_' value='" + value + "'>");

        $("#value_").focus();
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
                        if (res == 0) {
                            content()
                        } else {
                            alert(res)
                        }
                    },
                });
            }
        });
    });
</script>