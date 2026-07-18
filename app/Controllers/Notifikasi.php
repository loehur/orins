<?php

class Notifikasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
   }

   /**
    * Payload Pusat Notifikasi (HTML).
    * - data-notif-count: jumlah notifikasi saja (prioritas TIDAK dihitung)
    * - #menuPrioritasItems: ikut terload untuk sync menu sidebar Prioritas
    * - Section: Cashier + Customer Service + Driver (semua user bisa lihat)
    */
   public function poll()
   {
      $title = isset($_GET['t']) ? $_GET['t'] : '';
      $data = [
         'title' => $title,
         'notifs_cashier' => [],
         'notifs_cs' => [],
         'notifs_driver' => [],
         'notif_cashier_c' => 0,
         'notif_cs_c' => 0,
         'notif_driver_c' => 0,
         'notif_c' => 0,
         'can_prioritas' => in_array($this->userData['user_tipe'], PV::PRIV[4]),
         'show_aff' => false,
         'show_spk' => false,
         'aff' => [],
         'aff_c' => 0,
         'list_l' => [],
         'lanjut_c' => 0,
      ];

      // Cashier: Barang Masuk status Checking → 1 notifikasi (klik ke Barang_Masuk)
      $whereBm = "(tipe = 1 OR tipe = 2) AND id_target = '" . $this->userData['id_toko'] . "' AND cek = 0";
      $bmChecking = (int) $this->db(0)->count_where('master_input', $whereBm);
      if ($bmChecking > 0) {
         $data['notifs_cashier'][] = [
            'title' => 'Barang Masuk perlu dicek',
            'body' => $bmChecking . ' dokumen menunggu konfirmasi',
            'link' => PV::BASE_URL . 'Barang_Masuk',
         ];
      }

      // CS: semua Afiliasi Order masuk → 1 notifikasi (klik ke order pertama)
      $whereAff = "id_afiliasi = " . $this->userData['id_toko'] . " AND id_penerima <> 0 AND (id_user_afiliasi = 0 OR status_order = 1) AND cancel = 0 GROUP BY id_toko, id_pelanggan, ref";
      $affList = $this->db(0)->get_cols_where('order_data', 'ref', $whereAff, 1);
      $affCount = (is_array($affList) && !isset($affList['errno'])) ? count($affList) : 0;
      if ($affCount > 0) {
         $firstRef = $affList[0]['ref'] ?? '';
         $data['notifs_cs'][] = [
            'title' => 'Afiliasi Order masuk',
            'body' => $affCount . ' order menunggu proses',
            'link' => PV::BASE_URL . 'Buka_Order_Aff/index/' . $firstRef,
         ];
      }

      $data['notif_cashier_c'] = count($data['notifs_cashier']);
      $data['notif_cs_c'] = count($data['notifs_cs']);
      $data['notif_driver_c'] = count($data['notifs_driver']);
      $data['notif_c'] = $data['notif_cashier_c'] + $data['notif_cs_c'] + $data['notif_driver_c'];

      if ($data['can_prioritas']) {
         $prio = $this->data('Prioritas')->menuData($this, $title);
         $data = array_merge($data, $prio);
         $data['can_prioritas'] = true;
      }

      $this->view('Menu/pusat_notifikasi', $data);
   }
}
