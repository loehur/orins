<?php

class Buka_Order extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();

      if (!in_array($this->userData['user_tipe'], $this->pCS)) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index($jenis_pelanggan)
   {
      if ($jenis_pelanggan == 1) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Umum"
         ]);
      } elseif ($jenis_pelanggan == 2) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Rekanan"
         ]);
      }
      $this->viewer($jenis_pelanggan);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["page" => $this->page, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['id_jenis_pelanggan'] = $parse;
      $where = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0";
      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);
      $data_harga = $this->model('M_DB_1')->get('produk_harga');
      $data['count'] = count($data['order']);

      $getHarga = [];

      foreach ($data['order'] as $key => $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $countDH[$key] = count($detail_harga);
         foreach ($detail_harga as $dh_o) {
            $getHarga[$key][$dh_o['c_h']] = 0;
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $parse] <> 0) {
                  $getHarga[$key][$dh_o['c_h']] = $dh['harga_' . $parse];
                  $countDH[$key] -= 1;
                  break;
               }
            }
         }

         if ($countDH[$key] == 0) {
            if (isset($data['order'][$key]['harga'])) {
               $data['order'][$key]['harga'] = array_sum($getHarga[$key]);
            } else {
               echo "Error! Transaction ID: " . $do['id_order_data'];
               exit();
            }
         }
      }

      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 AND id_pelanggan_jenis = " . $parse;
      $data['pelanggan'] = $this->model('M_DB_1')->get_where('pelanggan', $wherePelanggan);
      $data['karyawan'] = $this->dKaryawan;
      $data['harga'] = $getHarga;

      $this->view($this->v_content, $data);
   }

   function add($afiliasi = 0)
   {
      $this->dataSynchrone();
      $this->data();

      $id_produk = $_POST['id_produk'];
      $jumlah = $_POST['jumlah'];
      $note = $_POST['note'];

      $where_idProduk = "id_produk = " . $id_produk;
      $detailHarga = [];
      $listDetail = $this->model('M_DB_1')->get_where('produk_detail', $where_idProduk);

      if (count($listDetail) == 0) {
         echo "Pengaturan Harga belum di setting!";
         exit();
      }

      $spkNote = [];
      foreach ($this->dSPK_all as $sd) {
         if ($sd['id_produk'] == $id_produk) {
            $spkNote[$sd['id_divisi']] = $_POST['d-' . $sd['id_divisi']];
         }
      }

      $data = [];
      foreach ($this->dProdukAll as $dp) {
         if ($dp['id_produk'] == $id_produk) {
            $data = unserialize($dp['produk_detail']);
            $produk_name = $dp['produk'];
         }
      }

      $produk_code = $id_produk . "#";
      $detail_code = "";
      $get_detail_item = [];

      foreach ($data as $d) {

         $groupName = "";
         $detail_item = [];

         $id_detail_item_ex = explode("#", $_POST['f-' . $d]);
         $id_item_ex = explode("-", $id_detail_item_ex[0]);
         $get_detail_item[$d]['id'] = $id_item_ex[0];
         $get_detail_item[$d]['name'] = $id_item_ex[1];

         if ($id_detail_item_ex[1] <> "") {
            $varian_ex = explode("-", $id_detail_item_ex[1]);
            $get_detail_item[$d]['id_varian'] = $varian_ex[0];
            $get_detail_item[$d]['varian'] = $varian_ex[1];
            $detail_item = $get_detail_item[$d]['name'] . "-" . $get_detail_item[$d]['varian'];
         } else {
            $get_detail_item[$d]['id_varian'] = "";
            $get_detail_item[$d]['varian'] = "";
            $detail_item = $get_detail_item[$d]['name'];
         }

         foreach ($this->dDetailGroupAll as $dg) {
            if ($dg['id_index'] == $d) {
               $groupName = $dg['detail_group'];
            }
         }

         if ($groupName == "" || $detail_item == "") {
            echo "Error! diperlukan Synchrone Data!";
            exit();
         }

         $produk_detail_[$d] = array(
            "group_name" => $groupName,
            "detail_id" =>   $get_detail_item[$d]['id'] . "_" .  $get_detail_item[$d]['id_varian'],
            "detail_name" => $detail_item,
         );

         $detail_code .= "-" .  $get_detail_item[$d]['id'] . "&" .  $get_detail_item[$d]['id_varian'];
      }

      foreach ($listDetail as $key_l => $ldt) {
         $c_harga = "";
         $c_barang = "";
         $g = "";
         $n_b = "";
         $n_v = "";

         $detail__ = unserialize($ldt['detail']);
         foreach ($detail__ as $d_L) {
            foreach ($data as $d) {
               if ($d_L == $d) {
                  $c_harga .= "#" . $get_detail_item[$d]['id'] . "-";
                  $c_barang .= "#" . $get_detail_item[$d]['id'] . "&" . $get_detail_item[$d]['id_varian'] . "-";
                  $n_b .= $get_detail_item[$d]['name'] . " ";

                  if (strlen($get_detail_item[$d]['varian']) > 0) {
                     $n_v .= $get_detail_item[$d]['name'] . "-" . $get_detail_item[$d]['varian'] . " ";
                  } else {
                     $n_v .= $get_detail_item[$d]['name'] . " ";
                  }
                  $g .= "#" . $d . "-";
               }
            }


            $n_b = preg_replace('!\s+!', ' ', $n_b);
            $n_v = preg_replace('!\s+!', ' ', $n_v);

            $detailHarga[$key_l] = array(
               "c_h" => $c_harga, //code harga
               "c_b" => $c_barang, // code_barang
               "n_b" => $n_b, // nama barang
               "n_v" => $n_v, // nama barang varian
               "g" => $g, // group komponen
               "h" => 0, // harga
               "d" => 0, // diskon
            );
         }
      }


      $produk_code .= $detail_code;
      $produk_detail = serialize($produk_detail_);

      $spkDVS = [];

      foreach ($this->dSPK_all as $ds) {
         if ($id_produk == $ds['id_produk']) {
            $detailNeed = [];
            $dgr = unserialize($ds['detail_groups']);
            $cm = $ds['cm'];

            foreach ($dgr as $key => $dgr_) {
               foreach ($produk_detail_ as $key => $pd) {
                  if ($dgr_ == $key) {
                     $detailNeed[$pd['detail_id']] = $pd['detail_name'];
                  }
               }
            }

            $spkDVS[$ds['id_divisi']] = array(
               "divisi_code" => "D-" . $ds['id_divisi'],
               "spk" => $detailNeed,
               "status" => 0,
               "user_produksi" => 0,
               "update" => "",
               "cm" => $cm, //complete maker
               "cm_status" => 0,
               "user_cm" => 0,
               "update_cm" => "",
            );
         }
      }

      $spkDVS_ = serialize($spkDVS);
      $spkNote_ = serialize($spkNote);
      $detailHarga_ = serialize($detailHarga);

      if ($afiliasi == 0) {
         $cols = 'detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, id_user, note, note_spk';
         $vals = "'" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . "," . $this->userData['id_user'] . ",'" . $note . "','" . $spkNote_ . "'";
      } else {
         $cols = 'detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, id_user, note, note_spk, id_afiliasi, status_order';
         $vals = "'" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . "," . $this->userData['id_user'] . ",'" . $note . "','" . $spkNote_ . "'," . $afiliasi . ",1";
      }

      $do = $this->model('M_DB_1')->insertCols('order_data', $cols, $vals);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Add Order Success!");
         echo $do['errno'];
      } else {
         print_r($do['error']);
      }
   }

   function load_detail($produk)
   {
      $data = [];
      foreach ($this->dProdukAll as $dp) {
         if ($dp['id_produk'] == $produk) {
            $data = unserialize($dp['produk_detail']);
         }
      }

      $spkNote = [];
      foreach ($this->dSPK_all as $sd) {
         if ($sd['id_produk'] == $produk) {
            $spkNote[$sd['id_divisi']] = "";
         }
      }

      $data_ = [];
      $varian = [];
      foreach ($data as $d) {
         $groupName = "";
         foreach ($this->dDetailGroupAll as $dg) {
            if ($dg['id_index'] == $d) {
               $where = "id_detail_group = " . $dg['id_detail_group'] . " ORDER BY detail_item ASC";
               $data_item = $this->model('M_DB_1')->get_where('detail_item', $where);

               foreach ($data_item as $di) {
                  $where = "id_detail_item = " . $di['id_detail_item'];
                  $varian_ = $this->model('M_DB_1')->get_where('detail_item_varian', $where);
                  if (count($varian_) > 0) {
                     $varian[$di['id_detail_item']] = $varian_;
                  }
               }

               $groupName = $dg['detail_group'];
            }
         }
         $data_[$d]['name'] = $groupName;
         $data_[$d]['item'] = $data_item;
      }

      $data_['detail'] = $data_;
      $data_['varian'] = $varian;
      $data_['spkNote'] = $spkNote;
      $this->view($this->page . "/detail", $data_);
   }

   function add_price($id_pelanggan_jenis)
   {
      $harga_code = $_POST['harga_code'];
      $harga = $_POST['harga'];

      $cols = 'id_toko, code, harga_' . $id_pelanggan_jenis;
      $vals = "'" . $this->userData['id_toko'] . "','" . $harga_code . "'," . $harga;

      $whereCount = "code = '" . $harga_code . "'";
      $dataCount = $this->model('M_DB_1')->count_where('produk_harga', $whereCount);
      if ($dataCount < 1) {
         $do = $this->model('M_DB_1')->insertCols('produk_harga', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add produk_harga Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $where = "code = '" . $harga_code . "'";
         $set = "harga_" . $id_pelanggan_jenis . " = " . $harga;
         $update = $this->model('M_DB_1')->update("produk_harga", $set, $where);
         echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
      }

      $this->dataSynchrone();
   }

   function diskon()
   {
      $parse = explode("_", $_POST['parse']);
      $diskon = $_POST['diskon'];
      $harga = $parse[2];

      if ($diskon > $harga) {
         echo "Diskon tidak boleh melebihi harga!";
         exit();
      }

      $cols = "detail_harga";
      $where = "id_order_data = " . $parse[0];
      $detail = unserialize($this->model('M_DB_1')->get_cols_where('order_data', $cols, $where, 0)['detail_harga']);

      $detail[$parse[1]]['d'] = $diskon;
      $detail_ = serialize($detail);

      $set = "detail_harga = '" . $detail_ . "'";
      $update = $this->model('M_DB_1')->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];

      $this->dataSynchrone();
   }

   function proses($id_pelanggan_jenis)
   {

      $id_pelanggan = $_POST['id_pelanggan'];
      $id_karyawan = $_POST['id_karyawan'];
      $ref = date("Ymdhis") . rand(0, 9);

      $where = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0";
      $data['order'] = $this->model('M_DB_1')->get_where('order_data', $where);
      $data_harga = $this->model('M_DB_1')->get('produk_harga');

      $detail_harga = [];
      foreach ($data['order'] as $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $countDH = count($detail_harga);
         foreach ($detail_harga as $kH => $dh_o) {
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $id_pelanggan_jenis] <> 0) {
                  $countDH -= 1;
                  break;
               }
            }
         }

         if ($countDH <> 0) {
            echo "Tetapkan Harga terlebih dahulu!";
            exit();
         }
      }

      $error = 0;

      foreach ($data['order'] as $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $harga = 0;
         $diskon = 0;
         $jumlah = $do['jumlah'];

         foreach ($detail_harga as $key => $dh_o) {
            $diskon += ($dh_o['d'] * $jumlah);
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $id_pelanggan_jenis] <> 0) {
                  $harga +=  $dh['harga_' . $id_pelanggan_jenis];
                  $detail_harga[$key]['h'] = $dh['harga_' . $id_pelanggan_jenis];
                  break;
               }
            }
         }

         $where = "id_order_data = " . $do['id_order_data'];
         $set = "diskon = " . $diskon . ", detail_harga = '" . serialize($detail_harga) . "', harga = " . $harga . ", id_penerima = " . $id_karyawan . ", id_pelanggan = " . $id_pelanggan . ", id_pelanggan_jenis = " . $id_pelanggan_jenis . ", ref = '" . $ref . "'";
         $update = $this->model('M_DB_1')->update("order_data", $set, $where);
         $error = $update['errno'];
      }

      if ($error == 0) {
         echo 1;
      }
   }

   function deleteOrder()
   {
      $id_order = $_POST['id_order'];
      $where = "id_order_data =" . $id_order;
      $do = $this->model('M_DB_1')->delete_where('order_data', $where);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Delete Order Success!");
         echo $do['errno'];
      } else {
         print_r($do);
      }
   }

   function updateCell_N()
   {
      $value = $_POST['value'];
      $id = $_POST['id'];

      $where = "id_order_data = '" . $id . "'";
      $set = "jumlah = " . $value;
      $update = $this->model('M_DB_1')->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   function load_aff($target)
   {
      foreach ($this->dToko as $dt) {
         if ($dt['id_toko'] == $target) {
            $data['toko'] = $dt['nama_toko'];
         }
      }

      $data['id_toko'] = $target;
      $this->view($this->page . "/afiliasi", $data);
   }
}
