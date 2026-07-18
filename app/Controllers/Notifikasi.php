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

      // Slot sumber notifikasi (belum diisi) — prioritas tidak masuk count.
      // Isi $data['notifs_cashier'] / $data['notifs_cs'] / $data['notifs_driver'] lalu update count-nya.

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
