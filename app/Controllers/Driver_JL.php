<?php

class Driver_JL extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[9])) {
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
         "title" => "Driver - Pickup List"
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $cols = "CONCAT(id_afiliasi,'#',id_toko) as unic, id_toko, id_afiliasi, ready_aff_cs, ready_aff_date, id_pelanggan, jumlah, ref, id_user_afiliasi";
      $where = "cancel = 0 AND id_afiliasi <> 0 AND id_ambil_aff = 0 AND id_ambil = 0 AND tuntas = 0 AND ready_aff_cs <> 0 ORDER BY ready_aff_date DESC";
      $data['jl_pro'] = $this->db(0)->get_cols_where('order_data', $cols, $where, 1, 'unic', 1);

      $get = [];
      foreach ($data['jl_pro'] as $key => $jlp) {
         foreach ($jlp as $a) {
            if (!isset($get[$key][$a['ref']])) {
               $get[$key][$a['ref']]['id_pelanggan'] = $a['id_pelanggan'];
               $get[$key][$a['ref']]['qty'] = $a['jumlah'];
               $get[$key][$a['ref']]['cs'] = $a['id_user_afiliasi'];
            } else {
               $get[$key][$a['ref']]['qty'] += $a['jumlah'];
            }
         }
      }

      $data['ref_pro'] = $get;
      $this->view($this->v_content, $data);
   }
}
