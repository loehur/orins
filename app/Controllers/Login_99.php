<?php
class Login_99 extends Controller
{
   public function index($hp = "NULL")
   {
      $this->cek_cookie();

      if ($hp == "") {
         $hp = "NULL";
      }
      if (isset($_SESSION['pre_log'])) {
         if ($_SESSION['pre_log'] == true && isset($hp)) {
            if (isset($_SESSION['login_orins'])) {
               if ($_SESSION['login_orins'] == TRUE) {
                  header('Location: ' . PV::BASE_URL . "Home");
               } else {
                  $this->view('Login/login', ['user' => $hp]);
               }
            } else {
               $this->view('Login/login', ['user' => $hp]);
            }
         } else {
            $this->view('Pre_login/login');
         }
      } else {
         $this->view('Pre_login/login');
      }
   }

   public function cek_login()
   {
      $hp = $_POST["HP"];

      if (strlen(PV::DB_PASS) == 0) {
         $_SESSION['secure']['db_pass'] = "";
      } else {
         $_SESSION['secure']['db_pass'] = $this->model("Enc")->dec_2(PV::DB_PASS);
      }

      if (isset($_SESSION['login_orins'])) {
         if ($_SESSION['login_orins'] == TRUE) {
            header('Location: ' . PV::BASE_URL . "Home");
         }
      }

      $pass = $this->model('Enc')->enc($_POST["PASS"]);
      if (strlen($hp) < 3 || strlen($pass) < 3) {
         $this->model('Log')->write($hp . " Login Failed, Validate");
         $this->view('Login/login',  ['user' => $hp, "failed" => 'Authentication Error']);
         exit();
      }

      $where = "user = '" . $hp . "' AND password = '" . $pass . "'";
      $userData = $this->db(0)->get_where_row('user', $where);

      if (empty($userData)) {
         $this->view('Login/login',  ['user' => $hp, "failed" => 'Authentication Error']);
         $this->model('Log')->write($hp . " Login Failed, Auth");
         exit();
      } else {
         $this->model('Log')->write($hp . " Login Success");
         $this->set_login($userData);
         $this->save_cookie($userData);
         $this->index($userData['user']);
      }
   }

   function save_cookie($userData)
   {
      $device = $_SERVER['HTTP_USER_AGENT'];
      $userData['device'] = $device;
      $cookie_value = $this->model("Enc")->enc_2(serialize($userData));
      setcookie("ORINSESSID", $cookie_value, time() + 86400, "/");
   }


   public function logout()
   {
      if (isset($_SESSION['user_data']['user'])) {
         if (strlen($_SESSION['user_data']['user']) > 0) {
            $this->model('Log')->write($_SESSION['user_data']['user'] . " LOGOUT");
         } else {
            $this->model('Log')->write("FORCE LOGOUT");
         }
      } else {
         $this->model('Log')->write("FORCE LOGOUT");
      }
      session_unset();
      session_destroy();

      if (isset($_COOKIE['ORINSESSID'])) {
         unset($_COOKIE['ORINSESSID']);
         setcookie('ORINSESSID', '', -1, '/');
      }

      header('Location: ' . PV::BASE_URL . "Home");
   }
}
