<?php

class I extends Controller
{
   public function __construct()
   {
      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function i($parse, $parse_2 = 0)
   {
      $this->view("Layouts/layout_i", [
         "content" => $this->v_content,
         "title" => "Realtime Invoice"
      ]);
      $this->viewer($parse, $parse_2);
   }

   public function viewer($parse = 0, $parse_2 = 0)
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse_2" => $parse_2]);
   }

   public function content($parse = 0, $parse_2 = 0)
   {
      $data['spk_pending'] = $this->db(0)->get('spk_pending', 'id');
      $data['ea'] = $this->db(0)->get('expedisi_account', 'id');

      $data['parse'] = $parse;
      $data['parse_2'] = $parse_2;
      $data['kas'] = [];
      $data['r_kas'] = [];
      $data['divisi'] = $this->db(0)->get('divisi', 'id_divisi');

      $data['paket'] = $this->db(0)->get('paket_main', "id");
      $data['barang'] = $this->db(0)->get('master_barang', 'id');

      $where = "id_pelanggan = " . $parse . " AND tuntas = 0";
      $where_mutasi = "id_target = " . $parse . " AND tuntas = 0";

      if ($parse == 0) {
         $data['order'] = [];
         $data['mutasi'] = [];
      } else {
         $data['order'] = $this->db(0)->get_where('order_data', $where, 'ref', 1);
         $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where_mutasi, 'ref', 1);
      }

      $ref1 = array_keys($data['order']);
      $ref2 = array_keys($data['mutasi']);
      $refs = array_unique(array_merge($ref1, $ref2));

      $new_refs = [];
      foreach ($refs as $ts) {
         $new_refs[substr($ts, 0, 7) . substr($ts, -4)] = $ts;
      }

      krsort($new_refs);
      $refs = $new_refs;

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $where_kas = "jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ")";
         $data['kas'] = $this->db(0)->get_where('kas', $where_kas, 'ref_transaksi', 1);

         $where_ref = "ref IN (" . $ref_list . ")";
         $data['ref'] = $this->db(0)->get_where('ref', $where_ref, 'ref');

         $cols = "ref_bayar, metode_mutasi, sum(jumlah) as total, sum(bayar) as bayar, sum(kembali) as kembali, status_mutasi, insertTime";
         $where_2 = "jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ") GROUP BY ref_bayar";
         $data['r_kas'] = $this->db(0)->get_cols_where('kas', $cols, $where_2, 1);

         $where = "ref_transaksi IN (" . $ref_list . ")";
         $data['diskon'] = $this->db(0)->get_where('xtra_diskon', $where, 'ref_transaksi', 1);

         $where = "ref_transaksi IN (" . $ref_list . ")";
         $data['charge'] = $this->db(0)->get_where('charge', $where, 'ref_transaksi', 1);

         //PASTIKAN BELUM TUNTAS INDUK REF
         if ($parse_2 == 0) {
            $set = "tuntas = 0";
            $where = "ref IN (" . $ref_list . ")";
            $up = $this->db(0)->update("ref", $set, $where);
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
         }
      }

      $data['refs'] = $refs;
      foreach ($refs as $r) {
         $data['head'][$r]['cs_to'] = 0;
         $data['head'][$r]['id_afiliasi'] = 0;
      }

      foreach ($data['order'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['id_penerima'];
            if ($dd['id_afiliasi'] <> 0) {
               $data['head'][$ref]['id_afiliasi'] = $dd['id_afiliasi'];
            }
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            $data['head'][$ref]['user_id'] = $dd['id_user'];
            if ($dd['id_user_afiliasi'] <> 0) {
               $data['head'][$ref]['cs_to'] = $dd['id_user_afiliasi'];
               break;
            }
         }
      }

      foreach ($data['mutasi'] as $ref => $do) {
         foreach ($do as $dd) {
            $data['head'][$ref]['cs'] = $dd['cs_id'];
            $data['head'][$ref]['insertTime'] = $dd['insertTime'];
            $data['head'][$ref]['tuntas'] = $dd['tuntas'];
            $data['head'][$ref]['user_id'] = $dd['user_id'];
            break;
         }
      }

      $this->view($this->v_content, $data);
   }
}
