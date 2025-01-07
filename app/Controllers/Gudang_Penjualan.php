<?php

class Gudang_Penjualan extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[7])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Gudang Penjualan"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $data['tujuan'] = $this->db(0)->get_where('pelanggan', 'id_pelanggan_jenis = 0', 'id_pelanggan');
      $data['input'] = $this->db(0)->get_where('master_input', 'tipe = 3 ORDER BY id DESC');
      $this->view(__CLASS__ . '/content', $data);
   }

   public function list($id)
   {
      $this->view("Layouts/layout_main", [
         "title" => "Gudang Penjualan"
      ]);
      $this->viewer($page = "list_data", $id);
   }

   function list_data($id)
   {
      $data['input'] = $this->db(0)->get_where_row('master_input', "id = '" . $id . "'");
      $data['tujuan'] = $this->db(0)->get_where('pelanggan', 'id_pelanggan_jenis = 0', 'id_pelanggan');
      $cols = "id, code, CONCAT(brand,' ',model) as nama";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "ref = '" . $id . "'");
      $data['id'] = $id;
      $this->view(__CLASS__ . '/list_data', $data);
   }

   function stok_data($kode, $ref)
   {
      $data['ref'] = $ref;
      $data['stok'] = $this->data('Barang')->stok_data($kode, 0);
      $this->view(__CLASS__ . '/list_stok', $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   function add()
   {
      $tujuan = strtoupper($_POST['tujuan']);
      $tanggal = $_POST['tanggal'];
      $error = 0;

      if (strlen($tujuan) == 0 || strlen($tanggal) == 0) {
         exit();
      }

      $id = date('ymdHi');
      $cols = 'id, tipe, id_sumber, id_target, tanggal, user_id';
      $vals = "'" . $id . "',3,0,'" . $tujuan . "','" . $tanggal . "'," . $this->userData['id_user'];
      $do = $this->db(0)->insertCols('master_input', $cols, $vals);
      if ($do['errno'] <> 0) {
         $error .= $do['error'];
      }

      echo $error;
   }

   function add_mutasi($ref)
   {
      $id_barang = $_POST['kode'];

      $head = $this->db(0)->get_where_row('master_input', "id = '" . $ref . "'");
      $target = $head['id_target'];
      $qty = $_POST['qty'];
      $sds = $_POST['sds'];
      $sn =  $_POST['sn'];
      $sn_c = 0;
      if (strlen($sn) > 0) {
         $sn_c = 1;
      }

      $id_sumber = 0;
      $cek = $this->data('Barang')->cek($id_barang, 0, $sn, $sds, $qty);
      if ($cek == false) {
         echo "Stok ter-update tidak tersedia";
         exit();
      }

      $cols = 'ref, jenis, id_barang, id_sumber, id_target, qty, sds, sn, sn_c';

      $vals = "'" . $ref . "',2," . $id_barang . ",'" . $id_sumber . "','" . $target . "'," . $qty . "," . $sds . ",'" . $sn . "'," . $sn_c;
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }
}
