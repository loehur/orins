<?php

class Gudang_Input extends Controller
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
         "title" => "Gudang - Input"
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
      $data['input'] = $this->db(0)->get_where('master_input', 'tipe = 0 ORDER BY id DESC');
      $this->view(__CLASS__ . '/content', $data);
   }

   public function list($id)
   {
      $this->view("Layouts/layout_main", [
         "title" => "Gudang - Input"
      ]);
      $this->viewer($page = "list_data", $id);
   }

   function list_data($id)
   {
      $data['input'] = $this->db(0)->get_where_row('master_input', "id = '" . $id . "'");
      $cols = "id, code, CONCAT(brand,' ',model) as nama, code_f";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');
      $data['barang_code'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'code');
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "ref = '" . $id . "'");
      $data['id'] = $id;
      $this->view(__CLASS__ . '/list_data', $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   function add()
   {
      $supplier_c = strtoupper($_POST['supplier_c']);
      $supplier = strtoupper($_POST['supplier']);
      $tanggal = $_POST['tanggal'];
      $no_fak = strtoupper($_POST['no_fak']);
      $no_po = strtoupper($_POST['no_po']);
      $sds = isset($_POST['sds']) ? $_POST['sds'] : 0;
      $error = 0;

      if (strlen($supplier_c) == 0 || strlen($supplier_c) != 8) {
         $supplier_c = substr($supplier, 0, 1) . date('ymd') . rand(0, 9);
      }

      //SUPPLIER
      $cols = 'id,nama';
      $vals = "'" . $supplier_c . "','" . $supplier . "'";
      $do = $this->db(0)->insertCols('master_supplier', $cols, $vals);
      if ($do['errno'] == 1062) {
         $set = "nama = '" . $supplier . "'";
         $where_supplier = "id = '" . $supplier_c . "'";
         $up = $this->db(0)->update('master_supplier', $set, $where_supplier);
         if ($up['errno'] <> 0) {
            $error .= $up['error'];
            echo $error;
            exit();
         }
      } else if ($do['errno'] <> 0) {
         $error .= $do['error'];
         echo $error;
         exit();
      }

      $id = date('ymdHi');
      $cols = 'id, id_sumber,no_faktur,no_po,tanggal,sds';
      $vals = $id . ",'" . $supplier_c . "','" . $no_fak . "','" . $no_po . "','" . $tanggal . "'," . $sds;
      $do = $this->db(0)->insertCols('master_input', $cols, $vals);
      if ($do['errno'] <> 0) {
         $error .= $do['error'];
      }

      echo $error;
   }

   function add_mutasi()
   {
      $ref = $_POST['head_id'];
      $barang_ = $_POST['barang_'];
      $head = $this->db(0)->get_where_row('master_input', "id = '" . $ref . "'");
      $barang = $this->db(0)->get_where_row('master_barang', "code = '" . $barang_ . "'");

      $qty = $_POST['qty'];
      $sds = $head['sds'];
      $sn =  $barang['sn'];
      $id_sumber = $head['id_sumber'];
      $id_barang = $barang['id_barang'];
      $h_beli = $barang['harga'];

      $cols = 'ref,jenis,id_barang,kode_barang,id_sumber,id_target,harga_beli,qty,sds,sn_c';

      if ($sn == 1) {
         $vals = "'" . $ref . "',0," . $id_barang . ",'" . $barang_ . "','" . $id_sumber . "',0," . $h_beli . ",1," . $sds . "," . $sn;
         for ($x = 1; $x <= $qty; $x++) {
            $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
         }
      } else {
         $vals = "'" . $ref . "',0," . $id_barang . ",'" . $barang_ . "','" . $id_sumber . "',0," . $h_beli . "," . $qty . "," . $sds . "," . $sn;
         $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      }
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }

   function update_sn()
   {
      $kode = $_POST['kode'];
      $id = $_POST['id'];
      $value = $_POST['value'];
      $col = $_POST['col'];
      $primary = $_POST['primary'];
      $tb = $_POST['tb'];
      $set = $col . " = '" . $value . "'";
      $where = $primary . " = " . $id;

      $cek_sn = $this->db(0)->count_where('master_mutasi', "sn_unic = '" . $kode . $value . "'");
      if ($cek_sn == 0) {
         $up = $this->db(0)->update($tb, $set, $where);
         echo $up['errno'] == 0 ? 0 : $up['error'];
      } else {
         echo "Duplicate SN " . $value;
      }
   }
}
