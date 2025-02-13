<?php

class Petty_Cash_F extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
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
      $whereTopup = "id_target = " . $this->userData['id_toko'] . " AND tipe = 1 AND st = 1";
      $topup = $this->db(0)->sum_col_where('kas_kecil', 'jumlah', $whereTopup);

      $wherePakai = "id_sumber = " . $this->userData['id_toko'] . " AND tipe = 2 AND st <> 2";
      $pakai = $this->db(0)->sum_col_where('kas_kecil', 'jumlah', $wherePakai);

      $whereTopupMutasi = "id_target = " . $this->userData['id_toko'] . " AND tipe = 1 ORDER BY insertTime DESC";
      $data['topup'] = $this->db(0)->get_where('kas_kecil', $whereTopupMutasi);

      $wherePakaiMutasi = "id_sumber = " . $this->userData['id_toko'] . " AND tipe = 2 ORDER BY insertTime DESC";
      $data['pakai'] = $this->db(0)->get_where('kas_kecil', $wherePakaiMutasi);

      $data['jkeluar'] = $this->db(0)->get('pengeluaran_jenis', 'id');

      $data['saldo'] = $topup - $pakai;
      $this->view(__CLASS__ . '/content', $data);
   }

   function verify($id, $status)
   {
      $set = "st = '" . $status . "'";
      $where = "id = '" . $id . "'";
      $update = $this->db(0)->update("kas_kecil", $set, $where);
      echo $update['errno'] == 0 ? 0 : $update['error'];
   }
}
