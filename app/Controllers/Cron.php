<?php

class Cron extends Controller
{
   public function unser()
   {
      $data = unserialize('a:5:{i:0;a:7:{s:3:"c_h";s:10:"#500-#163-";s:3:"c_b";s:12:"#500&-#163&-";s:3:"n_b";s:17:"100CM X 60CM LPS ";s:3:"n_v";s:17:"100CM X 60CM LPS ";s:1:"g";s:7:"#1-#27-";s:1:"h";i:0;s:1:"d";i:0;}i:1;a:7:{s:3:"c_h";s:9:"#500-#51-";s:3:"c_b";s:11:"#500&-#51&-";s:3:"n_b";s:20:"100CM X 60CM LIQUID ";s:3:"n_v";s:20:"100CM X 60CM LIQUID ";s:1:"g";s:6:"#1-#7-";s:1:"h";i:0;s:1:"d";i:0;}i:2;a:7:{s:3:"c_h";s:9:"#500-#63-";s:3:"c_b";s:13:"#500&-#63&34-";s:3:"n_b";s:20:"100CM X 60CM 101050 ";s:3:"n_v";s:22:"100CM X 60CM 101050-c ";s:1:"g";s:6:"#1-#5-";s:1:"h";i:0;s:1:"d";i:0;}i:3;a:7:{s:3:"c_h";s:10:"#500-#377-";s:3:"c_b";s:15:"#500&-#377&150-";s:3:"n_b";s:20:"100CM X 60CM 101060 ";s:3:"n_v";s:22:"100CM X 60CM 101060-w ";s:1:"g";s:6:"#1-#6-";s:1:"h";i:0;s:1:"d";i:0;}i:4;a:7:{s:3:"c_h";s:9:"#500-#56-";s:3:"c_b";s:11:"#500&-#56&-";s:3:"n_b";s:17:"100CM X 60CM 6MM ";s:3:"n_v";s:17:"100CM X 60CM 6MM ";s:1:"g";s:7:"#1-#10-";s:1:"h";i:0;s:1:"d";i:0;}}');
      echo "<pre>";
      print_r($data);
      echo "</pre>";
   }

   function tes()
   {
      //id_toko = " . $this->userData['id_toko']
      $where_count = "insertTime LIKE '2023-%' GROUP BY ref";
      $count_order =  $this->db(0)->count_where('order_data', $where_count);

      $n = $count_order;
      $n = substr($n, -4);
      $nv = str_pad($n, 4, "0", STR_PAD_LEFT);
      echo $nv;
   }
}
