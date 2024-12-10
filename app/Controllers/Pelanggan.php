<?php

class Pelanggan extends Controller
{
   public $main_table = "pelanggan";

   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[3])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($jenis_pelanggan = 1)
   {
      if ($jenis_pelanggan == 1) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Pelanggan - Umum"
         ]);
      } elseif ($jenis_pelanggan == 2) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Pelanggan - Rekanan"
         ]);
      } elseif ($jenis_pelanggan == 3) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Pelanggan - Online"
         ]);
      }
      $this->viewer($jenis_pelanggan);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "1")
   {
      $where = "id_toko = " . $this->userData['id_toko'] . " AND en = 1 AND id_pelanggan_jenis = " . $parse;
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $where);
      $data['id_jenis_pelanggan'] = $parse;
      $data["_c"] = __CLASS__;
      $this->view($this->v_content, $data);
   }

   function add($id_pelanggan_jenis)
   {
      $hp = $_POST['hp'];
      $nama = $_POST['nama'];

      if ($id_pelanggan_jenis == 2) {
         $usaha = $_POST['usaha'];
         $alamat = $_POST['alamat'];
         $cols = 'id_toko, nama, no_hp, usaha, alamat, id_pelanggan_jenis';
         $vals = "'" . $this->userData['id_toko'] . "','" . $nama . "','" . $hp . "','" . $usaha . "','" . $alamat . "'," . $id_pelanggan_jenis;
      } else {
         $cols = 'id_toko, nama, no_hp, id_pelanggan_jenis';
         $vals = "'" . $this->userData['id_toko'] . "','" . $nama . "','" . $hp . "'," . $id_pelanggan_jenis;
      }

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND nama = '" . $nama . "' AND id_pelanggan_jenis = " . $id_pelanggan_jenis;
      $dataCount = $this->db(0)->count_where('pelanggan', $whereCount);
      if ($dataCount < 1) {
         $do = $this->db(0)->insertCols('pelanggan', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add Pelanggan Success!");
            echo 0;
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add Pelanggan Failed, Double Forbidden!");
         echo "Pelanggan dengan Nama/Nomor tersebut sudah Ada!";
      }
   }

   public function delete()
   {
      $id = $_POST['id'];
      $set = "en = 0";
      $where = "id_pelanggan = " . $id;
      $update = $this->db(0)->update("pelanggan", $set, $where);
      echo $update['errno'];
      $this->dataSynchrone();
   }

   public function updateCell()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];
      $col = $_POST['col'];

      $set = $col . " = '" . $value . "'";
      $where = "id_pelanggan = " . $id;
      $update = $this->db(0)->update($this->main_table, $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }
}
