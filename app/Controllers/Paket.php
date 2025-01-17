<?php

class Paket extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data_order();

      if (!in_array($this->userData['user_tipe'], PV::PRIV[3])) {
         $this->model('Log')->write($this->userData['user'] . " Force Logout. Hacker!");
         $this->logout();
      }

      $this->v_content = __CLASS__ . "/content";
      $this->v_viewer = "Layouts/viewer";
   }

   public function index($jenis_pelanggan, $ref = "")
   {
      if ($jenis_pelanggan == 1) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Paket - Umum"
         ]);
      } elseif ($jenis_pelanggan == 2) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Paket - Rekanan"
         ]);
      } elseif ($jenis_pelanggan == 3) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Paket - Online"
         ]);
      }
      $this->viewer($jenis_pelanggan, $ref = "");
   }

   public function viewer($parse = "", $ref = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse, "parse_2" => $ref]);
   }

   function price_key()
   {
      $id = $_POST['id'];
      $tb = $_POST['tb'];
      $ref = $_POST['ref'];
      $primary = $_POST['primary'];
      $up = $this->db(0)->update('paket_order', 'price_locker = 0', "paket_ref = '" . $ref . "'");
      if ($up['errno'] == 0) {
         $up2 = $this->db(0)->update('paket_mutasi', 'price_locker = 0', "paket_ref = '" . $ref . "'");
         if ($up2['errno'] == 0) {
            $up3 = $this->db(0)->update($tb, 'price_locker = 1', $primary . " = '" . $id . "'");
            if ($up3['errno'] == 0) {
               echo 0;
            } else {
               echo $up3['error'];
            }
         } else {
            echo $up2['error'];
         }
      } else {
         echo $up['error'];
      }
   }

   public function content($parse = "", $ref = "")
   {
      $data['main'] = $this->db(0)->get_where('paket_main', 'id_toko = ' . $this->userData['id_toko'], 'id');
      $data['ref'] = $ref;
      $data['produk'] = $this->db(0)->get_where('produk', 'pj = 0 ORDER BY freq DESC, id_produk');
      $data['produk_jasa'] = $this->db(0)->get_where('produk', "pj = " . $this->userData['id_toko'] . " ORDER BY freq DESC, id_produk");

      $data['id_jenis_pelanggan'] = $parse;
      $where = "id_toko = " . $this->userData['id_toko'] . " AND paket_ref = '" . $ref . "'";
      $data['order'] = $this->db(0)->get_where('paket_order', $where);

      $whereBarang = "id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND paket_ref = '" . $ref . "'";
      $data['order_barang'] = $this->db(0)->get_where('paket_mutasi', $whereBarang);

      $data['barang'] = $this->db(0)->get('master_barang', 'id');
      $data['stok'] = $this->data('Barang')->stok_data_list_all($this->userData['id_toko']);

      $data_harga = $this->db(0)->get('produk_harga');
      $data['count'] = count($data['order']);
      $getHarga = [];
      $data['errorID'] = [];

      foreach ($data['order'] as $key => $do) {
         $detail_harga = unserialize($do['detail_harga']);
         if (is_array($detail_harga)) {
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
                  if (isset($getHarga[$key]) && is_array($getHarga[$key])) {
                     $data['order'][$key]['harga'] = array_sum($getHarga[$key]);
                  } else {
                     array_push($data['errorID'], ['id' => $do['id_order_data'], 'produk' => $do['produk']]);
                  }
               }
            }
         }
      }

      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 AND id_pelanggan_jenis = " . $parse . " ORDER BY freq DESC";
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePelanggan, 'id_pelanggan');

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan, 'id_karyawan');
      $data['harga'] = $getHarga;

      $this->view($this->v_content, $data);
   }

   function update_catatan()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];
      $col = $_POST['col'];

      if ($mode == "main") {
         $do = $this->db(0)->update("paket_order", "note = '" . $value . "'", "id_order_data = " . $id);
      } else {
         $data = $this->db(0)->get_where_row("paket_order", "id_order_data = " . $id)['note_spk'];
         $data = unserialize($data);
         $data[$col] = $value;
         $new_data = serialize($data);
         $do = $this->db(0)->update("paket_order", "note_spk = '" . $new_data . "'", "id_order_data = " . $id);
      }

      echo $do['errno'] == 0 ? 1 : $do['error'];
   }

   function add($afiliasi = 0, $pj = 0, $ref = '')
   {
      $this->dataSynchrone();
      $this->data_order();

      $id_produk = $_POST['id_produk'];
      //update freq
      $this->db(0)->update("produk", "freq = freq+1", "id_produk = " . $id_produk);

      $jumlah = $_POST['jumlah'];
      $note = $_POST['note'];

      $where_idProduk = "id_produk = " . $id_produk;
      $detailHarga = [];
      $listDetail = $this->db(0)->get_where('produk_detail', $where_idProduk);

      if (count($listDetail) == 0) {
         echo "Pengaturan Harga belum di setting!";
         exit();
      }

      $spkNote = [];
      foreach ($this->dSPK as $sd) {
         if ($sd['id_produk'] == $id_produk) {
            $spkNote[$sd['id_divisi']] = $_POST['d-' . $sd['id_divisi']];
         }
      }

      $data = [];
      $dp = $this->db(0)->get_where_row('produk', 'id_produk = ' . $id_produk);
      $data = unserialize($dp['produk_detail']);
      $produk_name = $dp['produk'];

      $produk_code = $id_produk . "#";
      $detail_code = "";
      $get_detail_item = [];

      foreach ($data as $d) {
         $groupName = "";
         $detail_item = [];

         $id_detail_item_ex = explode("#", $_POST['f-' . $d]);
         $id_item_ex = explode("-", $id_detail_item_ex[0]);

         //update freq
         $this->db(0)->update("detail_item", "freq = freq+1", "id_detail_item = " . $id_item_ex[0]);

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

         foreach ($this->dDetailGroup as $dg) {
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

      foreach ($this->dSPK as $ds) {
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
         $cols = 'detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, note, note_spk, pj, paket_ref';
         $vals = "'" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . ",'" . $note . "','" . $spkNote_ . "'," . $pj . ",'" . $ref . "'";
      } else {
         $cols = 'detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, note, note_spk, id_afiliasi, pj, paket_ref';
         $vals = "'" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . ",'" . $note . "','" . $spkNote_ . "'," . $afiliasi . "," . $pj . ",'" . $ref . "'";;
      }

      $do = $this->db(0)->insertCols('paket_order', $cols, $vals);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Add Order Success!");
         echo $do['errno'];
      } else {
         print_r($do['error']);
         exit();
      }
   }

   function add_barang($ref = "")
   {
      $barang_c = $_POST['kode'];
      $qty = $_POST['qty'];
      $id_sumber = $this->userData['id_toko'];

      $cols = 'jenis, id_barang, id_sumber, qty, paket_ref';
      $vals = "2,'" . $barang_c . "','" . $id_sumber . "'," . $qty . ",'" . $ref . "'";
      $do = $this->db(0)->insertCols('paket_mutasi', $cols, $vals);
      echo $do['errno'] == 0 ? 0 : $do['error'];
   }

   function load_detail($produk)
   {
      $data = [];
      $dp = $this->db(0)->get_where_row('produk', "id_produk = " . $produk);
      $data = unserialize($dp['produk_detail']);
      $spkNote = [];
      foreach ($this->dSPK as $sd) {
         if ($sd['id_produk'] == $produk) {
            $spkNote[$sd['id_divisi']] = "";
         }
      }

      $data_ = [];
      $varian = [];
      foreach ($data as $d) {
         $groupName = "";
         foreach ($this->dDetailGroup as $dg) {
            if ($dg['id_index'] == $d) {
               $where = "id_detail_group = " . $dg['id_detail_group'] . " ORDER BY freq DESC";
               $data_item = $this->db(0)->get_where('detail_item', $where);

               foreach ($data_item as $di) {
                  $where = "id_detail_item = " . $di['id_detail_item'];
                  $varian_ = $this->db(0)->get_where('detail_item_varian', $where);
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
      $data_['divisi'] = $this->db(0)->get('divisi');
      $this->view(__CLASS__ . "/detail", $data_);
   }

   function load_detail_barang($produk, $id_pelanggan_jenis, $ref = "")
   {
      $data['stok'] = $this->data('Barang')->stok_data_all($produk, $this->userData['id_toko']);
      $data['id_pelanggan_jenis'] = $id_pelanggan_jenis;
      $data['ref'] = $ref;
      $this->view(__CLASS__ . "/detail_barang", $data);
   }

   function add_price($id_pelanggan_jenis)
   {
      $harga_code = $_POST['harga_code'];
      $harga = $_POST['harga'];

      $cols = 'code, harga_' . $id_pelanggan_jenis;
      $vals = "'" . $harga_code . "'," . $harga;

      $whereCount = "code = '" . $harga_code . "'";
      $dataCount = $this->db(0)->count_where('produk_harga', $whereCount);
      if ($dataCount < 1) {
         $do = $this->db(0)->insertCols('produk_harga', $cols, $vals);
         if ($do['errno'] == 0) {
            $this->model('Log')->write($this->userData['user'] . " Add produk_harga Success!");
            echo $do['errno'];
         } else {
            print_r($do['error']);
         }
      } else {
         $where = "code = '" . $harga_code . "'";
         $set = "harga_" . $id_pelanggan_jenis . " = " . $harga;
         $update = $this->db(0)->update("produk_harga", $set, $where);
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
      $detail = unserialize($this->db(0)->get_cols_where('paket_order', $cols, $where, 0)['detail_harga']);

      $detail[$parse[1]]['d'] = $diskon;
      $detail_ = serialize($detail);

      $set = "detail_harga = '" . $detail_ . "'";
      $update = $this->db(0)->update("paket_order", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];

      $this->dataSynchrone();
   }

   function save($id_pelanggan_jenis, $ref_s)
   {

      $paket = $_POST['paket'];
      $harga_paket = $_POST['harga_paket'];

      $count_price_locker = 0;

      $total_harga = 0;

      if ($ref_s == '') {
         $ref = $this->userData['id_toko'] . date("ymdHi");
      } else {
         $ref = $ref_s;
      }

      $where = "id_toko = " . $this->userData['id_toko'] . " AND paket_ref = '" . $ref_s . "'";
      $data['order'] = $this->db(0)->get_where('paket_order', $where);
      $data_harga = $this->db(0)->get('produk_harga');

      $detail_harga = [];
      foreach ($data['order'] as $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $countDH = count($detail_harga);
         if ($do['price_locker'] == 1) {
            $count_price_locker += 1;
            $countDH -= 1;

            foreach ($detail_harga as $kH => $dh_o) {
               foreach ($data_harga as $dh) {
                  if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $id_pelanggan_jenis] <> 0) {
                     $total_harga += ($dh['harga_' . $id_pelanggan_jenis] * $do['jumlah']);
                     break;
                  }
               }
            }
         } else {
            foreach ($detail_harga as $kH => $dh_o) {
               foreach ($data_harga as $dh) {
                  if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $id_pelanggan_jenis] <> 0) {
                     $total_harga += ($dh['harga_' . $id_pelanggan_jenis] * $do['jumlah']);
                     $countDH -= 1;
                     break;
                  }
               }
            }
         }

         if ($countDH <> 0) {
            echo "Lengkapi harga " . $do['produk'] . " terlebih dahulu!";
            exit();
         }
      }

      $where = "id_sumber = " . $this->userData['id_toko'] . " AND paket_ref = '" . $ref . "'";
      $data['mutasi'] = $this->db(0)->get_where('paket_mutasi', $where);
      $data['barang'] = $this->db(0)->get('master_barang', 'id');

      foreach ($data['mutasi'] as $dm) {
         $db = $data['barang'][$dm['id_barang']];
         $harga = $db['harga_' . $id_pelanggan_jenis];
         $total_harga += ($db['harga_' . $id_pelanggan_jenis] * $dm['qty']);
         if ($harga == 0 && $dm['price_locker'] == 0) {
            echo "Lengkapi harga " . trim($db['brand'] . " " . $db['model']) .  " terlebih dahulu!";
            exit();
         }
         if ($dm['price_locker'] == 1) {
            $count_price_locker += 1;
         }
      }



      if ($ref_s == '') {
         $vals = "'" . $ref . "'," . $this->userData['id_toko'] . ",'" . $paket . "'," . $harga_paket;
         $in = $this->db(0)->insertCols('paket_main', 'id, id_toko, nama, harga_' . $id_pelanggan_jenis, $vals);
         if ($in['errno'] <> 0) {
            echo $in['error'];
            exit();
         }
      } else {
         $set = "nama = '" . $paket . "', harga_" . $id_pelanggan_jenis . " = " . $harga_paket;
         $up = $this->db(0)->update('paket_main', $set, "id = '" . $ref . "'");
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
      }

      if ($ref_s == '') {
         $up = $this->db(0)->update('paket_order', "paket_ref = '" . $ref . "'", "paket_ref = ''",);
         if ($up['errno'] == 0) {
            $up2 = $this->db(0)->update('paket_mutasi', "paket_ref = '" . $ref . "'", "paket_ref = ''",);
            if ($up2['errno'] <> 0) {
               echo $up2['error'];
            }
         } else {
            echo $up['error'];
         }
      }

      echo 0;
   }

   function deleteOrder()
   {
      $id_order = $_POST['id_order'];
      $where = "id_order_data =" . $id_order;
      $do = $this->db(0)->delete_where('paket_order', $where);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Delete Order Produksi Success!");
         echo $do['errno'];
      } else {
         print_r($do);
      }
   }

   function deleteOrderBarang()
   {
      $id = $_POST['id'];
      $where = "id =" . $id;
      $do = $this->db(0)->delete_where('paket_mutasi', $where);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Delete Order Barang Success!");
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
      $update = $this->db(0)->update("paket_order", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   function load_aff($target)
   {
      foreach ($this->dToko as $dt) {
         if ($dt['id_toko'] == $target) {
            $data['toko'] = $dt['nama_toko'];
         }
      }

      $data['produk'] = $this->db(0)->get_where('produk', 'pj = 0 ORDER BY freq DESC, id_produk');
      $data['id_toko'] = $target;
      $this->view(__CLASS__ . "/afiliasi", $data);
   }
}
