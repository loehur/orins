<div class="card mx-1 my-1 bg-light">
    <div class="card-header ">Karyawan
        <button type="button" class="float-end btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
    </div>
    <div class="card-body py-1">
        <?php
        foreach ($data as $a) { ?>
            <div class="row mb-1 border rounded py-1 bg-white">
                <div class="col col-t">
                    <small>
                        ID. <?= $a['id_karyawan'] ?>
                        <a class="delete" data-id="<?= $a['id_karyawan'] ?>" data-nama="<?= $a['nama'] ?>" href="#"><i class="text-danger fa-regular fa-circle-xmark"></i></a>
                    </small>
                    <br>
                    <?= $a['nama'] ?>
                </div>
                <div class="col col-t">
                    <small>Joined</small><br>
                    <?= substr($a['insertTime'], 0, 10) ?>
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
            <form action="<?= $this->BASE_URL ?>Karyawan/add" method="POST">
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

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
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
                url: "<?= $this->BASE_URL ?>Karyawan/delete",
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