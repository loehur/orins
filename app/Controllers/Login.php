<?php
class Login extends Controller
{
    public function index()
    {
        $_SESSION['secure']['encryption'] = "j499uL0v3ly&N3lyL0vEly_F0r3ver";
        $this->cek_cookie();
        if (isset($_SESSION['login_orins'])) {
            if ($_SESSION['login_orins'] == TRUE) {
                header('Location: ' . PV::BASE_URL . "Home");
            } else {
                $this->view('Pre_login/login');
            }
        } else {
            $this->view('Pre_login/login');
        }
    }

    public function cek_login()
    {
        $_SESSION['pre_log'] = false;

        $hp = $_POST['HP'];
        $c = $_POST['c_'];
        $token = $_POST['token_'];

        $token_ = $this->model("Enc")->dec_2(PV::LOGIN_KEY);

        if ($c <> $_SESSION['captcha']) {
            $this->model('Log')->write($hp . " PRE Login Failed, INVALID CAPTCHA");
            $this->view('Pre_login/login', "INVALID CAPTCHA");
            exit();
        }

        $match = true;

        if ($match == true) {
            if ($token <> $token_) {
                $this->model('Log')->write($hp . " INVALID SECRET KEY");
                $this->view('Pre_login/login', "INVALID SECRET KEY");
                exit();
            } else {
                $_SESSION['pre_log'] = true;
                $this->model('Log')->write($hp . " PRE Login Success");
                echo "<script>window.location.href = '" . PV::BASE_URL . "Login_99/index/" . $hp . "';</script>";
            }
        } else {
            $this->model('Log')->write($hp . " PRE Login Failed, INVALID NUMBER");
            $this->view('Pre_login/login', "INVALID NUMBER");
            exit();
        }
    }

    public function captcha()
    {
        $random_alpha = md5(rand());
        $captcha_code = substr($random_alpha, 0, 4);
        $_SESSION['captcha'] = $captcha_code;

        $target_layer = imagecreatetruecolor(45, 24);
        $captcha_background = imagecolorallocate($target_layer, 255, 160, 199);
        imagefill($target_layer, 0, 0, $captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        imagestring($target_layer, 5, 5, 5, $captcha_code, $captcha_text_color);
        header("Content-type: image/jpeg");
        imagejpeg($target_layer);
    }
}
