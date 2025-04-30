<?php

class Stok_Bahan_Baku extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[4])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Stok Bahan Baku"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $data['stok'] = $this->data('Barang')->stok_data_list_all($this->userData['id_toko']);
      $data['stok_gudang'] = $this->data('Barang')->stok_data_list_all(0);
      $data['barang'] = $this->db(0)->get_where('master_barang', 'en = 1 ORDER BY id DESC');
      $data['karyawan_toko'] = $this->db(0)->get_where('karyawan', "id_toko = " . $this->userData['id_toko'], 'id_karyawan');
      $this->view($this->v_content, $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   function pakai()
   {
      $id_sumber = $_POST['id_sumber'];
      $id_barang = $_POST['id_barang'];
      $karyawan = $_POST['staf_id'];
      //updateFreqCS
      $this->db(0)->update("karyawan", "freq_pro = freq_pro+1", "id_karyawan = " . $karyawan);

      $id_target = 0;
      $qty = $_POST['qty'];

      $ref = date("YmdHi");
      $cols = 'ref, jenis, id_barang, id_sumber, id_target, qty';

      $vals = "'" . $ref . "',4," . $id_barang . ",'" . $id_sumber . "','" . $id_target . "'," . $qty;
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }
}
