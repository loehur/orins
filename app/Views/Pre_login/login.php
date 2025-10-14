<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Orins | PRE LOGIN</title>
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
$_SESSION['pre_log'] = false;
$failed = "";
if (!is_array($data)) {
    $failed = $data;
}
?>

<body class="bg-info">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <div class="container-sm px-4" style="max-width: 400px;">
                <!-- Basic login form-->
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-body login-card-body">
                        <p class="login-box-msg text-center"><b>ORINS PRE LOGIN</b></p>
                        <div id="info" class="text-danger pb-2 float-end"><?= $failed ?></div>
                        <form action="<?= PV::BASE_URL ?>Login/cek_login" method="post">
                            <div class="row">
                                <div class="col">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                                        <input type="text" name="HP" class="form-control" placeholder="User Login" autocomplete="on" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                                        <input type="text" name="token_" class="form-control" placeholder="Secret Key" required autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-group mb-3">
                                        <input type="text" name="c_" class="form-control" placeholder="Captcha Code" required autocomplete="off">
                                        <span class="input-group-text" id="basic-addon2"><img class="rounded" src="<?= PV::BASE_URL ?>Login/captcha" alt="captcha" /></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <span id="span_loader" class="loader d-none"></span>
                                    <button type="submit" id="btnSubmit" onclick="hide()" class="btn w-100 bg-gradient btn-warning btn-block">Pre Log In</button>
                                </div>
                            </div>
                        </form>
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
        var pass = document.querySelector('[name="token_"]').value;
        var cap = document.querySelector('[name="c_"]').value;

        if (input.length < 1 || pass.length < 1 || cap.length < 1) {
            return;
        }

        var element = document.getElementById("span_loader");
        element.classList.remove("d-none");

        document.getElementById('btnSubmit').style.visibility = 'hidden';
    }
</script>