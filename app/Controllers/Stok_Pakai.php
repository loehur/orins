<?php

class Stok_Pakai extends Controller
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
         "title" => "Stok Pakai"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $data['stok_gudang'] = $this->data('Barang')->stok_data_list_all(0);
      $data['barang'] = $this->db(0)->get_where('master_barang', "en = 1 ORDER BY id DESC");
      $data['karyawan_toko'] = $this->db(0)->get_where('karyawan', "id_toko = " . $this->userData['id_toko'], 'id_karyawan');
      $data['akun_pakai'] = $this->db(0)->get('akun_pakai');
      $this->view($this->v_content, $data);
   }

   public function riwayat_pakai($period = 'this')
   {
      $period = ($period === 'last') ? 'last' : 'this';
      if ($period === 'last') {
         $startTime = date('Y-m-01 00:00:00', strtotime('first day of last month'));
         $endTime = date('Y-m-t 23:59:59', strtotime('last day of last month'));
      } else {
         $startTime = date('Y-m-01 00:00:00');
         $endTime = date('Y-m-t 23:59:59');
      }

      $where = "jenis = 4 AND id_sumber = 0 AND stat = 1 AND insertTime BETWEEN '" . $startTime . "' AND '" . $endTime . "' ORDER BY insertTime DESC";
      $data['riwayat'] = $this->db(0)->get_where('master_mutasi', $where);
      $data['barang'] = $this->db(0)->get('master_barang', 'id');
      $data['akun_pakai'] = $this->db(0)->get('akun_pakai', 'id');
      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');
      $data['period'] = $period;
      $this->view(__CLASS__ . '/riwayat_pakai', $data);
   }
}
