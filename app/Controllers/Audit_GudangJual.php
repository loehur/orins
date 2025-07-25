<?php

class Audit_GudangJual extends Controller
{
   public $title = "Audit - Gudang Jual";
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[5])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => $this->title
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $data['input'] = $this->db(0)->get_where('master_input', "tipe = 3 AND cek = 0 ORDER BY tanggal DESC");
      $data['input_done'] = $this->db(0)->get_where('master_input', "tipe = 3 AND cek <> 0 ORDER BY tanggal DESC LIMIT 50");
      $data['toko'] = $this->db(0)->get_where('pelanggan', "id_pelanggan_jenis = 0", "id_pelanggan");
      $this->view(__CLASS__ . '/content', $data);
   }

   public function list($id)
   {
      $this->view("Layouts/layout_main", [
         "title" => $this->title
      ]);
      $this->viewer($page = "list_data", $id);
   }

   function list_data($id)
   {
      $data['input'] = $this->db(0)->get_where_row('master_input', "id = '" . $id . "'");
      $data['toko'] = $this->db(0)->get_where('pelanggan', "id_pelanggan_jenis = 0", "id_pelanggan");

      $cols = "id, code, code_myob, CONCAT(brand,' ',model) as nama, product_name";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');

      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "ref = '" . $id . "'");
      $this->view(__CLASS__ . '/list_data', $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   function update()
   {
      $ref = $_POST['ref'];
      $up1 = $this->db(0)->update("master_input", "cek = 1", "id = '" . $ref . "'");
      if ($up1['errno'] <> 0) {
         echo $up1['errno'];
         exit();
      } else {
         $up2 = $this->db(0)->update("master_mutasi", "stat = 1", "ref = '" . $ref . "'");
         if ($up2['errno'] <> 0) {
            echo $up2['errno'];
            exit();
         }

         $dateNow = date("Y-m-d H:i:s");
         $up3 = $this->db(0)->update("order_data", "tgl_ambil = '" . $dateNow . "', id_ambil = " . $this->userData['id_user'], "ref = '" . $ref . "'");
         if ($up3['errno'] <> 0) {
            echo $up3['errno'];
            exit();
         }
      }
      echo 0;
   }
}
