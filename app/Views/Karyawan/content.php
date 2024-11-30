<div class="card mx-1 my-1 bg-light">
    <div class="card-header ">Karyawan
        <button type="button" class="float-end btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
    </div>
    <div class="card-body py-1">
        <?php
        foreach ($data['main'] as $a) { ?>
            <div class="row mb-1 border rounded py-1 bg-white">
                <div class="col col-t">
                    <b><span class="edit" data-col="nama" data-id="<?= $a['id_karyawan'] ?>"><?= ucwords($a['nama']) ?></span></b>
                    <br>
                    <small>
                        ID. <?= $a['id_karyawan'] ?>
                        <a class="delete" data-id="<?= $a['id_karyawan'] ?>" data-nama="<?= $a['nama'] ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a>
                    </small>
                </div>
                <div class="col col-t">
                    <small>
                        Registered<br>
                        <?= substr($a['insertTime'], 0, 10) ?>
                    </small>
                </div>
            </div>
        <?php }
        ?>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Karyawan/add" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label" required>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
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

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(result) {
                content();
            },
        });
    });

    $("a.delete").click(function() {
        var nama = $(this).attr("data-nama");
        if (confirm("Yakin Menonaktifkan " + nama + "?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL ?>Karyawan/delete",
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
    $("span.edit").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr('data-id');
        var col = $(this).attr('data-col');
        var value = $(this).html();
        var value_before = value;
        var span = $(this);
        span.html("<input type='text' id='value_3313' style='text-align:center;width:200px' value='" + value + "'>");

        $("#value_3313").focus();
        $("#value_3313").focusout(function() {
            var value_after = $(this).val();
            if (value_after == value_before) {
                span.html(value_before);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= PV::BASE_URL . $data['_c'] ?>/updateCell',
                    data: {
                        'id': id,
                        'value': value_after,
                        'col': col
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 0) {
                            content();
                        } else {
                            alert(res);
                        }
                    },
                });
            }
        });
    });
</script>