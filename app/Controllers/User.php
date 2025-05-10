<?php

class User extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[0])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($user_tipe = 2)
   {
      if ($user_tipe == 2) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "User Kasir"
         ]);
      } elseif ($user_tipe == 3) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "User CS"
         ]);
      } elseif ($user_tipe == 4) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "User Produksi"
         ]);
      } elseif ($user_tipe == 9) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "User Driver"
         ]);
      }
      $this->viewer($user_tipe);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $where = "user_tipe = " . $parse . " AND id_toko = " . $this->userData['id_toko'];
      $data['user'] = $this->db(0)->get_where('user', $where);
      $data['user_tipe'] = $parse;
      $this->view($this->v_content, $data);
   }

   function add($user_tipe = 3)
   {
      $no = $_POST['hp'];
      $nama = $_POST['nama'];
      $pass = $this->model('Enc')->enc("123");
      $cols = 'id_toko, nama, user, password, user_tipe';
      $vals = "'" . $this->userData['id_toko'] . "','" . $nama . "','" . $no . "','" . $pass . "'," . $user_tipe;

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND user = '" . $no . "' AND user_tipe = " . $user_tipe;
      $dataCount = $this->db(0)->count_where('user', $whereCount);
      if ($dataCount <> 1) {
         $do = $this->db(0)->insertCols('user', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add User Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add User Failed, Double Admin Forbidden!");
         echo "Double Entry!";
      }
   }
}
