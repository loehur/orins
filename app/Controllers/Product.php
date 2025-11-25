<?php

class Product extends Controller
{
   function get()
   {
      $where = "en = 1";
      $products = $this->db(0)->get_where('master_barang', $where, 'id');
      echo json_encode($products);
   }
}
