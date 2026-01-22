<?php

class M_Mutasi extends Controller
{
   /**
    * Mark mutasi as keep when SN exists in other mutasi records
    * Used to prevent reject when SN is already used elsewhere
    * 
    * @param string $sn - Serial Number
    * @param int $id_barang - Barang ID
    * @return array ['success' => bool, 'message' => string]
    */
   public function markMutasiKeepBySn($sn, $id_barang)
   {
      // Check for jenis masuk (1) atau keluar (2)
      $cek = $this->db(0)->get_where_row("master_mutasi", "sn = '" . $sn . "' AND id_barang = " . $id_barang . " AND jenis IN (1,2) AND stat <> 2");
      if (isset($cek['stat'])) {
         $up_s = $this->db(0)->update("master_mutasi", "keep = 1", "sn = '" . $sn . "' AND id_barang = " . $id_barang);
         if ($up_s['errno'] <> 0) {
            return [
               'success' => false,
               'message' => "Keep barang gagal. terjadi kesalahan pada sistem"
            ];
         }
      } else {
         $up_s = $this->db(0)->update("master_mutasi", "keep = 0", "sn = '" . $sn . "' AND id_barang = " . $id_barang);
         if ($up_s['errno'] <> 0) {
            return [
               'success' => false,
               'message' => "Batal keep barang gagal. terjadi kesalahan pada sistem"
            ];
         }
      }

      return [
         'success' => true,
         'message' => ''
      ];
   }
}
