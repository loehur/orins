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
    */
   public function poll()
   {
      $title = isset($_GET['t']) ? $_GET['t'] : '';
      $data = [
         'title' => $title,
         'notifs' => [],
         'notif_c' => 0,
         'can_prioritas' => in_array($this->userData['user_tipe'], PV::PRIV[4]),
         'show_aff' => false,
         'show_spk' => false,
         'aff' => [],
         'aff_c' => 0,
         'list_l' => [],
         'lanjut_c' => 0,
      ];

      // Slot notifikasi umum (belum ada sumber) — prioritas tidak masuk count.
      // Tambah sumber di sini nanti; set $data['notifs'] dan $data['notif_c'].

      if ($data['can_prioritas']) {
         $prio = $this->data('Prioritas')->menuData($this, $title);
         $data = array_merge($data, $prio);
         $data['can_prioritas'] = true;
      }

      $this->view('Menu/pusat_notifikasi', $data);
   }
}
