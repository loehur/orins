<?php

class Home extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();


      if ($this->userData['user_tipe'] == 9) {
         header('Location: ' . PV::BASE_URL . "Driver_JL");
      }

      $this->v_load = __CLASS__ . "/load";
      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index()
   {
      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => __CLASS__
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => ""]);
   }

   public function content()
   {
      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC LIMIT 5";
      $cs = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_pro DESC LIMIT 5";
      $pro = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $whereKaryawan =  "en = 1 ORDER BY freq_driver DESC LIMIT 5";
      $dr = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $data['cs'] = [];
      $data['cs_data'] = [];
      $data['pro'] = [];
      $data['pro_data'] = [];
      $data['dr'] = [];
      $data['dr_data'] = [];

      foreach ($cs as $c) {
         array_push($data['cs'], ucwords($c['nama']));
         array_push($data['cs_data'], $c['freq_cs']);
      }

      foreach ($pro as $c) {
         array_push($data['pro'], ucwords($c['nama']));
         array_push($data['pro_data'], $c['freq_pro']);
      }

      foreach ($dr as $c) {
         array_push($data['dr'], ucwords($c['nama']));
         array_push($data['dr_data'], $c['freq_driver']);
      }

      $this->view($this->v_content, $data);
   }

   public function menu_prioritas()
   {
      if (!in_array($this->userData['user_tipe'], PV::PRIV[3]) && !in_array($this->userData['user_tipe'], PV::PRIV[4])) {
         http_response_code(403);
         exit();
      }

      $data['title'] = isset($_GET['t']) ? $_GET['t'] : '';
      $data['show_aff'] = in_array($this->userData['user_tipe'], PV::PRIV[3]);
      $data['show_spk'] = in_array($this->userData['user_tipe'], PV::PRIV[4]);
      $data['aff'] = [];
      $data['aff_c'] = 0;
      $data['list_l'] = [];
      $data['lanjut_c'] = 0;

      if ($data['show_aff']) {
         $cols = "id_toko, id_pelanggan, ref";
         $where = "id_afiliasi = " . $this->userData['id_toko'] . " AND id_penerima <> 0 AND (id_user_afiliasi = 0 OR status_order = 1) AND cancel = 0 GROUP BY id_toko, id_pelanggan, ref";
         $aff = $this->db(0)->get_cols_where('order_data', $cols, $where, 1);
         if (is_array($aff) && !isset($aff['errno'])) {
            $data['aff'] = $aff;
            $data['aff_c'] = count($aff);
         }
      }

      if ($data['show_spk']) {
         $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND cancel = 0 AND id_ambil = 0 AND spk_lanjutan <> '' ORDER BY id_order_data DESC";
         $data_spk_lnjut = $this->db(0)->get_cols_where('order_data', 'ref, spk_lanjutan, spk_dvs', $where, 1);
         if (is_array($data_spk_lnjut) && !isset($data_spk_lnjut['errno'])) {
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
      }

      $this->view('Menu/prioritas', $data);
   }
}
