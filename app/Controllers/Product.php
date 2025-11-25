<?php

class Product extends Controller
{
   function get()
   {
      $where = "en = 1 AND model != '' ";
      $products = $this->db(0)->get_cols_where('master_barang', ('id, CONCAT(brand, " ", model) as nama_barang'), $where, 1, 'id');
      echo json_encode($products, JSON_UNESCAPED_UNICODE);
   }

   function getStock()
   {
      $where = "en = 1 AND model != '' ";
      $barang = $this->db(0)->get_cols_where('master_barang', ('id, harga_1'), $where, 1, 'id');
      $stok = $this->data('Barang')->stok_data_web();
      foreach ($stok as $key => $s) {
         $stok[$key]['price'] = $barang[$key]['harga_1'];
      }
      echo json_encode($stok, JSON_UNESCAPED_UNICODE);
   }
}
