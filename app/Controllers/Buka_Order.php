<?php

class Buka_Order extends Controller
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

   function Edit_order($ref, $jenis_pelanggan, $dibayar, $id_pelanggan)
   {
      $_SESSION['edit'][$this->userData['id_user']] = [$ref, $jenis_pelanggan, $dibayar, $id_pelanggan];
      $dEdit = $_SESSION['edit'][$this->userData['id_user']];

      //balikan stok nya dl
      $where = "ref = '" . $dEdit[0] . "' AND id_target = " . $id_pelanggan;
      $set = "stat = 0";
      $up = $this->db(0)->update("master_mutasi", $set, $where);
      if ($up['errno'] <> 0) {
         echo $up['error'];
         exit();
      }

      $where = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0 AND ref = ''";
      $do = $this->db(0)->delete_where('order_data', $where);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      $whereBarang = "id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND jenis = 2 AND id_target = 0 AND ref = ''";
      $do = $this->db(0)->delete_where('master_mutasi', $whereBarang);
      if ($do['errno'] <> 0) {
         echo $do['error'];
         exit();
      }

      $this->index($jenis_pelanggan);
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
      } elseif ($jenis_pelanggan == 3) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Online"
         ]);
      } elseif ($jenis_pelanggan == 100) {
         $this->view("Layouts/layout_main", [
            "content" => $this->v_content,
            "title" => "Buka Order - Stok"
         ]);
      }
      $this->viewer($jenis_pelanggan);
   }

   public function viewer($parse = "")
   {
      $this->view($this->v_viewer, ["controller" => __CLASS__, "parse" => $parse]);
   }

   public function content($parse = "")
   {
      $data['produk'] = $this->db(0)->get_where('produk', 'pj = 0 ORDER BY freq DESC, id_produk');
      $data['produk_jasa'] = $this->db(0)->get_where('produk', "pj = " . $this->userData['id_toko'] . " ORDER BY freq DESC, id_produk");
      $data['paket'] = $this->db(0)->get_where('paket_main', "id_toko = " . $this->userData['id_toko'], "id");

      $wherePelanggan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 AND id_pelanggan_jenis = " . $parse . " ORDER BY freq DESC";
      $data['pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePelanggan, 'id_pelanggan');

      $data['id_jenis_pelanggan'] = $parse;
      if (isset($_SESSION['edit'][$this->userData['id_user']])) {
         $dEdit = $_SESSION['edit'][$this->userData['id_user']];
         $where = "ref = '" . $dEdit[0] . "' OR (id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0)";;
         $whereBarang = "ref = '" . $dEdit[0] . "' OR (id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND jenis = 2 AND id_target = 0)";
      } else {
         $where = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0";
         $whereBarang = "id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND jenis = 2 AND id_target = 0";
      }

      $data['order'] = $this->db(0)->get_where('order_data', $where);
      $data['order_barang'] = $this->db(0)->get_where('master_mutasi', $whereBarang);

      $data['barang'] = $this->db(0)->get('master_barang', 'code');
      $data['stok'] = $this->data('Barang')->stok_data_list($this->userData['id_toko']);

      $data_harga = $this->db(0)->get('produk_harga');
      $data['count'] = count($data['order']) + count($data['order_barang']);
      $getHarga = [];
      $data['errorID'] = [];

      $count_price_locker = 0;
      $id_margin = [];
      $total_per_paket = [];
      $harga_paket = [];

      foreach ($data['order'] as $key => $do) {
         if (strlen($do['paket_ref']) > 0) {
            if ($do['price_locker'] == 1) {

               if (!isset($total_per_paket[$do['paket_ref']])) {
                  $total_per_paket[$do['paket_ref']] = 0;
               }

               $count_price_locker += 1;
               $harga_paket[$do['paket_ref']] = $data['paket'][$do['paket_ref']]['harga_' . $parse];
               $id_margin[$do['paket_ref']]['id'] = $do['id_order_data'];
               $get = $this->db(0)->get_where_row('paket_order', "paket_ref = '" . $do['paket_ref'] . "' AND price_locker = 1");
               if (isset($get['jumlah'])) {
                  $paket_qty = $do['jumlah'] / $get['jumlah'];
                  $id_margin[$do['paket_ref']]['qty'] = $paket_qty;
                  $id_margin[$do['paket_ref']]['nama'] =  $data['paket'][$do['paket_ref']]['nama'];
                  $id_margin[$do['paket_ref']]['tb'] = "order_data";
                  $id_margin[$do['paket_ref']]['primary'] =  "id_order_data";
               }
            }
         }

         $parse_harga = $parse;
         if ($parse == 100) {
            $parse_harga = 2;
         }

         $detail_harga = unserialize($do['detail_harga']);
         if (is_array($detail_harga)) {
            $countDH[$key] = count($detail_harga);
            foreach ($detail_harga as $dh_o) {
               $getHarga[$key][$dh_o['c_h']] = 0;
               foreach ($data_harga as $dh) {
                  if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $parse_harga] <> 0) {
                     $getHarga[$key][$dh_o['c_h']] = $dh['harga_' . $parse_harga];
                     if (strlen($do['paket_ref']) > 0) {
                        if (isset($total_per_paket[$do['paket_ref']])) {
                           $total_per_paket[$do['paket_ref']] += ($getHarga[$key][$dh_o['c_h']] * $do['jumlah']);
                        } else {
                           $total_per_paket[$do['paket_ref']] = ($getHarga[$key][$dh_o['c_h']] * $do['jumlah']);
                        }
                     }
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

      foreach ($data['order_barang'] as $dm) {
         if ($dm['price_locker'] == 1) {

            if (!isset($total_per_paket[$dm['paket_ref']])) {
               $total_per_paket[$dm['paket_ref']] = 0;
            }

            $count_price_locker += 1;
            $harga_paket[$dm['paket_ref']] = $data['paket'][$dm['paket_ref']]['harga_' . $parse];
            $id_margin[$dm['paket_ref']]['id'] = $dm['id'];
            $get = $this->db(0)->get_where_row('paket_order', "paket_ref = '" . $dm['paket_ref'] . "' AND price_locker = 1");
            if (isset($get['jumlah'])) {
               $paket_qty = $dm['jumlah'] / $get['jumlah'];
               $id_margin[$dm['paket_ref']]['qty'] = $paket_qty;
               $id_margin[$do['paket_ref']]['nama'] =  $data['paket'][$dm['paket_ref']]['nama'];
               $id_margin[$do['paket_ref']]['tb'] = "master_mutasi";
               $id_margin[$do['paket_ref']]['primary'] =  "id";
            }
         }

         if (strlen($dm['paket_ref']) > 0) {
            $db = $data['barang'][$dm['kode_barang']];
            if (isset($total_per_paket[$dm['paket_ref']])) {
               $total_per_paket[$dm['paket_ref']] += ($db['harga_' . $parse] * $dm['qty']);
            } else {
               $total_per_paket[$dm['paket_ref']] = ($db['harga_' . $parse] * $dm['qty']);
            }
         }
      }

      $adjuster = [];
      foreach ($total_per_paket as $key => $tpp) {
         $adjuster[$key] = ($data['paket'][$key]['harga_' . $parse] * $id_margin[$key]['qty']) - $tpp;
         $id_margin[$key]['margin_paket'] = $adjuster[$key];
      }

      $whereKaryawan =  "id_toko = " . $this->userData['id_toko'] . " AND en = 1 ORDER BY freq_cs DESC";
      $data['karyawan'] = $this->db(0)->get_where('karyawan', $whereKaryawan, 'id_karyawan');
      $data['harga'] = $getHarga;

      $data['margin_paket'] = $id_margin;
      $this->view($this->v_content, $data);
   }

   function delete_error()
   {
      $id = $_POST['id'];
      $where = "id_order_data = " . $id;
      $this->db(0)->delete_where('order_data', $where);
   }

   function update_catatan()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];
      $col = $_POST['col'];

      if ($mode == "main") {
         $do = $this->db(0)->update("order_data", "note = '" . $value . "'", "id_order_data = " . $id);
      } else {
         $data = $this->db(0)->get_where_row("order_data", "id_order_data = " . $id)['note_spk'];
         $data = unserialize($data);
         $data[$col] = $value;
         $new_data = serialize($data);
         $do = $this->db(0)->update("order_data", "note_spk = '" . $new_data . "'", "id_order_data = " . $id);
      }

      echo $do['errno'] == 0 ? 1 : $do['error'];
   }

   function add_paket($id_pelanggan_jenis)
   {
      $id = $_POST['id'];
      $paket_group = $this->userData['id_toko'] . date("ymdHis") . rand(0, 9);
      $data['order'] = $this->db(0)->get_where("paket_order", "paket_ref = '" . $id . "'");
      $data['mutasi'] = $this->db(0)->get_where("paket_mutasi", "paket_ref = '" . $id . "'");
      $data['barang'] = $this->db(0)->get('master_barang', 'code');

      foreach ($data['mutasi'] as $dm) {
         $_POST['kode'] = $dm['kode_barang'];
         $_POST['qty'] = $_POST['qty_paket'] * $dm['qty'];
         $_POST['sds'] = 0;
         $_POST['sn'] = '';
         $id_sumber = $dm['id_sumber'];
         $this->add_barang($id_pelanggan_jenis, $dm['price_locker'], $id, $id_sumber, 0, $paket_group);
      }

      foreach ($data['order'] as $do) {

         $_POST['id_produk'] = $do['id_produk'];
         $_POST['note'] = $do['note'];
         $_POST['note_spk'] = $do['note_spk'];
         $_POST['detail_harga'] = $do['detail_harga'];
         $_POST['produk_code'] = $do['produk_code'];
         $_POST['produk_detail'] = $do['produk_detail'];
         $_POST['jumlah'] = $_POST['qty_paket'] * $do['jumlah'];
         $this->add($do['id_afiliasi'], $id, $paket_group, $do['price_locker'], $do['pj']);
      }
   }

   function add($afiliasi = 0, $paket_ref = '', $paket_group = '', $price_locker = 0, $margin_paket = 0, $pj = 0)
   {
      $this->dataSynchrone();
      $this->data_order();

      if ($afiliasi == 0 && isset($_POST['aff_target'])) {
         $afiliasi = $_POST['aff_target'];
      }

      $id_produk = $_POST['id_produk'];
      $jumlah = $_POST['jumlah'];
      $note = $_POST['note'];

      //update freq
      $this->db(0)->update("produk", "freq = freq+1", "id_produk = " . $id_produk);

      $where_idProduk = "id_produk = " . $id_produk;
      $detailHarga = [];
      $listDetail = $this->db(0)->get_where('produk_detail', $where_idProduk);

      if (count($listDetail) == 0) {
         echo "Pengaturan Harga belum di setting!";
         exit();
      }

      if (isset($_POST['note_spk'])) {
         $spkNote = unserialize($_POST['note_spk']);
      } else {
         $spkNote = [];
         foreach ($this->dSPK as $sd) {
            if ($sd['id_produk'] == $id_produk) {
               $spkNote[$sd['id_divisi']] = $_POST['d-' . $sd['id_divisi']];
            }
         }
      }

      $data = [];
      $dp = $this->db(0)->get_where_row('produk', 'id_produk = ' . $id_produk);
      $data = unserialize($dp['produk_detail']);
      $produk_name = $dp['produk'];

      $produk_code = $id_produk . "#";
      $detail_code = "";
      $get_detail_item = [];

      if (isset($_POST['produk_detail'])) {
         $produk_detail_ = unserialize($_POST['produk_detail']);
      } else {
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
      }

      if (isset($_POST['detail_harga'])) {
         $detailHarga = unserialize($_POST['detail_harga']);
      } else {
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
      }

      if (isset($_POST['produk_code'])) {
         $produk_code = $_POST['produk_code'];
      } else {
         $produk_code .= $detail_code;
      }

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

      if (isset($_POST['id_paket']) && $_POST['id_paket'] <> "") {
         $paketGet = explode("-", $_POST['id_paket']);

         $where = $paketGet[1] . " = " . $paketGet[0];
         $link_paket = $this->db(0)->get_where_row($paketGet[2], $where);
         $paket_ref = $link_paket['paket_ref'];
         $paket_group = $link_paket['paket_group'];
      }

      if ($afiliasi == 0) {
         $cols = 'detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, id_user, note, note_spk, paket_ref, paket_group, price_locker, margin_paket, pj';
         $vals = "'" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . "," . $this->userData['id_user'] . ",'" . $note . "','" . $spkNote_ . "','" . $paket_ref . "','" . $paket_group . "'," . $price_locker . "," . $margin_paket . "," . $pj;
      } else {
         $cols = 'detail_harga, produk, id_toko, id_produk, produk_code, produk_detail, spk_dvs, jumlah, id_user, note, note_spk, id_afiliasi, status_order, paket_ref, paket_group, price_locker, margin_paket, pj';
         $vals = "'" . $detailHarga_ . "','" . $produk_name . "'," . $this->userData['id_toko'] . "," . $id_produk . ",'" . $produk_code . "','" . $produk_detail . "','" . $spkDVS_ . "'," . $jumlah . "," . $this->userData['id_user'] . ",'" . $note . "','" . $spkNote_ . "'," . $afiliasi . ",1,'" . $paket_ref . "','" . $paket_group . "'," . $price_locker . "," . $margin_paket . "," . $pj;
      }

      $do = $this->db(0)->insertCols('order_data', $cols, $vals);
      if ($do['errno'] == 0) {
         $this->model('Log')->write($this->userData['user'] . " Add Order Success!");
         echo $do['errno'];
      } else {
         print_r($do['error']);
         exit();
      }
   }

   function add_barang($id_jenis_pelanggan, $price_locker = 0, $paket_ref = "", $id_sumber = 0, $margin_paket = 0, $paket_group = "")
   {
      $barang_c = $_POST['kode'];
      $qty = $_POST['qty'];
      $sds = $_POST['sds'];
      $sn =  $_POST['sn'];
      $sn_c = 0;
      if (strlen($sn) > 0) {
         $sn_c = 1;
      }

      if ($id_sumber == 0) {
         $id_sumber = $this->userData['id_toko'];
      }

      $cek = $this->data('Barang')->cek($barang_c, $id_sumber, $sn, $sds, $qty);
      if ($cek == false) {
         echo "Stok (" . $barang_c . ") kosong";
         exit();
      }

      if (isset($_POST['id_paket']) && $_POST['id_paket'] <> "") {
         $paketGet = explode("-", $_POST['id_paket']);
         $where = $paketGet[1] . " = " . $paketGet[0];
         $link_paket = $this->db(0)->get_where_row($paketGet[2], $where);
         $paket_ref = $link_paket['paket_ref'];
         $paket_group = $link_paket['paket_group'];
      }

      $barang = $this->db(0)->get_where_row('master_barang', "code = '" . $barang_c . "'");
      $id_barang = $barang['id'];
      $harga = $barang['harga_' . $id_jenis_pelanggan];

      $cols = 'jenis, jenis_target, id_barang, kode_barang, id_sumber, qty, sds, sn, sn_c, user_id, harga_jual, price_locker, paket_ref, paket_group, margin_paket';
      $vals = "2," . $id_jenis_pelanggan . "," . $id_barang . ",'" . $barang_c . "'," . $id_sumber . "," . $qty . "," . $sds . ",'" . $sn . "'," . $sn_c . "," . $this->userData['id_user'] . "," . $harga . "," . $price_locker . ",'" . $paket_ref . "','" . $paket_group . "'," . $margin_paket;
      $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);
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

   function load_detail_barang($produk, $id_pelanggan_jenis)
   {
      $data['stok'] = $this->data('Barang')->stok_data($produk, $this->userData['id_toko']);
      $data['id_pelanggan_jenis'] = $id_pelanggan_jenis;
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
      $detail = unserialize($this->db(0)->get_cols_where('order_data', $cols, $where, 0)['detail_harga']);

      $detail[$parse[1]]['d'] = $diskon;
      $detail_ = serialize($detail);

      $set = "detail_harga = '" . $detail_ . "'";
      $update = $this->db(0)->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];

      $this->dataSynchrone();
   }

   function proses($id_pelanggan_jenis, $id_pelanggan = 0)
   {

      if ($id_pelanggan_jenis == 100) {
         $stok_order = 1;
      } else {
         $stok_order = 0;
      }

      if (isset($_SESSION['edit'][$this->userData['id_user']])) {
         $dEdit = $_SESSION['edit'][$this->userData['id_user']];
         $ref = $dEdit[0];
         $where_order = "ref = '" . $ref . "' OR (id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0)";
         $where_barang = "ref = '" . $ref . "' OR (id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND id_target = 0 AND jenis = 2)";
      } else {
         if ($_POST['id_pelanggan'] <> "") {
            $id_pelanggan = $_POST['id_pelanggan'];
         } else {
            $hp = $_POST['hp'];

            if (strlen($hp) > 0) {
               $hp = $this->data('Validasi')->valid_wa($hp);
               if ($hp == false) {
                  echo "Nomor HP tidak valid";
                  exit();
               }
            }

            $nama = strtoupper($_POST['new_customer']);
            if (strlen($nama) == 0) {
               echo "Lengkapi Nama Customer";
               exit();
            }

            $cek_pelanggan = $this->db(0)->get_where_row('pelanggan', "UPPER(nama) = '" . $nama . "' AND no_hp = '" . $hp . "'");

            if (isset($cek_pelanggan['id_pelanggan'])) {
               $id_pelanggan = $cek_pelanggan['id_pelanggan'];
            } else {
               $get_lastID = $this->db(0)->get_cols('pelanggan', 'MAX(id_pelanggan) as max', 0);
               $id_pelanggan = $get_lastID['max'] + 1;

               $cols = 'id_pelanggan, id_toko, nama, no_hp, id_pelanggan_jenis';
               $vals = $id_pelanggan . ",'" . $this->userData['id_toko'] . "','" . $nama . "','" . $hp . "'," . $id_pelanggan_jenis;

               $do = $this->db(0)->insertCols('pelanggan', $cols, $vals);
               if ($do['errno'] <> 0) {
                  echo $do['error'];
                  exit();
               }
            }
         }

         $id_karyawan = $_POST['id_karyawan'];

         $where_order = "id_toko = " . $this->userData['id_toko'] . " AND id_user = " . $this->userData['id_user'] . " AND id_pelanggan = 0";
         $where_barang = "id_sumber = " . $this->userData['id_toko'] . " AND user_id = " . $this->userData['id_user'] . " AND id_target = 0 AND jenis = 2";

         $n_ref = [];
         $where_n = "id_toko = " . $this->userData['id_toko'] . " AND insertTime LIKE '" . date("Y") . "-" . date('m') . "-%' AND ref <> '' GROUP BY ref";
         $n_ref =  $this->db(0)->get_cols_where('order_data', 'ref', $where_n, 1, 'ref');

         $n2_ref = [];
         $where_n2 = "id_sumber = " . $this->userData['id_toko'] . " AND jenis = 2 AND insertTime LIKE '" . date("Y") . "-" . date('m') . "-%' AND ref <> '' GROUP BY ref";
         $n2_ref =  $this->db(0)->get_cols_where('master_mutasi', 'ref', $where_n2, 1, 'ref');
         foreach ($n2_ref as $key => $n2) {
            if (isset($n_ref[$key])) {
               unset($n2_ref[$key]);
            }
         }

         $qty_ref = count($n_ref) + count($n2_ref);
         $qty_ref += 1;
         $qty_ref = substr($qty_ref, -5);
         $nv = str_pad($qty_ref, 5, "0", STR_PAD_LEFT);
         $ref = $this->userData['id_toko'] . date("ymd") . rand(0, 9) . $nv;
      }

      if ($id_pelanggan_jenis == 100) {
         $tujuan = $this->userData['id_toko'];
         $tanggal = date('Y-m-d');

         $cols = 'id, tipe, id_sumber, id_target, tanggal, user_id';
         $vals = "'" . $ref . "',2,0,'" . $tujuan . "','" . $tanggal . "'," . $this->userData['id_user'];
         $do = $this->db(0)->insertCols('master_input', $cols, $vals);
         if ($do['errno'] <> 0 && $do['errno'] <> 1062) {
            echo $do['error'];
            exit();
         }
      }

      $data['barang'] = $this->db(0)->get('master_barang', 'code');
      $data['order'] = $this->db(0)->get_where('order_data', $where_order);
      $data['mutasi'] = $this->db(0)->get_where('master_mutasi', $where_barang);

      $data['paket'] = $this->db(0)->get_where('paket_main', "id_toko = " . $this->userData['id_toko'], "id");
      $id_margin = [];
      $total_per_paket = [];
      $harga_paket = [];

      $paket_qty = [];

      foreach ($data['mutasi'] as $dbr) {
         $id_sumber = $dbr['id_sumber'];
         $barang_c = $dbr['kode_barang'];

         if ($dbr['ref'] <> '') {
            $id_karyawan = $dbr['cs_id'];
         }

         $qty = $dbr['qty'];
         $sds = $dbr['sds'];
         $sn =  $dbr['sn'];

         if ($dbr['price_locker'] == 1) {
            $harga_paket[$dbr['paket_ref']] = $data['paket'][$dbr['paket_ref']]['harga_' . $id_pelanggan_jenis];
            $id_margin[$dbr['paket_ref']]['id'] = $dbr['id'];
            $id_margin[$dbr['paket_ref']]['primary'] = 'id';
            $id_margin[$dbr['paket_ref']]['tb'] = 'master_mutasi';

            $get = $this->db(0)->get_where_row('paket_order', "paket_ref = '" . $dbr['paket_ref'] . "' AND price_locker = 1");
            if (isset($get['jumlah'])) {
               $paket_qty = $dbr['jumlah'] / $get['jumlah'];
               $id_margin[$dbr['paket_ref']]['qty'] = $paket_qty;
            }
         }

         if (strlen($dbr['paket_ref']) > 0) {
            $db = $data['barang'][$barang_c];
            if (isset($total_per_paket[$dbr['paket_ref']])) {
               $total_per_paket[$dbr['paket_ref']] += ($db['harga_' . $id_pelanggan_jenis] * $dbr['qty']);
            } else {
               $total_per_paket[$dbr['paket_ref']] = ($db['harga_' . $id_pelanggan_jenis] * $dbr['qty']);
            }
         }

         if ($id_sumber == 0) {
            $id_sumber = $this->userData['id_toko'];
         }

         $cek = $this->data('Barang')->cek_proses($barang_c, $id_sumber, $sn, $sds, $qty);
         if ($cek == false) {
            echo "Stok (" . $barang_c . ") kosong";
            exit();
         }
      }
      //===========================

      $data_harga = $this->db(0)->get('produk_harga');
      $detail_harga = [];
      foreach ($data['order'] as $do) {

         if ($id_pelanggan_jenis == 100) {
            $b_code = str_replace(['-', '&', '#'], '', $do['produk_code']);
            $barang = $this->db(0)->get_where_row('master_barang', "code = '" . $b_code . "'");
            if (!isset($barang['product_name'])) {
               echo "Nama Barang belum di tentukan";
               exit();
            }
         }

         if ($do['ref'] <> '') {
            $id_karyawan = $do['id_penerima'];
         }

         if (strlen($do['paket_ref']) > 0) {
            if ($do['price_locker'] == 1) {
               $harga_paket[$do['paket_ref']] = $data['paket'][$do['paket_ref']]['harga_' . $id_pelanggan_jenis];
               $id_margin[$do['paket_ref']]['id'] = $do['id_order_data'];
               $id_margin[$do['paket_ref']]['primary'] = 'id_order_data';
               $id_margin[$do['paket_ref']]['tb'] = 'order_data';

               $get = $this->db(0)->get_where_row('paket_order', "paket_ref = '" . $do['paket_ref'] . "' AND price_locker = 1");
               if (isset($get['jumlah'])) {
                  $paket_qty = $do['jumlah'] / $get['jumlah'];
                  $id_margin[$do['paket_ref']]['qty'] = $paket_qty;
               }
            }
         }

         $detail_harga = unserialize($do['detail_harga']);
         $countDH = count($detail_harga);
         $harga_code = $id_pelanggan_jenis;
         if ($id_pelanggan_jenis == 100) {
            $harga_code = 2;
         }
         foreach ($detail_harga as $kH => $dh_o) {
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $harga_code] <> 0) {
                  $countDH -= 1;
                  break;
               }
            }
         }

         if ($countDH <> 0) {
            echo "Lengkapi harga (" . $do['produk'] . ") terlebih dahulu!";
            exit();
         }
      }

      //updateFreq
      $this->db(0)->update("pelanggan", "freq = freq+1", "id_pelanggan = " . $id_pelanggan);
      //updateFreqCS
      $this->db(0)->update("karyawan", "freq_cs = freq_cs+1", "id_karyawan = " . $id_karyawan);

      foreach ($data['mutasi'] as $dbr) {
         $barang_c = $dbr['kode_barang'];
         $harga = $this->db(0)->get_where_row('master_barang', "code ='" . $barang_c . "'")['harga_' . $id_pelanggan_jenis];

         if ($harga == 0) {
            echo "Harga " . $barang_c . " belum ditentukan";
            exit();
         }

         $id_sumber = $dbr['id_sumber'];
         $qty = $dbr['qty'];
         $sds = $dbr['sds'];
         $sn =  $dbr['sn'];
         $sn_c = 0;
         if (strlen($sn) > 0) {
            $sn_c = 1;
         }

         $where = "id = " . $dbr['id'];
         $set = "margin_paket = 0, stat = 1, harga_jual = " . $harga . ", sn_c = " . $sn_c . ", cs_id = " . $id_karyawan . ", id_target = " . $id_pelanggan . ", jenis_target = " . $id_pelanggan_jenis . ", ref = '" . $ref . "'";
         $update = $this->db(0)->update("master_mutasi", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }

      foreach ($data['order'] as $do) {
         $detail_harga = unserialize($do['detail_harga']);
         $harga = 0;
         $diskon = 0;
         $jumlah = $do['jumlah'];

         foreach ($detail_harga as $key => $dh_o) {
            $diskon += ($dh_o['d'] * $jumlah);
            foreach ($data_harga as $dh) {
               if ($dh['code'] == $dh_o['c_h'] && $dh['harga_' . $harga_code] <> 0) {
                  $harga +=  $dh['harga_' . $harga_code];
                  $detail_harga[$key]['h'] = $dh['harga_' . $harga_code];
                  if (strlen($do['paket_ref']) > 0) {
                     if (isset($total_per_paket[$do['paket_ref']])) {
                        $total_per_paket[$do['paket_ref']] += ($detail_harga[$key]['h'] * $do['jumlah']);
                     } else {
                        $total_per_paket[$do['paket_ref']] = ($detail_harga[$key]['h'] * $do['jumlah']);
                     }
                  }
                  break;
               }
            }
         }

         $where = "id_order_data = " . $do['id_order_data'];
         $set = "margin_paket = 0, diskon = " . $diskon . ", detail_harga = '" . serialize($detail_harga) . "', harga = " . $harga . ", id_penerima = " . $id_karyawan . ", id_pelanggan = " . $id_pelanggan . ", id_pelanggan_jenis = " . $id_pelanggan_jenis . ", ref = '" . $ref . "', stok = " . $stok_order;
         $update = $this->db(0)->update("order_data", $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }

         if ($id_pelanggan_jenis == 100) {
            $b_code = str_replace(['-', '&', '#'], '', $do['produk_code']);
            $barang = $this->db(0)->get_where_row('master_barang', "code = '" . $b_code . "'");

            $qty = $do['jumlah'];
            $sds = 0;
            $sn =  "";
            $id_sumber = 0;
            $id_barang = $barang['id'];
            $h_beli = $barang['harga'];
            $target_id = $this->userData['id_toko'];

            $cols = 'ref,jenis,id_barang,kode_barang,id_sumber,id_target,harga_beli,qty';
            $vals = "'" . $ref . "',0," . $id_barang . ",'" . $b_code . "','" . $id_sumber . "','" . $target_id . "'," . $h_beli . "," . $qty;
            $do = $this->db(0)->insertCols('master_mutasi', $cols, $vals);

            if ($do['errno'] <> 0) {
               echo $do['error'];
               exit();
            }
         }
      }

      $adjuster = [];
      foreach ($total_per_paket as $key => $tpp) {
         $adjuster[$key] = ($data['paket'][$key]['harga_' . $id_pelanggan_jenis] * $id_margin[$key]['qty']) - $tpp;
         $id_margin[$key]['margin_paket'] = $adjuster[$key];
      }

      foreach ($id_margin as $key => $val) {
         $where = $val['primary'] . " = " . $val['id'];
         $set = "margin_paket = " . $val['margin_paket'];
         $update = $this->db(0)->update($val['tb'], $set, $where);
         if ($update['errno'] <> 0) {
            echo $update['error'];
            exit();
         }
      }

      unset($_SESSION['edit']);
      echo $id_pelanggan;
   }

   function deleteOrder()
   {
      $id_order = $_POST['id_order'];

      $cek_price_lock = $this->db(0)->get_where_row('order_data', 'id_order_data = ' . $id_order);
      if ($cek_price_lock['price_locker'] == 1) {
         $where = "paket_group = '" . $cek_price_lock['paket_group'] . "' AND paket_ref = '" . $cek_price_lock['paket_ref'] . "'";
         $do = $this->db(0)->delete_where('master_mutasi', $where);
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      } else {
         $where = "id_order_data =" . $id_order;
      }

      $do = $this->db(0)->delete_where('order_data', $where);
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
      $cek_price_lock = $this->db(0)->get_where_row('master_mutasi', 'id = ' . $id);
      if ($cek_price_lock['price_locker'] == 1) {
         $where = "paket_group = '" . $cek_price_lock['paket_group'] . "' AND paket_ref = '" . $cek_price_lock['paket_ref'] . "'";
         $do = $this->db(0)->delete_where('order_data', $where);
         if ($do['errno'] <> 0) {
            echo $do['error'];
            exit();
         }
      } else {
         $where = "id =" . $id;
      }

      $do = $this->db(0)->delete_where('master_mutasi', $where);
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
      $update = $this->db(0)->update("order_data", $set, $where);
      echo ($update['errno'] <> 0) ? $update['error'] : $update['errno'];
   }

   function add_produksi()
   {
      $code_s = strtoupper($_POST['product_code']);
      $code = str_replace(['-', '&', '#'], '', $code_s);
      $nama = strtoupper($_POST['nama']);

      //BARANG
      $cols = 'code, code_s, product_name, sp';
      $vals = "'" . $code . "','" . $code_s . "','" . $nama . "',1";
      $do = $this->db(0)->insertCols('master_barang', $cols, $vals);
      if ($do['errno'] <> 0) {
         if ($do['errno'] == 1062) {
            $up = $this->db(0)->update('master_barang', "product_name = '" . $nama . "'", "code = '" . $code . "' AND code_s = '" . $code_s . "'");
            if ($up['errno'] <> 0) {
               echo $up['error'];
               exit();
            }
         } else {
            echo $do['error'];
            exit();
         }
      }

      echo 0;
   }
}
