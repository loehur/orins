<?php

class Gudang_Stok extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[101])) {
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
         "title" => "Gudang Stok"
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
      $this->view($this->v_content, $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   public function print()
   {
      $data['stok'] = $this->data('Barang')->stok_data_list_all($this->userData['id_toko']);
      $data['stok_gudang'] = $this->data('Barang')->stok_data_list_all(0);
      $data['barang'] = $this->db(0)->get_where('master_barang', 'en = 1 ORDER tipe ASC, brand ASC, CONCAT(model, product_name) ASC', 'tipe', 1);
      $data['grup'] = $this->db(0)->get('master_grup');
      $data['tipe'] = $this->db(0)->get('master_tipe');
      $data['brand'] = $this->db(0)->get('master_brand');
      $this->view(__CLASS__ . "/print", $data);
   }
}
