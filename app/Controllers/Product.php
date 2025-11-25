<?php

class Product extends Controller
{
   function get()
   {
      $where = "en = 1";
      $products = $this->db(0)->get_cols_where('master_barang', ('id, CONCAT(brand, model) as nama_barang'), $where, 1);
      echo json_encode($products, JSON_UNESCAPED_UNICODE);
   }
}
