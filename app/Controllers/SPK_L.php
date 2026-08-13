<?php

class SPK_L extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
      if (!in_array($this->userData['user_tipe'], PV::PRIV[4])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($dvs)
   {
      foreach ($this->dDvs as $dv) {
         if ($dv['id_divisi'] == $dvs) {
            $t = $dv['divisi'];
         }
      }

      $this->view("Layouts/layout_main", [
         "content" => $this->v_content,
         "title" => "SPK - Lanjutan " . $t
      ]);

      $this->viewer($dvs);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['parse'] = $parse;
      $data['spk_pending'] = $this->db(0)->get('spk_pending', 'id');
      $data['pelanggan'] = $this->db(0)->get('pelanggan', 'id_pelanggan');

      $data['karyawan'] = $this->db(0)->get('karyawan', 'id_karyawan');

      $dvs = '"D-' . $parse . '"';

      $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND cancel = 0 AND id_ambil = 0 AND spk_lanjutan LIKE '%D-" . $parse . "#%' AND spk_dvs LIKE '%" . $dvs . "%' ORDER BY id_order_data DESC";
      $orders = $this->db(0)->get_where('order_data', $where);
      if (!is_array($orders) || isset($orders['errno'])) {
         $orders = [];
      }

      // Gabungkan tahap bertahap yang di-push ke lanjutan
      $whereBt = "(spk_lanjutan LIKE '%D-" . $parse . "#%')";
      $btRows = $this->db(0)->get_where('spk_bertahap', $whereBt);
      if (!is_array($btRows) || isset($btRows['errno'])) {
         $btRows = [];
      }
      $parentIds = [];
      foreach ($orders as $do) {
         $parentIds[] = (int) $do['id_order_data'];
      }
      foreach ($btRows as $st) {
         $parentIds[] = (int) $st['id_order_data'];
      }
      $parentIds = array_values(array_unique(array_filter($parentIds)));
      $parents = [];
      if (count($parentIds) > 0) {
         $pRows = $this->db(0)->get_where('order_data', 'id_order_data IN (' . implode(',', $parentIds) . ') AND cancel = 0 AND id_ambil = 0 AND (id_toko = ' . $this->userData['id_toko'] . ' OR id_afiliasi = ' . $this->userData['id_toko'] . ')');
         if (is_array($pRows) && !isset($pRows['errno'])) {
            foreach ($pRows as $p) {
               $parents[(int)$p['id_order_data']] = $p;
            }
         }
      }

      $stagesByOrder = [];
      if (count($parentIds) > 0) {
         $allStages = $this->db(0)->get_where('spk_bertahap', 'id_order_data IN (' . implode(',', $parentIds) . ') ORDER BY tahap ASC');
         $stagesByOrder = $this->model('SpkBertahap')->groupByOrderId($allStages);
      }

      // Base: order biasa (induk tanpa tahapan) yang punya push
      $baseOrders = [];
      foreach ($orders as $do) {
         $oid = (int) $do['id_order_data'];
         if (!isset($stagesByOrder[$oid]) || count($stagesByOrder[$oid]) === 0) {
            $baseOrders[] = $do;
         }
      }
      $expanded = $this->model('SpkBertahap')->expandOrdersForSpk($baseOrders, []);

      // Tambah tahap yang di-push
      foreach ($btRows as $st) {
         $oid = (int) $st['id_order_data'];
         if (!isset($parents[$oid])) {
            continue;
         }
         $sumQty = isset($stagesByOrder[$oid]) ? $this->model('SpkBertahap')->sumQty($stagesByOrder[$oid]) : (int)$st['qty'];
         $expanded[] = $this->model('SpkBertahap')->buildVirtualFromStage($parents[$oid], $st, $sumQty);
      }

      $data['order'] = $expanded;

      $data_ = [];
      foreach ($data['order'] as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }
      $col = [];
      $actif_col = 1;
      $col[1] = 0;
      $col[2] = 0;

      $data_fix[1] = [];
      $data_fix[2] = [];

      foreach ($data_ as $key => $d) {
         if ($col[1] <= $col[2] + 1) {
            $actif_col = 1;
         } else {
            $actif_col = 2;
         }
         $col[$actif_col] += count($data_[$key]);

         $data_fix[$actif_col][$key] = $d;
      }
      $data['order'] = $data_fix;
      $this->view($this->v_content, $data);
   }
}
