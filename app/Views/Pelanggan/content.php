<?php
$pelanggan_jenis = "";
$id_pelanggan_jenis = $data['id_jenis_pelanggan'];

if ($id_pelanggan_jenis == 1) {
    $pelanggan_jenis = "Umum";
} else {
    $pelanggan_jenis = "Rekanan";
}
?>

<main>
    <div class="card mx-1 my-1 bg-light">
        <div class="card-header ">Pelanggan <b><?= $pelanggan_jenis ?></b>
            <button type="button" class="float-end btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
        </div>
        <div class="card-body py-1">
            <?php
            foreach ($data['pelanggan'] as $a) { ?>
                <div class="row mb-1 border rounded py-1 bg-white">
                    <div class="col col-t">
                        <?= ucwords($a['nama']) ?><br>
                        <small><?= ucwords($a['no_hp']) ?></small>
                        <?php if ($id_pelanggan_jenis == 2) { ?>
                            <br><?= ucwords($a['usaha']) ?> - <?= ucfirst($a['alamat']) ?>
                        <?php } ?>
                    </div>
                    <div class="col col-t">
                        <small>
                            ID. <?= $a['id_pelanggan'] ?>
                            <a class="delete" data-id="<?= $a['id_pelanggan'] ?>" data-nama="<?= $a['nama'] ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a>
                        </small>
                        <br>
                        <small>Registered: <?= substr($a['insertTime'], 0, 10) ?></small>
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
            <form action="<?= $this->BASE_URL ?>Pelanggan/add/<?= $id_pelanggan_jenis ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" required>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" required>Nomor Handphone</label>
                        <input type="text" name="hp" class="form-control" required>
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

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

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
                url: "<?= $this->BASE_URL . $data['_c'] ?>/delete",
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