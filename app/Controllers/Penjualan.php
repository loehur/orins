<?php

class Penjualan extends Controller
{
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

   public function index($parse = "", $parse_2 = "")
   {
      $title = "Data Order - Harian";

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => $title
      ]);

      $this->viewer($parse, $parse_2);
   }

   public function viewer($parse = "", $parse_2 = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse_2" => $parse_2]);
   }

   public function content($parse = "", $parse_2 = "")
   {
      if ($parse == "") {
         $parse = date("Y-m-d");
      }

      $data['parse'] = $parse;

      $data['barang'] = $this->db(0)->get('master_barang', 'id');
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $data['karyawan_toko'] = $this->db(0)->get_where('karyawan', "id_toko = " . $this->userData['id_toko'], 'id_karyawan');

      $where = "insertTime LIKE '" . $parse . "%'";
      $refs_data = $this->db(0)->get_where('ref', $where, 'ref');
      $refs = array_keys($refs_data);
      $data['order'] = [];
      $data['mutasi'] = [];

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $cols = "insertTime, id_penerima, produk, produk_detail, id_pelanggan, ref";
         $where = "ref IN (" . $ref_list . ") AND id_toko = " . $this->userData['id_toko'] . " AND cancel = 0 GROUP BY ref";
         $data['order'] = $this->db(0)->get_cols_where('order_data', $cols, $where, 1, 'ref');

         $cols = "insertTime, cs_id, id_barang, id_target, ref";
         $where = "ref IN (" . $ref_list . ") AND id_sumber = " . $this->userData['id_toko'] . " AND stat = 1 AND id_target <> 0 AND jenis = 2 GROUP BY ref";
         $data['mutasi'] = $this->db(0)->get_cols_where('master_mutasi', $cols, $where, 1, 'ref');
      }

      $this->view($this->v_content, $data);
   }

   function ready()
   {
      $ref = $_POST['ref'];
      $id_karyawan = $_POST['staf_id'];
      $expedisi = $_POST['expedisi'];
      $up =  $this->data('Operasi')->ready($ref, $id_karyawan, $expedisi);
      echo $up['errno'] <> 0 ? $up['error'] : 0;
   }
}
