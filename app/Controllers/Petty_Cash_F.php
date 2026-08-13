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

   public function content($year = "")
   {
      $idToko = (int)$this->userData['id_toko'];

      $year = (int)$year;
      if ($year < 2000 || $year > 2100) {
         $year = (int)date('Y');
      }

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
         $wherePending . " ORDER BY id DESC LIMIT 10"
      );
      $data['pending_shown'] = is_array($data['pakai']) ? count($data['pakai']) : 0;

      $data['year'] = $year;
      $data['topup'] = $this->db(0)->get_where(
         'kas_kecil',
         "id_target = " . $idToko . " AND tipe = 1 AND insertTime LIKE '" . $year . "%' ORDER BY id DESC"
      );

      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');

      $this->view(__CLASS__ . '/content', $data);
   }

   public function topupList($year = "")
   {
      $idToko = (int)$this->userData['id_toko'];
      $year = (int)$year;
      if ($year < 2000 || $year > 2100) {
         $year = (int)date('Y');
      }

      $data['year'] = $year;
      $data['topup'] = $this->db(0)->get_where(
         'kas_kecil',
         "id_target = " . $idToko . " AND tipe = 1 AND insertTime LIKE '" . $year . "%' ORDER BY id DESC"
      );
      $this->view(__CLASS__ . '/topup_list', $data);
   }

   public function pendingList()
   {
      $idToko = (int)$this->userData['id_toko'];
      $wherePending = "id_sumber = " . $idToko . " AND (tipe = 2 OR tipe = 5) AND st = 0";
      $data['pending_total'] = (int)$this->db(0)->count_where('kas_kecil', $wherePending);
      $data['pakai'] = $this->db(0)->get_where(
         'kas_kecil',
         $wherePending . " ORDER BY id DESC LIMIT 10"
      );
      $data['pending_shown'] = is_array($data['pakai']) ? count($data['pakai']) : 0;
      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');
      $this->view(__CLASS__ . '/pending_list', $data);
   }

   function verify($id, $status)
   {
      $id = (int)$id;
      $status = (int)$status;
      if ($id <= 0) {
         header('Content-Type: application/json');
         echo json_encode(['ok' => 0, 'error' => 'ID tidak valid']);
         exit();
      }

      $update = $this->db(0)->update(
         "kas_kecil",
         "st = '" . $status . "'",
         "id = '" . $id . "' AND st = 0"
      );

      header('Content-Type: application/json');
      if ($update['errno'] <> 0) {
         echo json_encode(['ok' => 0, 'error' => $update['error']]);
         exit();
      }

      $idToko = (int)$this->userData['id_toko'];
      $wherePending = "id_sumber = " . $idToko . " AND (tipe = 2 OR tipe = 5) AND st = 0";
      $pendingTotal = (int)$this->db(0)->count_where('kas_kecil', $wherePending);

      echo json_encode([
         'ok' => 1,
         'pending_total' => $pendingTotal,
      ]);
   }

   function topupPety()
   {
      $jumlah = (int)($_POST['jumlah'] ?? 0);
      $target = (int)$this->userData['id_toko'];

      if ($jumlah <= 0) {
         echo "Jumlah tidak valid";
         exit();
      }

      // Anti double: nominal sama di hari yang sama
      $today = date('Y-m-d');
      $dupDay = "id_target = " . $target . " AND tipe = 1 AND jumlah = " . $jumlah
         . " AND insertTime LIKE '" . $today . "%'";
      if ($this->db(0)->count_where('kas_kecil', $dupDay) > 0) {
         echo "Nominal yang sama sudah diinput hari ini";
         exit();
      }

      // Anti double klik cepat (cadangan)
      $dupWhere = "id_sumber = 100 AND id_target = " . $target . " AND tipe = 1 AND jumlah = " . $jumlah;
      if ($this->recentKasKecilDuplicate($dupWhere, 90)) {
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

   function deleteTopup($id = 0)
   {
      header('Content-Type: application/json');
      $id = (int)$id;
      $idToko = (int)$this->userData['id_toko'];

      if ($id <= 0) {
         echo json_encode(['ok' => 0, 'error' => 'ID tidak valid']);
         exit();
      }

      $row = $this->db(0)->get_where_row(
         'kas_kecil',
         "id = " . $id . " AND id_target = " . $idToko . " AND tipe = 1"
      );
      if (!$row || empty($row['id'])) {
         echo json_encode(['ok' => 0, 'error' => 'Data topup tidak ditemukan']);
         exit();
      }

      $del = $this->db(0)->delete_where(
         'kas_kecil',
         "id = " . $id . " AND id_target = " . $idToko . " AND tipe = 1"
      );
      if ($del['errno'] <> 0) {
         echo json_encode(['ok' => 0, 'error' => $del['error']]);
         exit();
      }

      echo json_encode([
         'ok' => 1,
         'saldo' => $this->calcSaldo($idToko),
      ]);
   }

   private function calcSaldo($idToko)
   {
      $topup = (int)$this->db(0)->sum_col_where(
         'kas_kecil',
         'jumlah',
         "id_target = " . (int)$idToko . " AND tipe = 1 AND st <> 2"
      );
      $pakai = (int)$this->db(0)->sum_col_where(
         'kas_kecil',
         'jumlah',
         "id_sumber = " . (int)$idToko . " AND (tipe = 2 OR tipe = 5) AND st <> 2"
      );
      return $topup - $pakai;
   }

   private function recentKasKecilDuplicate($whereExtra, $seconds = 90)
   {
      $since = date('Y-m-d H:i:s', time() - (int)$seconds);
      return $this->db(0)->count_where('kas_kecil', $whereExtra . " AND insertTime >= '" . $since . "'") > 0;
   }
}
