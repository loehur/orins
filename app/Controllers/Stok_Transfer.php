<?php

class Stok_Transfer extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
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

   public    function content()
   {
      $data['tujuan'] = $this->db(0)->get_cols_where('toko', 'id_toko, id_toko as id, nama_toko as nama', 'en = 1', 1, 'id_toko');
      $data['input'] = $this->db(0)->get_where("master_input", "tipe = 1 AND id_target = '" . $this->userData['id_toko'] . "' ORDER BY id DESC");
      foreach ($data['input'] as $key => $row) {
         $data['input'][$key]['can_cancel'] = 0;
         if ($row['cek'] == 0) {
            $verified = $this->db(0)->count_where('master_mutasi', "ref = '" . $row['id'] . "' AND stat <> 0");
            $data['input'][$key]['can_cancel'] = ($verified == 0) ? 1 : 0;
         }
      }
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
      $cols = "id, code, CONCAT(brand,' ',model) as nama, product_name";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');
      $data['id'] = $id;
      $data['can_cancel'] = 0;
      if ($data['input']['cek'] == 0) {
         $verified = $this->db(0)->count_where('master_mutasi', "ref = '" . $id . "' AND stat <> 0");
         $data['can_cancel'] = ($verified == 0) ? 1 : 0;
      }
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

      $id_sumber = 0;
      $sisa_stok = $this->data('Barang')->sisa_stok($id_barang, 0, $sn, $sds);

      if ($sisa_stok < $qty) {
         echo "Sisa stok tersedia hanya " . $qty;
         exit();
      }

      $cols = 'ref, jenis, id_barang, id_sumber, id_target, qty, sds, sn, sn_c';
      $vals = "'" . $ref . "',1," . $id_barang . ",'" . $id_sumber . "','" . $target . "'," . $qty . "," . $sds . ",'" . $sn . "'," . $sn_c;
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }

   function req_antar()
   {
      $note = $_POST['note'];
      $id = $_POST['id'];
      $ref = $id;

      $up = $this->db(0)->update("master_input", "delivery = 1, note_driver = '" . $note . "'", "id = '" . $ref . "'");
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      } else {
         if (PV::PRO == 1) {
            $target = "6285278703970-1501834492@g.us"; // delivery order
            $cek = $this->db(0)->get_where_row("master_input", "id = '" . $ref . "'");
            $nama_target = strtoupper($this->dToko[$cek['id_target']]['nama_toko']);
            $sort_ref = substr($id, -4);
            $text = "*Permintaan Kirim Barang* #" . $sort_ref . " \nGUDANG ke " . $nama_target . " \n_" . $note . "_";
            $kirim = $this->data("WA")->send_wa(PV::API_KEY['fonnte'], $target, $text, 1);
            if ($kirim['status'] <> true) {
               print_r($kirim);
            } else {
               echo 0;
            }
         } else {
            echo 0;
         }
      }
   }

   function cancel()
   {
      if (!in_array($this->userData['user_tipe'], PV::PRIV[7])) {
         echo "Hanya Gudang yang dapat membatalkan surat transfer.";
         exit();
      }

      $id = $_POST['id'];
      $head = $this->db(0)->get_where_row('master_input', "id = '" . $id . "' AND tipe = 1");
      if (!$head) {
         echo "Surat transfer tidak ditemukan.";
         exit();
      }

      if ($head['cek'] != 0) {
         echo "Surat (" . $id . ") sudah terverifikasi dan tidak dapat dibatalkan.";
         exit();
      }

      $verified = $this->db(0)->count_where('master_mutasi', "ref = '" . $id . "' AND stat <> 0");
      if ($verified > 0) {
         echo "Masih ada item yang sudah terverifikasi. Pembatalan tidak diizinkan.";
         exit();
      }

      $del = $this->db(0)->delete_where('master_mutasi', "ref = '" . $id . "'");
      if ($del['errno'] <> 0) {
         echo $del['error'];
         exit();
      }

      $del = $this->db(0)->delete_where('master_input', "id = '" . $id . "'");
      if ($del['errno'] <> 0) {
         echo $del['error'];
         exit();
      }

      $this->model('Log')->write($this->userData['user'] . " Cancel Stok Transfer #" . $id);
      echo 0;
   }
}
