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

   public function deleteCell()
   {
      $id = $_POST['id'];
      $primary = $_POST['primary'];
      $tb = $_POST['tb'];
      $where = $primary . " = " . $id;
      print_r($this->db(0)->delete_where($tb, $where));
   }
}
