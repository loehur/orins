<?php

class Petty_Cash_F extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[5])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Petty Cash Finance"
      ]);

      $this->viewer();
   }

   public function viewer($page = "", $parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "page" => $page]);
   }

   public function content()
   {
      $idToko = (int)$this->userData['id_toko'];

      $topup = (int)$this->db(0)->sum_col_where(
         'kas_kecil',
         'jumlah',
         "id_target = " . $idToko . " AND tipe = 1 AND st <> 2"
      );
      $pakai = (int)$this->db(0)->sum_col_where(
         'kas_kecil',
         'jumlah',
         "id_sumber = " . $idToko . " AND (tipe = 2 OR tipe = 5) AND st <> 2"
      );
      $data['saldo'] = $topup - $pakai;

      $wherePending = "id_sumber = " . $idToko . " AND (tipe = 2 OR tipe = 5) AND st = 0";
      $data['pending_total'] = (int)$this->db(0)->count_where('kas_kecil', $wherePending);
      $data['pakai'] = $this->db(0)->get_where(
         'kas_kecil',
         $wherePending . " ORDER BY id DESC LIMIT 20"
      );
      $data['pending_shown'] = is_array($data['pakai']) ? count($data['pakai']) : 0;

      $data['topup'] = $this->db(0)->get_where(
         'kas_kecil',
         "id_target = " . $idToko . " AND tipe = 1 ORDER BY id DESC LIMIT 40"
      );

      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');

      $this->view(__CLASS__ . '/content', $data);
   }

   function verify($id, $status)
   {
      $id = (int)$id;
      $status = (int)$status;
      if ($id <= 0) {
         echo "ID tidak valid";
         exit();
      }

      $update = $this->db(0)->update(
         "kas_kecil",
         "st = '" . $status . "'",
         "id = '" . $id . "' AND st = 0"
      );
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }

   function topupPety()
   {
      $jumlah = (int)($_POST['jumlah'] ?? 0);
      $target = (int)$this->userData['id_toko'];

      if ($jumlah <= 0) {
         echo "Jumlah tidak valid";
         exit();
      }

      $dupWhere = "id_sumber = 100 AND id_target = " . $target . " AND tipe = 1 AND jumlah = " . $jumlah;
      if ($this->recentKasKecilDuplicate($dupWhere)) {
         echo "Data sudah di input";
         exit();
      }

      $ref = date('ymdHis') . rand(10, 99);
      $cols = 'id_sumber, id_target, tipe, ref, jumlah, st';
      $vals = "100," . $target . ",1,'" . $ref . "'," . $jumlah . ",0";
      $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
      if ($do['errno'] == 1062) {
         echo "data sudah di input";
         exit();
      }
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      echo 0;
   }

   private function recentKasKecilDuplicate($whereExtra, $seconds = 90)
   {
      $since = date('Y-m-d H:i:s', time() - (int)$seconds);
      return $this->db(0)->count_where('kas_kecil', $whereExtra . " AND insertTime >= '" . $since . "'") > 0;
   }
}
