<?php

class Akun_Pembayaran extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[5])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "Finance - Akun Pembayaran"
      ]);
      $this->viewer();
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $idToko = (int)$this->userData['id_toko'];
      $data['list'] = $this->db(0)->get_where(
         'payment_account',
         "id_toko = " . $idToko . " ORDER BY payment_account ASC"
      );
      $this->view($this->v_content, $data);
   }

   public function update()
   {
      header('Content-Type: application/json; charset=utf-8');

      $id = (int)($_POST['id'] ?? 0);
      $col = trim((string)($_POST['col'] ?? ''));
      $value = trim((string)($_POST['value'] ?? ''));
      $idToko = (int)$this->userData['id_toko'];

      $allowed = ['payment_account', 'sds'];
      if ($id <= 0 || !in_array($col, $allowed, true)) {
         echo json_encode(['ok' => 0, 'error' => 'Data tidak valid']);
         exit();
      }

      $row = $this->db(0)->get_where_row('payment_account', "id = " . $id . " AND id_toko = " . $idToko);
      if (!is_array($row) || !isset($row['id'])) {
         echo json_encode(['ok' => 0, 'error' => 'Akun tidak ditemukan']);
         exit();
      }

      if ($col === 'payment_account') {
         $value = strtoupper($value);
         if ($value === '') {
            echo json_encode(['ok' => 0, 'error' => 'Nama akun tidak boleh kosong']);
            exit();
         }
         $set = "payment_account = '" . addslashes($value) . "'";
      } else {
         $sds = (int)$value;
         if (!in_array($sds, [0, 1, 2], true)) {
            echo json_encode(['ok' => 0, 'error' => 'Nilai SDS tidak valid']);
            exit();
         }
         $set = "sds = " . $sds;
         $value = (string)$sds;
      }

      $up = $this->db(0)->update('payment_account', $set, "id = " . $id . " AND id_toko = " . $idToko);
      if (($up['errno'] ?? 1) !== 0) {
         echo json_encode(['ok' => 0, 'error' => $up['error'] ?? 'Gagal update']);
         exit();
      }

      $this->model('Log')->write($this->userData['user'] . " Update payment_account #" . $id . " " . $col . "=" . $value);
      echo json_encode(['ok' => 1, 'value' => $value]);
   }
}
