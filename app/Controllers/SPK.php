<?php

class SPK extends Controller
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
         "title" => "SPK_R - " . $t
      ]);

      $this->viewer($dvs);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   private function loadExpandedOrders($where)
   {
      $orders = $this->db(0)->get_where('order_data', $where);
      if (!is_array($orders) || isset($orders['errno'])) {
         $orders = [];
      }

      $ids = [];
      foreach ($orders as $do) {
         if (isset($do['id_order_data'])) {
            $ids[] = (int) $do['id_order_data'];
         }
      }
      $ids = array_values(array_unique(array_filter($ids)));
      $stagesByOrder = [];
      if (count($ids) > 0) {
         $rows = $this->db(0)->get_where('spk_bertahap', 'id_order_data IN (' . implode(',', $ids) . ') ORDER BY tahap ASC');
         $stagesByOrder = $this->model('SpkBertahap')->groupByOrderId($rows);
      }

      return $this->model('SpkBertahap')->expandOrdersForSpk($orders, $stagesByOrder);
   }

   private function resolveCekRow($cekId)
   {
      $parsed = $this->model('SpkBertahap')->parseCekId($cekId);
      if ($parsed['type'] === 'bertahap') {
         $st = $this->db(0)->get_where_row('spk_bertahap', 'id = ' . $parsed['id']);
         if (!isset($st['id'])) {
            return null;
         }
         $parent = $this->db(0)->get_where_row('order_data', 'id_order_data = ' . (int) $st['id_order_data']);
         if (!isset($parent['id_order_data'])) {
            return null;
         }
         $siblings = $this->db(0)->get_where('spk_bertahap', 'id_order_data = ' . (int) $st['id_order_data']);
         $sumQty = $this->model('SpkBertahap')->sumQty(is_array($siblings) ? $siblings : []);
         return [
            'parsed' => $parsed,
            'row' => $this->model('SpkBertahap')->buildVirtualFromStage($parent, $st, $sumQty),
            'stage' => $st,
            'parent' => $parent,
         ];
      }

      $row = $this->db(0)->get_where_row('order_data', 'id_order_data = ' . $parsed['id']);
      if (!isset($row['id_order_data'])) {
         return null;
      }
      $row['cek_id'] = (string) $row['id_order_data'];
      $row['bertahap'] = null;
      return [
         'parsed' => $parsed,
         'row' => $row,
         'stage' => null,
         'parent' => $row,
      ];
   }

   public function content($parse = "")
   {
      $data['id_divisi'] = $parse;

      $data['pelanggan'] = $this->db(0)->get('pelanggan');

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_pro DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan);

      $dvs = '"D-' . $parse . '"';
      $where = "(id_toko = " . $this->userData['id_toko'] . " OR id_afiliasi = " . $this->userData['id_toko'] . ") AND id_pelanggan <> 0 AND tuntas = 0 AND cancel = 0 AND spk_dvs LIKE '%" . $dvs . "%' ORDER BY id_order_data DESC";
      $expanded = $this->loadExpandedOrders($where);

      $recap = [];
      $recap_2 = [];

      foreach ($expanded as $do) {
         $spk = @unserialize($do['spk_dvs']);
         if (!is_array($spk) || !isset($spk[$parse])) {
            continue;
         }
         $spk_code = "";
         $spk_text = "";
         $cekId = isset($do['cek_id']) ? $do['cek_id'] : (string) $do['id_order_data'];

         if ($spk[$parse]['status'] == 0) {
            foreach ($spk as $s_key => $sp) {
               if ($s_key == $parse) {
                  foreach ($sp['spk'] as $key_ => $sp_) {
                     $spk_code .= "-" . $key_;
                     $spk_text .= $sp_ . " ";
                  }
               }
            }

            if (isset($recap[$spk_code])) {
               $recap[$spk_code]['order'] .= "," . $cekId;
               $recap[$spk_code]['jumlah'] += $do['jumlah'];
            } else {
               $recap[$spk_code]['order'] = $cekId;
               $recap[$spk_code]['spk'] = $spk_text;
               $recap[$spk_code]['jumlah'] = $do['jumlah'];
            }
         } else {
            if ($spk[$parse]['cm'] == 1) {
               if ($spk[$parse]['cm_status'] == 0) {
                  foreach ($spk as $s_key => $sp) {
                     if ($s_key == $parse) {
                        foreach ($sp['spk'] as $key_ => $sp_) {
                           $spk_code .= "-" . $key_;
                           $spk_text .= $sp_ . " ";
                        }
                     }
                  }

                  if (isset($recap_2[$spk_code])) {
                     $recap_2[$spk_code]['order'] .= "," . $cekId;
                     $recap_2[$spk_code]['jumlah'] += $do['jumlah'];
                  } else {
                     $recap_2[$spk_code]['order'] = $cekId;
                     $recap_2[$spk_code]['spk'] = $spk_text;
                     $recap_2[$spk_code]['jumlah'] = $do['jumlah'];
                  }
               }
            }
         }
      }

      $data_ = [];
      foreach ($expanded as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }

      $col = [];
      $actif_col = 1;
      $col[1] = 0;
      $col[2] = 0;

      $data_fix[1] = [];
      $data_fix[2] = [];

      foreach ($data_ as $key => $d) {
         if ($col[1] <= $col[2]) {
            $actif_col = 1;
         } else {
            $actif_col = 2;
         }
         $col[$actif_col] += count($data_[$key]);

         $data_fix[$actif_col][$key] = $d;
      }

      $data['order'] = $data_fix;
      $data['recap'] = $recap;
      $data['recap_2'] = $recap_2;

      $this->view($this->v_content, $data);
   }

   function load_update($order)
   {
      $data = explode(",", $order);

      $data_ = [];

      foreach ($data as $d) {
         $d = trim($d);
         if ($d === '') {
            continue;
         }
         $resolved = $this->resolveCekRow($d);
         if ($resolved === null) {
            continue;
         }
         $data_[$d] = $resolved['row'];
      }

      $data['pelanggan'] = $this->db(0)->get('pelanggan');

      $data['order'] = $data_;
      $this->view(__CLASS__ . "/update", $data);
   }

   function cekSPK($order, $parse)
   {
      $data['produk'] = $this->db(0)->get('produk', 'id_produk');
      $data['spk_pending'] = $this->db(0)->get('spk_pending', 'id');
      $data_get = explode(",", $order);

      $data['order'] = [];
      foreach ($data_get as $d) {
         $d = trim($d);
         if ($d === '') {
            continue;
         }
         $resolved = $this->resolveCekRow($d);
         if ($resolved === null) {
            continue;
         }
         array_push($data['order'], $resolved['row']);
      }

      $data_ = [];
      foreach ($data['order'] as $key => $do) {
         $data_[$do['ref']][$key] = $do;
      }

      $data['order'] = $data_;
      $data['pelanggan'] = $this->db(0)->get('pelanggan');

      $whereKaryawan =  "en = 1 ORDER BY freq_pro DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan, "id_karyawan");

      $data['parse'] = $parse;
      $this->view(__CLASS__ . "/cek", $data);
   }

   function updateSPK($id_divisi, $tahap = 1)
   {
      $karyawan = $_POST['id_karyawan'];

      if (!isset($_POST['cek'])) {
         echo "Ceklis terlebih orderan yang ingin diselesaikan";
         exit();
      }

      //updateFreqPro
      $this->db(0)->update("karyawan", "freq_pro = freq_pro+1", "id_karyawan = " . $karyawan);

      $cek = $_POST['cek'];
      $date = date("Y-m-d h:i:s");

      if (count($cek) > 0) {
         foreach ($cek as $c) {
            $resolved = $this->resolveCekRow($c);
            if ($resolved === null) {
               echo "Item tidak ditemukan: " . $c;
               exit();
            }

            $data = @unserialize($resolved['row']['spk_dvs']);
            if (!is_array($data)) {
               echo "Data SPK tidak valid";
               exit();
            }

            if ($tahap == 1) {
               $data[$id_divisi]["status"] = 1;
               $data[$id_divisi]["user_produksi"] = $karyawan;
               $data[$id_divisi]["update"] = $date;
            } else {
               $data[$id_divisi]["cm_status"] = 1;
               $data[$id_divisi]["user_cm"] = $karyawan;
               $data[$id_divisi]["update_cm"] = $date;
            }

            $set = "spk_dvs = '" . serialize($data) . "', spk_lanjutan = REPLACE(spk_lanjutan, 'D-" . $id_divisi . "#', '')";
            if ($resolved['parsed']['type'] === 'bertahap') {
               $where = "id = " . $resolved['parsed']['id'];
               $do = $this->db(0)->update("spk_bertahap", $set, $where);
            } else {
               $where = "id_order_data = " . $resolved['parsed']['id'];
               $do = $this->db(0)->update("order_data", $set, $where);
            }

            if ($do['errno'] == 0) {
               $this->model('Log')->write($this->userData['user'] . " updateSPK Success!");
               echo $do['errno'];
            } else {
               $this->model('Log')->write($this->userData['user'] . " updateSPK" . $do['error']);
               print_r($do['error']);
               exit();
            }
         }
      }
   }
}
