<header class="py-5 mb-4 bg-gradient-primary-to-secondary">
    <div class="container-xl px-4">
        <div class="text-center">
            <h1 class="text-white">Account Setting</h1>
        </div>
    </div>
</header>
<!-- Main page content-->
<div class="row ms-2">
    <div class="col-auto me-auto">
        <form id="form" action="<?= PV::BASE_URL ?>Akun/updatePass" method="post">
            <div class="row mb-2">
                <div class="col">
                    <label>Password Lama</label>
                    <input type="password" class="form-control form-control-sm" name="pass" required>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label>Password Baru</label>
                    <input type="password" class="form-control form-control-sm" name="pass_" required>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label>Ulangi Password Baru</label>
                    <input type="password" class="form-control form-control-sm" name="pass__">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <button type="submit" class="btn btn-primary">
                        Update Password
                    </button>
                </div>
            </div>
        </form>
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
            success: function(res) {
                if (res == 0) {
                    alert("Success! New Password Updated!")
                    location.reload(true);
                } else {
                    alert(res);
                }
            }
        });
    });
</script>