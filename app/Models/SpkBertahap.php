<?php

class SpkBertahap
{
   public static function parseCekId($id)
   {
      $id = trim((string) $id);
      if ($id !== '' && ($id[0] === 'B' || $id[0] === 'b')) {
         return ['type' => 'bertahap', 'id' => (int) substr($id, 1)];
      }
      return ['type' => 'order', 'id' => (int) $id];
   }

   public static function cekIdBertahap($id)
   {
      return 'B' . (int) $id;
   }

   public static function resetSpkStatus($spkRaw)
   {
      $spk = @unserialize($spkRaw);
      if (!is_array($spk)) {
         return serialize([]);
      }
      foreach ($spk as $k => $v) {
         $spk[$k]['status'] = 0;
         $spk[$k]['user_produksi'] = 0;
         $spk[$k]['update'] = '';
         $spk[$k]['cm_status'] = 0;
         $spk[$k]['user_cm'] = 0;
         $spk[$k]['update_cm'] = '';
      }
      return serialize($spk);
   }

   public static function isSpkComplete($spkArr)
   {
      if (!is_array($spkArr) || count($spkArr) === 0) {
         return false;
      }
      foreach ($spkArr as $dv) {
         if ((int) ($dv['status'] ?? 0) !== 1) {
            return false;
         }
         if ((int) ($dv['cm'] ?? 0) === 1 && (int) ($dv['cm_status'] ?? 0) !== 1) {
            return false;
         }
      }
      return true;
   }

   /**
    * @return array [id_order_data => [rows...]]
    */
   public static function groupByOrderId($rows)
   {
      $map = [];
      if (!is_array($rows)) {
         return $map;
      }
      foreach ($rows as $r) {
         $oid = (int) ($r['id_order_data'] ?? 0);
         if ($oid <= 0) {
            continue;
         }
         if (!isset($map[$oid])) {
            $map[$oid] = [];
         }
         $map[$oid][] = $r;
      }
      foreach ($map as $oid => $list) {
         usort($list, function ($a, $b) {
            return ((int) $a['tahap']) <=> ((int) $b['tahap']);
         });
         $map[$oid] = $list;
      }
      return $map;
   }

   public static function sumQty($stages)
   {
      $sum = 0;
      foreach ($stages as $s) {
         $sum += (int) ($s['qty'] ?? 0);
      }
      return $sum;
   }

   /**
    * Ganti induk yang punya tahapan dengan baris virtual tahap.
    * Induk tanpa tahapan tetap muncul seperti biasa.
    */
   public static function expandOrdersForSpk(array $orders, array $stagesByOrderId)
   {
      $out = [];
      foreach ($orders as $do) {
         if (!is_array($do) || !isset($do['id_order_data'])) {
            continue;
         }
         $oid = (int) $do['id_order_data'];
         if (isset($stagesByOrderId[$oid]) && count($stagesByOrderId[$oid]) > 0) {
            $stages = $stagesByOrderId[$oid];
            $sumQty = self::sumQty($stages);
            $qtyInduk = (int) $do['jumlah'];
            $sisa = max(0, $qtyInduk - $sumQty);
            foreach ($stages as $st) {
               $v = $do;
               $v['jumlah'] = (int) $st['qty'];
               $v['spk_dvs'] = $st['spk_dvs'];
               $v['spk_lanjutan'] = $st['spk_lanjutan'] ?? '';
               $v['cek_id'] = self::cekIdBertahap($st['id']);
               $v['bertahap'] = [
                  'id' => (int) $st['id'],
                  'tahap' => (int) $st['tahap'],
                  'qty_induk' => $qtyInduk,
                  'qty_tahap' => (int) $st['qty'],
                  'qty_sisa' => $sisa,
                  'qty_total_tahap' => $sumQty,
               ];
               $out[] = $v;
            }
         } else {
            $do['cek_id'] = (string) $oid;
            $do['bertahap'] = null;
            $out[] = $do;
         }
      }
      return $out;
   }

   public static function buildVirtualFromStage(array $parent, array $st, $sumQty = null)
   {
      if ($sumQty === null) {
         $sumQty = (int) $st['qty'];
      }
      $qtyInduk = (int) $parent['jumlah'];
      $v = $parent;
      $v['jumlah'] = (int) $st['qty'];
      $v['spk_dvs'] = $st['spk_dvs'];
      $v['spk_lanjutan'] = $st['spk_lanjutan'] ?? '';
      $v['cek_id'] = self::cekIdBertahap($st['id']);
      $v['bertahap'] = [
         'id' => (int) $st['id'],
         'tahap' => (int) $st['tahap'],
         'qty_induk' => $qtyInduk,
         'qty_tahap' => (int) $st['qty'],
         'qty_sisa' => max(0, $qtyInduk - (int) $sumQty),
         'qty_total_tahap' => (int) $sumQty,
      ];
      return $v;
   }
}
