<?php

class Functions extends Controller
{
   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $col = $_POST['col'];
      $primary = $_POST['primary'];
      $tb = $_POST['tb'];
      $set = $col . " = '" . $value . "'";
      $where = $primary . " = " . $id;
      $up = $this->db(0)->update($tb, $set, $where);
      echo $up['errno'] == 0 ? 0 : $up['error'];
   }

   public function resetPass()
   {
      $id = $_POST['id'];
      $value = $this->model('Enc')->enc("123");
      $col = 'password';
      $primary = 'id_user';
      $tb = 'user';
      $set = $col . " = '" . $value . "'";
      $where = $primary . " = " . $id;
      $up = $this->db(0)->update($tb, $set, $where);
      echo $up['errno'] == 0 ? 0 : $up['error'];
   }

   public function deleteCell()
   {
      $id = $_POST['id'];
      $primary = $_POST['primary'];
      $tb = $_POST['tb'];

      $where = $primary . " = " . $id;
      $do = $this->db(0)->delete_where($tb, $where);
      if ($do['errno'] <> 0){
         echo $do['error'];
         exit();
      } else {
         // Jika table master_mutasi, cek sn dan id_barang untuk mark keep
         if ($tb == 'master_mutasi') {
            $row = $this->db(0)->get_where_row($tb, $where);
            if (isset($row['sn']) && $row['sn'] <> '' && isset($row['id_barang'])) {
               $m_mutasi = new M_Mutasi($this->db(0));
               $result = $m_mutasi->markMutasiKeepBySn($row['sn'], $row['id_barang']);
               if (!$result['success']) {
                  echo $result['message'];
                  exit();
               }
            }
         }
         echo 0;
      }      
   }
}
