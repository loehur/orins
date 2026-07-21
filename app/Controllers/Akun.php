<?php

class Akun extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      $this->v_load = __CLASS__ . "/load";
      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => __CLASS__
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $pin = isset($this->userData['pin']) ? trim((string)$this->userData['pin']) : '';
      $data['has_pin'] = strlen($pin) > 0;
      $this->view($this->v_content, $data);
   }

   public function updatePass()
   {
      $pass = $_POST['pass'];
      $pass_ = $_POST['pass_'];
      $pass__ = $_POST['pass__'];

      if ($pass_ <> $pass__) {
         echo "Password Baru tidak Cocok";
         exit();
      }

      if ($this->model('Enc')->enc($pass) <> $this->userData['password']) {
         echo "Password Lama Salah!";
         exit();
      }

      $new = $this->model('Enc')->enc($pass_);

      $where = "id_user = '" . $this->userData['id_user'] . "'";
      $set = "password = '" . $new . "'";
      $update = $this->db(0)->update("user", $set, $where);
      echo $update['errno'];
   }

   public function generatePin()
   {
      header('Content-Type: application/json; charset=utf-8');

      $pass = $_POST['pass'] ?? '';
      if ($pass === '') {
         echo json_encode(['ok' => 0, 'error' => 'Password wajib diisi.']);
         exit();
      }

      if ($this->model('Enc')->enc($pass) <> $this->userData['password']) {
         echo json_encode(['ok' => 0, 'error' => 'Password salah!']);
         exit();
      }

      $pin = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
      $pinEnc = $this->model('Enc')->enc($pin);
      $where = "id_user = '" . $this->userData['id_user'] . "'";
      $set = "pin = '" . $pinEnc . "'";
      $update = $this->db(0)->update("user", $set, $where);
      if ($update['errno'] <> 0) {
         echo json_encode(['ok' => 0, 'error' => $update['error']]);
         exit();
      }

      $_SESSION['user_data']['pin'] = $pinEnc;
      $this->userData['pin'] = $pinEnc;
      $this->model('Log')->write($this->userData['user'] . " Generate PIN Success");
      echo json_encode(['ok' => 1, 'pin' => $pin]);
   }
}
