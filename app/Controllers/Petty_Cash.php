<?php

class Petty_Cash extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[104])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "title" => "Petty Cash"
      ]);

      $this->viewer();
   }

   public function viewer($parse1 = "", $parse2 = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse1, "page" => $parse2]);
   }

   public function content($year = "", $ym = "")
   {
      $idToko = (int)$this->userData['id_toko'];
      $year = $this->normalizeYear($year);
      $ym = $this->normalizeYm($ym);
      $canOps = in_array($this->userData['user_tipe'], PV::PRIV[2]);

      $data['saldo'] = $this->calcSaldo($idToko);
      $data['year'] = $year;
      $data['ym'] = $ym;
      $data['can_ops'] = $canOps;
      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');

      // Topup menunggu verify (selalu tampil)
      $wherePending = "id_target = " . $idToko . " AND tipe = 1 AND st = 0";
      $data['pending_total'] = (int)$this->db(0)->count_where('kas_kecil', $wherePending);
      $data['pending'] = $this->db(0)->get_where(
         'kas_kecil',
         $wherePending . " ORDER BY id DESC LIMIT 10"
      );
      $data['pending_shown'] = is_array($data['pending']) ? count($data['pending']) : 0;

      // Riwayat topup per tahun
      $data['topup'] = $this->db(0)->get_where(
         'kas_kecil',
         "id_target = " . $idToko . " AND tipe = 1 AND st <> 0 AND insertTime LIKE '" . $year . "%' ORDER BY id DESC"
      );

      // Pemakaian per bulan
      $data['pakai'] = $this->db(0)->get_where(
         'kas_kecil',
         "id_sumber = " . $idToko . " AND (tipe = 2 OR tipe = 5) AND insertTime LIKE '" . $ym . "%' ORDER BY id DESC"
      );

      $this->view(__CLASS__ . '/content', $data);
   }

   public function topupList($year = "")
   {
      $idToko = (int)$this->userData['id_toko'];
      $year = $this->normalizeYear($year);
      $data['year'] = $year;
      $data['can_ops'] = in_array($this->userData['user_tipe'], PV::PRIV[2]);
      $data['topup'] = $this->db(0)->get_where(
         'kas_kecil',
         "id_target = " . $idToko . " AND tipe = 1 AND st <> 0 AND insertTime LIKE '" . $year . "%' ORDER BY id DESC"
      );
      $this->view(__CLASS__ . '/topup_list', $data);
   }

   public function pakaiList($ym = "")
   {
      $idToko = (int)$this->userData['id_toko'];
      $ym = $this->normalizeYm($ym);
      $data['ym'] = $ym;
      $data['can_ops'] = in_array($this->userData['user_tipe'], PV::PRIV[2]);
      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');
      $data['pakai'] = $this->db(0)->get_where(
         'kas_kecil',
         "id_sumber = " . $idToko . " AND (tipe = 2 OR tipe = 5) AND insertTime LIKE '" . $ym . "%' ORDER BY id DESC"
      );
      $this->view(__CLASS__ . '/pakai_list', $data);
   }

   public function pendingList()
   {
      $idToko = (int)$this->userData['id_toko'];
      $wherePending = "id_target = " . $idToko . " AND tipe = 1 AND st = 0";
      $data['pending_total'] = (int)$this->db(0)->count_where('kas_kecil', $wherePending);
      $data['pending'] = $this->db(0)->get_where(
         'kas_kecil',
         $wherePending . " ORDER BY id DESC LIMIT 10"
      );
      $data['pending_shown'] = is_array($data['pending']) ? count($data['pending']) : 0;
      $data['can_ops'] = in_array($this->userData['user_tipe'], PV::PRIV[2]);
      $this->view(__CLASS__ . '/pending_list', $data);
   }

   function verify($id, $status)
   {
      header('Content-Type: application/json');
      $id = (int)$id;
      $status = (int)$status;
      $idToko = (int)$this->userData['id_toko'];

      if ($id <= 0 || !in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         echo json_encode(['ok' => 0, 'error' => 'Tidak diizinkan']);
         exit();
      }

      $update = $this->db(0)->update(
         "kas_kecil",
         "st = '" . $status . "'",
         "id = '" . $id . "' AND id_target = " . $idToko . " AND tipe = 1 AND st = 0"
      );
      if ($update['errno'] <> 0) {
         echo json_encode(['ok' => 0, 'error' => $update['error']]);
         exit();
      }

      $wherePending = "id_target = " . $idToko . " AND tipe = 1 AND st = 0";
      echo json_encode([
         'ok' => 1,
         'pending_total' => (int)$this->db(0)->count_where('kas_kecil', $wherePending),
         'saldo' => $this->calcSaldo($idToko),
      ]);
   }

   function pakai()
   {
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         echo "Tidak diizinkan";
         exit();
      }

      $jumlah = (int)($_POST['jumlah'] ?? 0);
      $jenis = (int)($_POST['jenis'] ?? 0);
      $note = addslashes(trim($_POST['note'] ?? ''));
      $tanggal = addslashes(trim($_POST['tanggal'] ?? ''));
      $idToko = (int)$this->userData['id_toko'];

      if ($jumlah <= 0 || $jenis <= 0 || $tanggal === '') {
         echo "Data tidak valid";
         exit();
      }

      // Anti double: nominal + jenis + tanggal nota sama di hari input yang sama
      $today = date('Y-m-d');
      $dupDay = "id_sumber = " . $idToko . " AND tipe = 2 AND id_target = " . $jenis
         . " AND jumlah = " . $jumlah . " AND tanggal = '" . $tanggal . "'"
         . " AND insertTime LIKE '" . $today . "%'";
      if ($this->db(0)->count_where('kas_kecil', $dupDay) > 0) {
         echo "Pemakaian dengan nominal/tanggal sama sudah diinput hari ini";
         exit();
      }

      $dupWhere = "id_sumber = " . $idToko . " AND tipe = 2 AND id_target = " . $jenis
         . " AND jumlah = " . $jumlah . " AND note = '" . $note . "' AND tanggal = '" . $tanggal . "'";
      if ($this->recentKasKecilDuplicate($dupWhere, 90)) {
         echo "Data sudah di input";
         exit();
      }

      $ref = date('ymdHis') . rand(10, 99);
      $cols = 'id_sumber, id_target, tipe, ref, jumlah, st, note, tanggal';
      $vals = "'" . $idToko . "','" . $jenis . "',2,'" . $ref . "'," . $jumlah . ",0,'" . $note . "','" . $tanggal . "'";

      $do = $this->db(0)->insertCols('kas_kecil', $cols, $vals);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      echo 0;
   }

   function update()
   {
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         echo "Tidak diizinkan";
         exit();
      }

      $id = (int)($_POST['id'] ?? 0);
      $col = $_POST['col'] ?? '';
      $value = addslashes(trim($_POST['val'] ?? ''));
      $idToko = (int)$this->userData['id_toko'];

      if ($id <= 0 || $col !== 'note' || $value === '') {
         echo "Data tidak valid";
         exit();
      }

      $up = $this->db(0)->update(
         "kas_kecil",
         "note = '" . $value . "'",
         "id = '" . $id . "' AND id_sumber = " . $idToko . " AND tipe = 2 AND st = 0"
      );
      echo $up['errno'] == 0 ? 0 : $up['error'];
   }

   function delete()
   {
      header('Content-Type: application/json');
      if (!in_array($this->userData['user_tipe'], PV::PRIV[2])) {
         echo json_encode(['ok' => 0, 'error' => 'Tidak diizinkan']);
         exit();
      }

      $id = (int)($_POST['id'] ?? 0);
      $idToko = (int)$this->userData['id_toko'];
      if ($id <= 0) {
         echo json_encode(['ok' => 0, 'error' => 'ID tidak valid']);
         exit();
      }

      $del = $this->db(0)->delete_where(
         "kas_kecil",
         "id = " . $id . " AND id_sumber = " . $idToko . " AND st = 0 AND tipe = 2"
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

   private function normalizeYear($year)
   {
      $year = (int)$year;
      if ($year < 2000 || $year > 2100) {
         return (int)date('Y');
      }
      return $year;
   }

   private function normalizeYm($ym)
   {
      $ym = trim((string)$ym);
      if (!preg_match('/^\d{4}-\d{2}$/', $ym)) {
         return date('Y-m');
      }
      return $ym;
   }

   private function calcSaldo($idToko)
   {
      $topup = (int)$this->db(0)->sum_col_where(
         'kas_kecil',
         'jumlah',
         "id_target = " . (int)$idToko . " AND tipe = 1 AND st = 1"
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
