<?php
$user_ = "";
if ($data['user_tipe'] == 2) {
    $user_ = "Kasir";
} else {
    $user_ = "Oprasional";
}
?>

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
        <div class="card mt-n10" style="max-width: 500px;">
            <div class="card-header ">User <b><?= $user_ ?></b>
                <button type="button" class="float-end btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
            </div>
            <div class="card-body">

                <?php
                foreach ($data['user'] as $a) { ?>
                    <?= $a['nama'] ?> ID : [ <?= $a['id_user'] ?> ]<br>
                    Username : <b><?= $a['user'] ?></b>
                    <button type="button" data-id="<?= $a['id_user'] ?>" class="float-end btn btn-sm btn-outline-primary resetPass">Reset Password</button> <br>
                    <small>Default Password : 123</small>
                    <hr>
                <?php }
                ?>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah User <?= $user_ ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>User/add/<?= $data['user_tipe'] ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label" required>Username</label>
                        <input type="text" name="hp" class="form-control" required>
                    </div>
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

    $(".resetPass").click(function() {
        var id = $(this).attr('data-id');

        $.ajax({
            url: '<?= PV::BASE_URL ?>Functions/resetPass',
            data: {
                'id': id,
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    alert("Reset password success");
                } else {
                    alert(res);
                }
            },
        });
    })
</script>