<?php

class Barang_Harga extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->dataBootstrap();
   }

   public function index()
   {
      header('Location: ' . PV::BASE_URL . 'Gudang_Stok');
      exit();
   }

   public function viewer()
   {
      $this->index();
   }

   public function content()
   {
      $this->index();
   }

   public function print()
   {
      header('Location: ' . PV::BASE_URL . 'Gudang_Stok/print');
      exit();
   }

   public function cek_barang($id)
   {
      header('Location: ' . PV::BASE_URL . 'Gudang_Stok');
      exit();
   }
}
