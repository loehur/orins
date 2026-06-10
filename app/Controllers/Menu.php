<?php

class Menu extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();
   }

   public function prioritas($title = '')
   {
      $data['title'] = urldecode($title);
      $data['aff'] = [];
      $data['aff_c'] = 0;
      $data['list_l'] = [];
      $data['lanjut_c'] = 0;

      if (in_array($this->userData['user_tipe'], PV::PRIV[3])) {
         $cols = "id_toko, id_pelanggan, ref";
         $where = "id_afiliasi = " . $this->userData['id_toko'] . " AND id_penerima <> 0 AND (id_user_afiliasi = 0 OR status_order = 1) AND cancel = 0 GROUP BY id_toko, id_pelanggan, ref";
         $data['aff'] = $this->db(0)->get_cols_where('order_data', $cols, $where, 1);
         $data['aff_c'] = count($data['aff']);
      }

      if (in_array($this->userData['user_tipe'], PV::PRIV[4])) {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND cancel = 0 AND id_ambil = 0 AND spk_lanjutan <> '' ORDER BY id_order_data DESC";
         $data_spk_lnjut = $this->db(0)->get_cols_where('order_data', 'ref, spk_lanjutan, spk_dvs', $where, 1);

         $refs_spk_lnjut = [];
         $list_l = [];
         foreach ($data_spk_lnjut as $ds) {
            $spk = explode('#', str_replace('D-', '', $ds['spk_lanjutan'] ?? ''));
            $spk_dvs = (strlen($ds['spk_dvs'] ?? '') > 1) ? @unserialize($ds['spk_dvs']) : [];
            if (!is_array($spk_dvs)) {
               $spk_dvs = [];
            }
            $ada_pending = false;
            foreach ($spk as $sl) {
               if ($sl !== '' && isset($this->dDvs[$sl])) {
                  $list_l[$sl] = 1;
                  $dv = $spk_dvs[$sl] ?? [];
                  $status = (int)($dv['status'] ?? 0);
                  $cm = (int)($dv['cm'] ?? 0);
                  $cm_status = (int)($dv['cm_status'] ?? 0);
                  $done = ($status == 1 && ($cm != 1 || $cm_status == 1));
                  if (!$done) {
                     $ada_pending = true;
                  }
               }
            }
            if ($ada_pending) {
               $refs_spk_lnjut[$ds['ref']] = 1;
            }
         }
         $data['list_l'] = array_keys($list_l);
         $data['lanjut_c'] = count($refs_spk_lnjut);
      }

      $this->view('Menu/prioritas', $data);
   }
}
