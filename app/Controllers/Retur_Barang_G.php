<?php

class Retur_Barang_G extends Controller
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
         "title" => "Gudang - Retur Barang"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $data['supplier'] = $this->db(0)->get('master_supplier', 'id');
      $data['input'] = $this->db(0)->get_where('master_input', 'tipe = 4 AND id_sumber = 0 ORDER BY id DESC');
      $this->view(__CLASS__ . '/content', $data);
   }

   public function list($id)
   {
      $this->view("Layouts/layout_main", [
         "title" => "Gudang - Retur Barang"
      ]);
      $this->viewer($page = "list_data", $id);
   }

   function list_data($id)
   {
      $data['input'] = $this->db(0)->get_where_row('master_input', "id = '" . $id . "'");
      $data['tujuan'] = $this->db(0)->get('master_supplier', 'id');
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
      $tanggal = $_POST['tanggal'];
      $sds = $_POST['sds'];
      $note = $_POST['note'];
      $error = 0;
      $supplier = strtoupper($_POST['supplier']);

      if ($sds == "") {
         $sds = 0;
      }

      $id = date('ymdHi');
      $cols = 'id, tipe, id_sumber, id_target, tanggal, user_id, sds, note';
      $vals = "'" . $id . "',4,0,'" . $supplier . "','" . $tanggal . "'," . $this->userData['id_user'] . "," . $sds . ",'" . $note . "'";
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
      $sisa = $this->data('Barang')->sisa_stok($id_barang, 0, $sn, $sds);
      if ($sisa <= 0) {
         echo "Stok ter-update tidak tersedia";
         exit();
      }

      $cols = 'ref, jenis, id_barang, id_sumber, id_target, qty, sds, sn, sn_c';

      $vals = "'" . $ref . "',3," . $id_barang . ",'" . $id_sumber . "','" . $target . "'," . $qty . "," . $sds . ",'" . $sn . "'," . $sn_c;
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }
}
