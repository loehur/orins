<?php

class Deposit extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[3])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }
   }

   public function i($mode = 1)
   {
      if ($mode == 1) {
         $page = 'deposit';
         $title = 'Topup';
      } else {
         $page = 'list';
         $title = 'List';
      }

      $this->view("Layouts/layout_main", ["title" => "Deposit - " . $title]);
      $this->view("Layouts/viewer", [
         "controller" => __CLASS__,
         "page" => $page,
         "parse" => ''
      ]);
   }

   public function deposit($parse = "")
   {
      $this->view(__CLASS__ . "/content", ['id_pelanggan' => $parse]);
   }

   function dep_data($id_pelanggan = 0)
   {
      $data['id_pelanggan'] = $id_pelanggan;
      $data['data'] = $this->db(0)->get_where("kas", "jenis_transaksi = 2 AND id_client = " . $id_pelanggan . " ORDER BY id_kas DESC LIMIT 10");
      $data['mutasi'] = $this->db(0)->get_where("kas", "metode_mutasi = 4 AND id_client = " . $id_pelanggan . " ORDER BY id_kas DESC LIMIT 10");
      $data['saldo'] = $this->data("Saldo")->deposit($id_pelanggan);
      $data['refs'] = [];

      if (count($data['mutasi']) > 0) {
         $ref_list = "";
         foreach ($data['mutasi'] as $r) {
            $ref_list .= $r['ref_transaksi'] . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $where = "ref IN (" . $ref_list . ")";
         $data['refs'] = $this->db(0)->get_where('ref', $where, 'ref');
      }

      $this->view(__CLASS__ . "/data", $data);
   }

   function topup($id_pelanggan)
   {
      $jumlah = $_POST['jumlah'];
      if ($jumlah < 1) {
         echo "Jumlah minimal 1";
         exit();
      }
      if ($id_pelanggan == 0) {
         echo "Data pelanggan tidak ditemukan";
         exit();
      }

      $ref_bayar = date("ymdhis") . rand(0, 9);
      $metode = $_POST['metode'];
      $note = $_POST['catatan'];
      $now = date('Y-m-d H:i');
      $id_karyawan = $_POST['id_karyawan'];

      $status = $metode == 1 ? 1 : 0;

      //cek double
      $cek = $this->db(0)->count_where('kas', "jumlah = " . $jumlah . " AND insertTime LIKE '" . substr($now, 0, 16) . "%'");
      if ($cek == 0) {
         $cols = "jenis_transaksi, jumlah, note, id_client, metode_mutasi, status_mutasi, jenis_mutasi, id_user, id_toko, ref_bayar";
         $vals = "2," . $jumlah . ",'" . $note . "'," . $id_pelanggan . "," . $metode . "," . $status . ",1," . $id_karyawan . "," . $this->userData['id_toko'] . ", '" . $ref_bayar . "'";
         $in = $this->db(0)->insertCols('kas', $cols, $vals);
         echo $in['errno'] == 0 ? 0 : $in['error'];
      }
   }

   public function list($parse = "")
   {
      echo "<br><span class='ms-3'>Deposit List is under construction (#Luhur)</span>";
   }
}
