<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Orins | LOGIN</title>
    <link href="<?= PV::ASSETS_URL ?>css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="<?= PV::ASSETS_URL ?>assets/img/favicon.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/fontawesome-free-6.4.0-web/css/all.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
    <!-- FONT -->

    <?php $fontStyle = "'Titillium Web', sans-serif;" ?>

    <style>
        html .table {
            font-family: <?= $fontStyle ?>;
        }

        html .content {
            font-family: <?= $fontStyle ?>;
        }

        html body {
            font-family: <?= $fontStyle ?>;
        }

        @media print {
            p div {
                font-family: <?= $fontStyle ?>;
                font-size: 14px;
            }
        }

        html {
            height: 100%;
            background-color: #F4F4F4;
        }

        body {
            min-height: 100%;
        }
    </style>

</head>
<?php
$failed = "";
$user = "";
if (is_array($data)) {
    $user = $data['user'];
    if (isset($data['failed'])) {
        $failed = $data['failed'];
    }
}
?>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <div class="container-sm px-4" style="max-width: 400px;">
                <div class="row justify-content-center">
                    <div class="col">
                        <!-- Basic login form-->
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-body login-card-body">
                                <p class="login-box-msg text-center"><b>ORINS LOGIN</b></p>
                                <div id="info" class="text-danger pb-2 float-end"><?= $failed ?></div>
                                <form action="<?= PV::BASE_URL ?>Login_99/cek_login" method="post">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                                        <input type="text" name="HP" readonly value="<?= $user ?>" class="form-control" placeholder="User Login" required autocomplete="off">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="PASS" class="form-control" placeholder="Password" required autocomplete="off">
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <span id="span_loader" class="loader d-none"></span>
                                            <button type="submit" id="btnSubmit" onclick="hide()" class="btn btn-success bg-gradient w-100 btn-block">Log In</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= PV::ASSETS_URL ?>js/scripts.js"></script>
</body>

</html>

<script>
    function hide() {
        var input = document.querySelector('[name="HP"]').value;
        var pass = document.querySelector('[name="PASS"]').value;
        var cap = document.querySelector('[name="c_"]').value;

        if (input.length < 1 || pass.length < 1 || cap.length < 1) {
            return;
        }

        var element = document.getElementById("span_loader");
        element.classList.remove("d-none");

        document.getElementById('btnSubmit').style.visibility = 'hidden';
    }
</script>