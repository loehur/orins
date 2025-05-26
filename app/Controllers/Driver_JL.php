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

      //PRODUKSI
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

      //EKPEDISI
      $data['ea'] = $this->db(0)->get('expedisi_account', 'id');
      $data['ref_exp'] = $this->db(0)->get_where('ref', 'tuntas = 0 AND expedisi <> 0', 'ref');
      $refs_exp = array_keys($data['ref_exp']);
      $data_exp_p = [];
      $data_exp_b = [];

      if (count($refs_exp) > 0) {
         $ref_list = "";
         foreach ($refs_exp as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');
         $where = "ref IN (" . $ref_list . ") AND id_ambil = 0 AND tuntas = 0 AND cancel = 0";
         $data_exp_p = $this->db(0)->get_where('order_data', $where);
         $where = "ref IN (" . $ref_list . ") AND tuntas = 0 AND stat = 1";
         $data_exp_b = $this->db(0)->get_where('master_mutasi', $where);
      }

      $get_exp = [];
      foreach ($data_exp_p as $a) {
         $key = $a['id_toko'] . "#" . $data['ref_exp'][$a['ref']]['expedisi'];
         if (!isset($get_exp[$key][$a['ref']])) {
            $get_exp[$key][$a['ref']]['id_pelanggan'] = $a['id_pelanggan'];
            $get_exp[$key][$a['ref']]['qty'] = $a['jumlah'];
            $get_exp[$key][$a['ref']]['cs'] = $a['id_penerima'];
         } else {
            $get_exp[$key][$a['ref']]['qty'] += $a['jumlah'];
         }
      }

      foreach ($data_exp_b as $a) {
         $key = $a['id_sumber'] . "#" . $data['ref_exp'][$a['ref']]['expedisi'];
         if (!isset($get_exp[$key][$a['ref']])) {
            $get_exp[$key][$a['ref']]['id_pelanggan'] = $a['id_target'];
            $get_exp[$key][$a['ref']]['qty'] = $a['qty'];
            $get_exp[$key][$a['ref']]['cs'] = $a['cs_id'];
         } else {
            $get_exp[$key][$a['ref']]['qty'] += $a['qty'];
         }
      }

      $data['jl_exp'] = $get_exp;

      // transfer stok
      $data_tfstok = $this->db(0)->get_where('master_input', 'tipe = 1 AND delivery = 1 AND id_driver = 0');
      $get_ts = [];
      foreach ($data_tfstok as $a) {
         $key = $a['id_sumber'] . "#" . $a['id_target'];
         $ref = $a['id'];
         $jumlah[$ref] = $this->db(0)->sum_col_where('master_mutasi', "qty", "stat <> 2 AND ref = '" . $ref . "'");

         if (!isset($get_ts[$key][$ref])) {
            $get_ts[$key][$ref]['id_pelanggan'] = $a['id_target'];
            $get_ts[$key][$ref]['qty'] = $jumlah[$ref];
            $get_ts[$key][$ref]['cs'] = 0;
         } else {
            $get_ts[$key][$ref]['qty'] +=  $jumlah[$ref];
         }
      }

      $data['jl_ts'] = $get_ts;

      $this->view($this->v_content, $data);
   }
}
