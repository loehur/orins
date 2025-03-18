<?php

class Stok_Transfer extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[105])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Stok - Transfer"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $data['tujuan'] = $this->db(0)->get_cols_where('toko', 'id_toko, id_toko as id, nama_toko as nama', 'en = 1', 1, 'id_toko');
      $data['input'] = $this->db(0)->get_where("master_input", "tipe = 1 AND id_target = '" . $this->userData['id_toko'] . "' ORDER BY id DESC");
      $this->view(__CLASS__ . '/content', $data);
   }

   public function list($id)
   {
      $this->view("Layouts/layout_main", [
         "title" => "Stok - Transfer"
      ]);
      $this->viewer($page = "list_data", $id);
   }

   function list_data($id)
   {
      $data['input'] = $this->db(0)->get_where_row('master_input', "id = '" . $id . "'");
      $data['stok'] = $this->data('Barang')->stok_data_list_all($data['input']['id_target']);
      $data['tujuan'] = $this->db(0)->get_where('toko', 'en = 1', 'id_toko');
      $cols = "id, code, CONCAT(brand,' ',model) as nama";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');
      $data['id'] = $id;
      $this->view(__CLASS__ . '/list_data', $data);
   }

   function list_transfer($ref)
   {
      $cols = "id, code, CONCAT(brand,' ',model) as nama";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "ref = '" . $ref . "'");
      $this->view(__CLASS__ . '/list_transfer', $data);
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
      $tujuan = $this->userData['id_toko'];
      $tanggal = $_POST['tanggal'];
      $error = 0;
      $note = $_POST['note'];

      if (strlen($tujuan) == 0 || strlen($tanggal) == 0) {
         exit();
      }

      $id = date('ymdHi');
      $cols = 'id, tipe, id_sumber, id_target, tanggal, user_id, note';
      $vals = "'" . $id . "',1,0,'" . $tujuan . "','" . $tanggal . "'," . $this->userData['id_user'] . ",'" . $note . "'";
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

      $sisa_stok = $this->data('Barang')->sisa_stok($id_barang, 0, $sn, $sds);

      if ($sisa_stok <= 0) {
         echo "Stok ter-update tidak tersedia";
         exit();
      }

      $id_sumber = 0;
      $cols = 'ref, jenis, id_barang, id_sumber, id_target, qty, sds, sn, sn_c';

      $vals = "'" . $ref . "',1," . $id_barang . ",'" . $id_sumber . "','" . $target . "'," . $qty . "," . $sds . ",'" . $sn . "'," . $sn_c;
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }
}
