<?php
$pelanggan_jenis = "";
$id_pelanggan_jenis = $data['id_jenis_pelanggan'];

if ($id_pelanggan_jenis == 1) {
    $pelanggan_jenis = "Umum";
} else if ($id_pelanggan_jenis == 2) {
    $pelanggan_jenis = "Rekanan";
} else {
    $pelanggan_jenis = "Online";
}
?>

<main>
    <div class="card mx-1 my-1 bg-light">
        <div class="card-header ">Pelanggan <b><?= $pelanggan_jenis ?></b>
            <?php if ($id_pelanggan_jenis <> 3) { ?>
                <button type="button" class="float-end btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
            <?php } ?>
        </div>
        <div class="card-body py-1">
            <?php
            foreach ($data['pelanggan'] as $a) { ?>
                <div class="row mb-1 border rounded py-1 bg-white">
                    <div class="col col-t">
                        <b><span class="edit" data-col="nama" data-id="<?= $a['id_pelanggan'] ?>"><?= ucwords($a['nama']) ?></span></b><br>
                        <small>
                            ID. <?= $a['id_pelanggan'] ?>
                            <a class="delete" data-id="<?= $a['id_pelanggan'] ?>" data-nama="<?= $a['nama'] ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a>
                        </small>
                        <br>
                        <small>Registered: <?= substr($a['insertTime'], 0, 10) ?></small>
                    </div>
                    <div class="col col-t">
                        <small>Contact:</small> <span class="edit" data-col="no_hp" data-id="<?= $a['id_pelanggan'] ?>"><?= ucwords($a['no_hp']) ?></span>
                        <?php if ($id_pelanggan_jenis == 2) { ?>
                            <br>
                            <small>Usaha:</small> <span class="edit" data-col="usaha" data-id="<?= $a['id_pelanggan'] ?>"><?= ucwords($a['usaha']) ?></span>
                            <br>
                            <small>Alamat:</small> <span class="edit" data-col="alamat" data-id="<?= $a['id_pelanggan'] ?>"><?= ucfirst($a['alamat']) ?></span>
                        <?php } else { ?>
                            <br>
                            <small>Usaha:</small> -
                            <br>
                            <small>Alamat:</small> -
                        <?php } ?>
                    </div>
                </div>
            <?php }
            ?>
        </div>
    </div>
</main>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah Pelanggan <b><?= $pelanggan_jenis ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Pelanggan/add/<?= $id_pelanggan_jenis ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" required>Nomor Handphone</label>
                        <input type="text" name="hp" class="form-control">
                    </div>
                    <?php
                    if ($id_pelanggan_jenis == 2) { ?>
                        <div class="mb-3">
                            <label class="form-label">Nama Usaha</label>
                            <input type="text" name="usaha" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Usaha</label>
                            <input type="text" name="alamat" class="form-control">
                        </div>
                    <?php }
                    ?>
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
                if (result == 0) {
                    content();
                } else {
                    alert(result);
                }
            },
        });
    });

    $("a.delete").click(function() {
        var nama = $(this).attr("data-nama");
        if (confirm("Yakin Menonaktifkan " + nama + "?")) {
            var id = $(this).attr("data-id");
            $.ajax({
                url: "<?= PV::BASE_URL . $data['_c'] ?>/delete",
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