<?php

class Karyawan extends Controller
{
   public $main_table = "karyawan";
   public $id_data = "id_karyawan";

   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[1])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Karyawan Aktif"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data["_c"] = __CLASS__;
      $where = "en = 1 AND id_toko = " . $this->userData['id_toko'];
      $data['main'] = $this->db(0)->get_where('karyawan', $where);
      $this->view($this->v_content, $data);
   }

   function add()
   {
      $nama = $_POST['nama'];
      $cols = 'id_toko, nama';
      $vals = "'" . $this->userData['id_toko'] . "','" . $nama . "'";

      $whereCount = "id_toko = '" . $this->userData['id_toko'] . "' AND nama = '" . $nama . "'";
      $dataCount = $this->db(0)->count_where('karyawan', $whereCount);
      if ($dataCount < 1) {
         $do = $this->db(0)->insertCols('karyawan', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add Karyawan Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $this->model('Log')->write($this->userData['user'] . " Add Karyawan Failed, Double Forbidden!");
         echo "Double Entry!";
      }
   }

   public function delete()
   {
      $id = $_POST['id'];
      $set = "en = 0";
      $where = "id_karyawan = " . $id;
      $update = $this->db(0)->update("karyawan", $set, $where);
      echo $update['errno'];
      $this->dataSynchrone();
   }

   public function updateCell()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];
      $col = $_POST['col'];

      $set = $col . " = '" . $value . "'";
      $where = $this->id_data . " = " . $id;
      $update = $this->db(0)->update($this->main_table, $set, $where);
      $this->dataSynchrone();
      echo $update['errno'];
   }
}
