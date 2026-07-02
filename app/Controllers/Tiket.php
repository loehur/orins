<?php

class Tiket extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   private function isDev()
   {
      return in_array($this->userData['user_tipe'], PV::PRIV[0]);
   }

   private function ticketScopeWhere($alias = '')
   {
      $prefix = $alias !== '' ? $alias . '.' : '';
      if ($this->isDev()) {
         return '1=1';
      }
      return $prefix . "id_toko = " . (int) $this->userData['id_toko'];
   }

   private function getTicketRow($id_tiket)
   {
      $where = "id_tiket = " . (int) $id_tiket . " AND " . $this->ticketScopeWhere();
      return $this->db(0)->get_where_row('tiket', $where);
   }

   private function canReply(array $ticket)
   {
      if ((int) $ticket['status'] !== 0) {
         return false;
      }
      return $this->isDev() || (int) $ticket['id_user'] === (int) $this->userData['id_user'];
   }

   public function index($mode = "proses")
   {
      if (!in_array($mode, ['proses', 'selesai'], true)) {
         $mode = 'proses';
      }

      $title = $mode === 'selesai' ? 'Tiket - Selesai' : 'Tiket - Proses';

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => $title
      ]);

      $this->viewer($mode);
   }

   public function viewer($mode = "proses", $month = "")
   {
      $this->view($this->v_viewer, [
         "controller" => __CLASS__,
         "parse" => $mode,
         "parse_2" => $month
      ]);
   }

   public function content($mode = "proses", $month = "", $nav = 0)
   {
      if (!in_array($mode, ['proses', 'selesai'], true)) {
         $mode = 'proses';
      }

      $data['mode'] = $mode;
      $data['is_dev'] = $this->isDev();
      $data['karyawan_form'] = $this->db(0)->get_where(
         'karyawan',
         "id_toko = " . (int) $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC",
         'id_karyawan'
      );
      if ($this->isDev()) {
         $data['karyawan'] = $this->db(0)->get_where('karyawan', "en = 1 ORDER BY nama ASC", 'id_karyawan');
      } else {
         $data['karyawan'] = $this->db(0)->get_where('karyawan', "id_toko = " . (int) $this->userData['id_toko'] . " AND en = 1 ORDER BY nama ASC", 'id_karyawan');
      }
      $data['users'] = $this->db(0)->get('user', 'id_user');

      if ($mode === 'proses') {
         $where = "status = 0 AND " . $this->ticketScopeWhere() . " ORDER BY id_tiket DESC";
         $data['tiket'] = $this->db(0)->get_where('tiket', $where);
      } else {
         if ($month === "") {
            $month = date('Y-m');
         }
         if ((int) $nav === 1) {
            $month = date('Y-m', strtotime('-1 month', strtotime($month . '-01')));
         } elseif ((int) $nav === 2) {
            $month = date('Y-m', strtotime('+1 month', strtotime($month . '-01')));
         }

         $data['month'] = $month;
         $where = "status = 1 AND selesai_time LIKE '" . addslashes($month) . "%' AND " . $this->ticketScopeWhere() . " ORDER BY selesai_time DESC";
         $data['tiket'] = $this->db(0)->get_where('tiket', $where);
      }

      $this->view($this->v_content, $data);
   }

   public function detail($id_tiket = 0)
   {
      $ticket = $this->getTicketRow($id_tiket);
      if (!isset($ticket['id_tiket'])) {
         echo '<div class="alert alert-danger m-3">Tiket tidak ditemukan.</div>';
         return;
      }

      $data['ticket'] = $ticket;
      $data['is_dev'] = $this->isDev();
      $data['can_reply'] = $this->canReply($ticket);
      $data['can_complete'] = $this->isDev() && (int) $ticket['status'] === 0;
      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');
      $data['users'] = $this->db(0)->get('user', 'id_user');
      $data['replies'] = $this->db(0)->get_where('tiket_reply', "id_tiket = " . (int) $id_tiket . " ORDER BY id_reply ASC");

      $this->view(__CLASS__ . '/detail', $data);
   }

   private function isRecentTiketDuplicate($id_karyawan, $judul, $tipe, $isi, $seconds = 90)
   {
      $since = date('Y-m-d H:i:s', time() - (int) $seconds);
      $where = "id_user = " . (int) $this->userData['id_user']
         . " AND id_karyawan = " . (int) $id_karyawan
         . " AND judul = '" . addslashes($judul) . "'"
         . " AND tipe = " . (int) $tipe
         . " AND isi = '" . addslashes($isi) . "'"
         . " AND status = 0"
         . " AND insertTime >= '" . $since . "'";

      return $this->db(0)->count_where('tiket', $where) > 0;
   }

   private function tiketBadgeClass($tipe)
   {
      if ((int) $tipe === 2) {
         return 'tiket-badge-fitur';
      }
      if ((int) $tipe === 3) {
         return 'tiket-badge-usulan';
      }
      return 'tiket-badge-perbaikan';
   }

   private function tiketTipeLabel($tipe)
   {
      $labels = [1 => 'Perbaikan', 2 => 'Fitur Baru', 3 => 'Usulan'];
      return $labels[(int) $tipe] ?? '-';
   }

   private function formatTicketRow(array $ticket, array $karyawanMap, array $userMap)
   {
      $idToko = (int) $ticket['id_toko'];
      $tokoInisial = $this->dToko[$idToko]['inisial'] ?? (string) $idToko;

      return [
         'id_tiket' => (int) $ticket['id_tiket'],
         'waktu' => date('d/m/y H:i', strtotime($ticket['insertTime'])),
         'judul' => $ticket['judul'],
         'tipe' => (int) $ticket['tipe'],
         'tipe_label' => $this->tiketTipeLabel($ticket['tipe']),
         'badge_class' => $this->tiketBadgeClass($ticket['tipe']),
         'karyawan' => $karyawanMap[$ticket['id_karyawan']]['nama'] ?? '-',
         'pembuat' => $userMap[$ticket['id_user']]['nama'] ?? $userMap[$ticket['id_user']]['user'] ?? '-',
         'toko' => $tokoInisial,
      ];
   }

   private function jsonOut(array $payload)
   {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($payload);
      exit();
   }

   public function create()
   {
      $id_karyawan = (int) ($_POST['id_karyawan'] ?? 0);
      $judul = trim($_POST['judul'] ?? '');
      $tipe = (int) ($_POST['tipe'] ?? 0);
      $isi = trim($_POST['isi'] ?? '');

      if ($id_karyawan <= 0 || $judul === '' || !in_array($tipe, [1, 2, 3], true) || $isi === '') {
         $this->jsonOut(['ok' => 0, 'error' => 'Lengkapi semua data tiket.']);
      }

      $karyawan = $this->db(0)->get_where_row('karyawan', "id_karyawan = " . $id_karyawan . " AND id_toko = " . (int) $this->userData['id_toko']);
      if (!isset($karyawan['id_karyawan'])) {
         $this->jsonOut(['ok' => 0, 'error' => 'Karyawan tidak valid.']);
      }

      if ($this->isRecentTiketDuplicate($id_karyawan, $judul, $tipe, $isi)) {
         $this->jsonOut(['ok' => 1, 'duplicate' => 1]);
      }

      $cols = 'id_karyawan, id_user, id_toko, judul, tipe, isi, status';
      $vals = $id_karyawan . "," . (int) $this->userData['id_user'] . "," . (int) $this->userData['id_toko']
         . ",'" . addslashes($judul) . "'," . $tipe . ",'" . addslashes($isi) . "',0";

      $do = $this->db(0)->insertCols('tiket', $cols, $vals);
      if ($do['errno'] != 0) {
         $this->jsonOut(['ok' => 0, 'error' => $do['error']]);
      }

      $id_tiket = (int) $this->db(0)->scalar("SELECT LAST_INSERT_ID()");
      $ticket = $this->db(0)->get_where_row('tiket', "id_tiket = " . $id_tiket);
      $karyawanMap = $this->db(0)->get('karyawan', 'id_karyawan');
      $userMap = $this->db(0)->get('user', 'id_user');

      $this->jsonOut([
         'ok' => 1,
         'tiket' => $this->formatTicketRow($ticket, $karyawanMap, $userMap),
      ]);
   }

   public function reply()
   {
      $id_tiket = (int) ($_POST['id_tiket'] ?? 0);
      $isi = trim($_POST['isi'] ?? '');

      if ($id_tiket <= 0 || $isi === '') {
         $this->jsonOut(['ok' => 0, 'error' => 'Balasan tidak boleh kosong.']);
      }

      $ticket = $this->getTicketRow($id_tiket);
      if (!isset($ticket['id_tiket'])) {
         $this->jsonOut(['ok' => 0, 'error' => 'Tiket tidak ditemukan.']);
      }

      if (!$this->canReply($ticket)) {
         $this->jsonOut(['ok' => 0, 'error' => 'Anda tidak dapat membalas tiket ini.']);
      }

      $cols = 'id_tiket, id_user, isi';
      $vals = $id_tiket . "," . (int) $this->userData['id_user'] . ",'" . addslashes($isi) . "'";
      $do = $this->db(0)->insertCols('tiket_reply', $cols, $vals);
      if ($do['errno'] != 0) {
         $this->jsonOut(['ok' => 0, 'error' => $do['error']]);
      }

      $this->jsonOut([
         'ok' => 1,
         'reply' => [
            'nama' => $this->userData['nama'] ?? $this->userData['user'],
            'is_dev' => $this->isDev() ? 1 : 0,
            'waktu' => date('d/m/y H:i'),
            'isi' => $isi,
         ],
      ]);
   }

   public function selesai()
   {
      if (!$this->isDev()) {
         $this->jsonOut(['ok' => 0, 'error' => 'Hanya developer yang dapat menyelesaikan tiket.']);
      }

      $id_tiket = (int) ($_POST['id_tiket'] ?? 0);
      $ticket = $this->getTicketRow($id_tiket);

      if (!isset($ticket['id_tiket'])) {
         $this->jsonOut(['ok' => 0, 'error' => 'Tiket tidak ditemukan.']);
      }

      if ((int) $ticket['status'] === 1) {
         $this->jsonOut(['ok' => 0, 'error' => 'Tiket sudah selesai.']);
      }

      $set = "status = 1, selesai_oleh = " . (int) $this->userData['id_user'] . ", selesai_time = NOW()";
      $up = $this->db(0)->update('tiket', $set, "id_tiket = " . $id_tiket);
      if ($up['errno'] != 0) {
         $this->jsonOut(['ok' => 0, 'error' => $up['error']]);
      }

      $this->jsonOut(['ok' => 1, 'id_tiket' => $id_tiket]);
   }
}
