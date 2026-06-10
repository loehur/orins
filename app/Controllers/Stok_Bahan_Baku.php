<?php

class Stok_Bahan_Baku extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[4]) && !in_array($this->userData['user_tipe'], PV::PRIV[7])) {
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
         "title" => "Stok Bahan Baku"
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
      $data['barang'] = $this->db(0)->get_where('master_barang', "code LIKE 'B0%' AND en = 1 ORDER BY id DESC");
      $data['karyawan_toko'] = $this->db(0)->get_where('karyawan', "id_toko = " . $this->userData['id_toko'], 'id_karyawan');
      $this->view($this->v_content, $data);
   }

   function stok_sn($id_barang, $id_sumber)
   {
      $stok = $this->data('Barang')->stok_data($id_barang, $id_sumber);
      $list = [];
      foreach ($stok as $s) {
         if ($s['qty'] > 0 && $s['sn'] !== "") {
            $list[] = [
               'sn' => $s['sn'],
               'sds' => (int) $s['sds'],
               'qty' => (int) $s['qty'],
            ];
         }
      }
      header('Content-Type: application/json');
      echo json_encode($list);
   }

   function pakai()
   {
      $id_sumber = $_POST['id_sumber'];
      $id_barang = $_POST['id_barang'];
      $karyawan = $_POST['staf_id'];

      if (isset($_POST['sn'])) {
         $sn = $_POST['sn'];
      } else {
         $sn = "";
      }

      if (isset($_POST['sds'])) {
         $sds = $_POST['sds'];
      } else {
         $sds = 0;
      }

      $qty = $_POST['qty'];
      $barang = $this->db(0)->get_where_row('master_barang', "id = " . $id_barang);
      if (isset($barang['sn']) && $barang['sn'] == 1 && $sn === "") {
         echo "SN wajib dipilih";
         exit();
      }

      //cek stok (sesuai tampilan: total jika SN kosong, per SN jika diisi)
      if ($sn === "") {
         $stok_all = $this->data('Barang')->stok_data_all($id_barang, $id_sumber);
         $stok = isset($stok_all[$id_barang]) ? (int) $stok_all[$id_barang]['qty'] : 0;
      } else {
         $stok = $this->data('Barang')->sisa_stok($id_barang, $id_sumber, $sn, $sds);
      }
      if ($stok < $qty) {
         echo $stok <= 0 ? "Stok Kosong" : "Stok tidak mencukupi. Tersedia: " . $stok;
         exit();
      }

      //updateFreqCS
      $this->db(0)->update("karyawan", "freq_pro = freq_pro+1", "id_karyawan = " . $karyawan);

      if (isset($_POST['akun_pakai'])) {
         $id_target = $_POST['akun_pakai'];
      } else {
         $id_target = 0;
      }

      $ref = date("YmdHi");
      $cols = 'ref, jenis, id_barang, id_sumber, id_target, qty, cs_id, sds, stat';
      if ($sn !== "") {
         $cols .= ', sn';
      }

      $vals = "'" . $ref . "',4," . $id_barang . ",'" . $id_sumber . "','" . $id_target . "'," . $qty . "," . $karyawan . "," . $sds . ",1";
      if ($sn !== "") {
         $vals .= ",'" . addslashes($sn) . "'";
      }
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }
}
