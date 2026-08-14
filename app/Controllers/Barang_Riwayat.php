<?php

class Barang_Riwayat extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
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
         "title" => "Barang - Riwayat"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $data['barang'] = $this->db(0)->get_where('master_barang', "sp = 0", 'id');
      $this->view(__CLASS__ . '/content', $data);
   }

   function data($kode, $sn = "")
   {
      $kode = addslashes($kode);
      $sn = trim(urldecode($sn));
      $data['barang'] = $this->db(0)->get_where_row('master_barang', "sp = 0 AND id = '" . $kode . "'");
      $data['supplier'] = $this->db(0)->get('master_supplier', "id");
      $data['akun_pakai'] = $this->db(0)->get('akun_pakai', "id");
      if ($sn == "") {
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', "id_barang = '" . $kode . "' AND stat <> 0");
      } else {
         $snEsc = addslashes($sn);
         $data['mutasi'] = $this->db(0)->get_where(
            'master_mutasi',
            "id_barang = '" . $kode . "' AND UPPER(TRIM(sn)) = '" . strtoupper($snEsc) . "' AND stat <> 0"
         );
      }
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');
      $this->view(__CLASS__ . '/data', $data);
   }

   function cek_sn()
   {
      header('Content-Type: application/json');
      $sn = trim($_POST['sn'] ?? '');
      if ($sn === '') {
         echo json_encode(['ok' => 0, 'error' => 'Serial Number wajib diisi']);
         exit();
      }

      $snEsc = addslashes($sn);
      $rows = $this->db(0)->get_cols_where(
         'master_mutasi',
         'id_barang',
         "TRIM(sn) <> '' AND UPPER(TRIM(sn)) = '" . strtoupper($snEsc) . "' AND stat <> 0 GROUP BY id_barang"
      );
      if (!is_array($rows) || isset($rows['errno'])) {
         echo json_encode(['ok' => 0, 'error' => 'Gagal cek Serial Number']);
         exit();
      }

      $ids = [];
      foreach ($rows as $r) {
         if (!isset($r['id_barang']) || $r['id_barang'] === '' || $r['id_barang'] === null) {
            continue;
         }
         $ids[(string)$r['id_barang']] = $r['id_barang'];
      }
      $ids = array_values($ids);
      $count = count($ids);

      if ($count === 0) {
         echo json_encode(['ok' => 1, 'count' => 0, 'sn' => $sn]);
         exit();
      }

      if ($count === 1) {
         echo json_encode(['ok' => 1, 'count' => 1, 'sn' => $sn, 'id_barang' => $ids[0]]);
         exit();
      }

      $idList = [];
      foreach ($ids as $id) {
         $idList[] = "'" . addslashes((string)$id) . "'";
      }
      $barang = $this->db(0)->get_where('master_barang', "id IN (" . implode(',', $idList) . ")");
      $produk = [];
      $found = [];
      if (is_array($barang)) {
         foreach ($barang as $br) {
            $found[(string)$br['id']] = true;
            $produk[] = $this->labelBarang($br);
         }
      }
      foreach ($ids as $id) {
         if (!isset($found[(string)$id])) {
            $produk[] = 'ID ' . $id;
         }
      }

      echo json_encode([
         'ok' => 1,
         'count' => $count,
         'sn' => $sn,
         'produk' => $produk,
      ]);
   }

   private function labelBarang($br)
   {
      $nama = trim(($br['code'] ?? '') . ' ' . trim(($br['brand'] ?? '') . ' ' . ($br['model'] ?? '')) . ' ' . ($br['product_name'] ?? ''));
      return trim(preg_replace('/\s+/', ' ', $nama));
   }

   function update_sn()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];

      $data = $this->db(0)->get_where_row('master_mutasi', "id = '" . $id . "'");

      if (isset($data['sn'])) {
         $cek_sn = $this->db(0)->count_where('master_mutasi', "sn = '" . $value . "' AND id_barang = '" . $data['id_barang'] . "' AND jenis = 0");
         if ($cek_sn == 0) {
            $where = "id_barang = '" . $data['id_barang'] . "' AND sn = '" . $data['sn'] . "'";
            $up = $this->db(0)->update("master_mutasi", "sn = '" . strtoupper($value) . "'", $where);
            echo $up['errno'] == 0 ? 0 : $up['error'];
         } else {
            echo "Duplicate SN " . $value;
            exit();
         }
      } else {
         echo "No Data";
         exit();
      }
      echo 0;
   }

   function update_sds()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];

      $data = $this->db(0)->get_where_row('master_mutasi', "id = '" . addslashes($id) . "'");
      if (!$data) {
         echo "No Data";
         exit();
      }

      $new_sds = $value == 0 ? 1 : 0;
      $sn = trim($data['sn'] ?? '');

      if ($sn === '') {
         $where = "id = " . intval($id);
      } else {
         $where = "id_barang = '" . addslashes($data['id_barang']) . "' AND sn = '" . addslashes($data['sn']) . "'";
      }

      $up = $this->db(0)->update("master_mutasi", "sds = " . (int)$new_sds, $where);
      echo $up['errno'] == 0 ? $new_sds : $up['error'];
   }
}
