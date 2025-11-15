<?php

class Barang_Masuk extends Controller
{
   public $title = "Barang - Masuk";
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2]) && !in_array($this->userData['user_tipe'], PV::PRIV[5])) {
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
      $data['input'] = $this->db(0)->get_where('master_input', "(tipe = 1 OR tipe = 2) AND id_target = '" . $this->userData['id_toko'] . "' AND cek = 0 ORDER BY tanggal DESC");
      $data['input_done'] = $this->db(0)->get_where('master_input', "(tipe = 1 OR tipe = 2) AND id_target = '" . $this->userData['id_toko'] . "' AND cek <> 0 ORDER BY tanggal DESC");
      $data['toko'] = $this->db(0)->get_where('toko', "en = 1", "id_toko");
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
      $data['toko'] = $this->db(0)->get_where('toko', "en = 1", "id_toko");

      $cols = "id, code, CONCAT(brand,' ',model) as nama, product_name";
      $data['barang'] = $this->db(0)->get_cols_where('master_barang', $cols, "en = 1", 1, 'id');

      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "ref = '" . $id . "'");
      $data['karyawan_toko'] = $this->db(0)->get_where('karyawan', "id_toko = " . $this->userData['id_toko'], 'id_karyawan');
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

      if (isset($_POST['id_karyawan'])) {
         $id_karyawan = $_POST['id_karyawan'];
      } else {
         $id_karyawan = 0;
      }

      if (isset($_POST['id_driver'])) {
         $id_driver = $_POST['id_driver'];
      } else {
         $id_driver = 0;
      }

      $up1 = $this->db(0)->update("master_input", "cek = 1, id_target_cs = " . $id_karyawan . ", id_driver = " . $id_driver, "id = '" . $ref . "'");
      if ($up1['errno'] <> 0) {
         echo $up1['error'];
         exit();
      } else {

         if ($id_driver <> 0) {
            $this->db(0)->update("karyawan", "freq_driver = freq_driver+1", "id_karyawan = " . $id_driver);
         }

         $up2 = $this->db(0)->update("master_mutasi", "stat = 1", "ref = '" . $ref . "'");
         if ($up2['errno'] <> 0) {
            echo $up2['error'];
            exit();
         } else {
            $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $id_karyawan);
            $up_ambil = $this->data('Operasi')->ambil_semua($ref, $id_karyawan);
            if ($up_ambil['errno'] <> 0) {
               echo $up_ambil['error'];
               exit();
            }
         }
      }
      echo 0;
   }

   function terima_per_item($id, $ref)
   {
      $update = $this->db(0)->update("master_mutasi", "stat = 1", "id_target = '" . $this->userData['id_toko'] . "' AND id = " . $id . " AND ref = '" . $ref . "'");
      if ($update['errno'] == 0) {
         echo 0;
      } else {
         echo $update['error'];
      }
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
