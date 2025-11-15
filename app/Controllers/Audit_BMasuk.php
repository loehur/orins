<?php

class Audit_BMasuk extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[6])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Audit - Barang Masuk"
      ]);

      $this->viewer();
   }

   function loadEdit($id)
   {
      $data['supplier'] = $this->db(0)->get('master_supplier', 'id');
      $data['input'] = $this->db(0)->get_where_row('master_input', "id = '" . $id . "'");
      $this->view(__CLASS__ . "/form", $data);
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $data['input'] = $this->db(0)->get_where('master_input', 'tipe = 0 AND cek = 0 ORDER BY insertTime DESC');
      $data['input_done'] = $this->db(0)->get_where('master_input', "tipe = 0 AND cek <> 0 ORDER BY insertTime DESC");
      $data['supplier'] = $this->db(0)->get('master_supplier', 'id');
      $this->view(__CLASS__ . '/content', $data);
   }

   public function list($id)
   {
      $this->view("Layouts/layout_main", [
         "title" => "Audit - Barang Masuk"
      ]);
      $this->viewer($page = "list_data", $id);
   }

   function list_data($id)
   {
      $data['input'] = $this->db(0)->get_where_row('master_input', "id = '" . $id . "'");
      $cols = "id, code, code_myob, CONCAT(brand,' ',model) as nama";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "ref = '" . $id . "'");
      $data['id'] = $id;
      $this->view(__CLASS__ . '/list_data', $data);
   }

   function load($kode, $table, $col)
   {
      $data = $this->db(0)->get_where($table, $col . " = '" . $kode . "'");
      echo json_encode($data);
   }

   function update_surat()
   {
      $supplier = strtoupper($_POST['supplier']);
      $tanggal = $_POST['tanggal'];
      $no_fak = strtoupper($_POST['no_fak']);
      $no_po = strtoupper($_POST['no_po']);
      $note = strtoupper($_POST['note']);
      $error = 0;

      $id = $_POST['id'];
      $set = "id_sumber = '" . $supplier . "', no_faktur = '" . $no_fak . "', no_po = '" . $no_po . "', tanggal = '" . $tanggal . "', note = '" . $note . "'";
      $where = "id = '" . $id . "'";
      $do = $this->db(0)->update('master_input', $set, $where);
      if ($do['errno'] <> 0) {
         $error = $do['error'];
      }

      echo $error;
   }

   function update()
   {
      $ref = $_POST['ref'];

      $cek_SN = $this->db(0)->count_where("master_mutasi", "ref = '" . $ref . "' AND sn_c = 1 AND sn = ''");
      if ($cek_SN > 0) {
         "Mohon lengkapi SN terlebih dahulu";
         exit();
      }

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
      }
      echo 0;
   }

   function reject()
   {
      $ref = $_POST['ref'];

      $get = $this->db(0)->get_where("master_mutasi", "ref = '" . $ref . "'");

      $boleh_reject = true;
      $message = "";
      foreach ($get as $g) {
         if ($g['pid'] <> 0) {
            $message = "Reject Gagal. silahkan lakukan pembatalan order untuk membatalkan barang produksi";
            $boleh_reject = false;
            break;
         }

         if ($g['sn'] <> '') {
            $cek = $this->db(0)->get_where_row("master_mutasi", "sn = '" . $g['sn'] . "' AND id_barang = " . $g['id_barang'] . " AND jenis = 2 AND stat <> 2");
            if (isset($cek['stat'])) {
               $up_s = $this->db(0)->update("master_mutasi", "sold = 1", "sn = '" . $g['sn'] . "' AND id_barang = " . $g['id_barang']);
               if ($up_s['errno'] <> 0) {
                  $message = "Reject Gagal. terjadi kesalahan pada sistem";
                  $boleh_reject = false;
                  break;
               }
            }

            $cek = $this->db(0)->get_where_row("master_mutasi", "sn = '" . $g['sn'] . "' AND id_barang = " . $g['id_barang'] . " AND jenis = 1 AND stat <> 2");
            if (isset($cek['stat'])) {
               $message = "Reject Gagal. SN: " . $cek['sn'] . " sudah/sedang bermutasi";
               $boleh_reject = false;
               break;
            }
         }
      }

      if ($boleh_reject == false) {
         echo $message;
         exit();
      }

      $up1 = $this->db(0)->update("master_input", "cek = 0", "id = '" . $ref . "'");
      if ($up1['errno'] <> 0) {
         echo $up1['errno'];
         exit();
      } else {
         $up2 = $this->db(0)->update("master_mutasi", "stat = 0", "ref = '" . $ref . "' AND sold = 0");
         if ($up2['errno'] <> 0) {
            echo $up2['errno'];
            exit();
         }
      }

      echo 0;
   }
}
