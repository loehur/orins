<main>
    <div class="row mx-2" style="max-width:600px">
        <div class="col px-1">
            <select class="border rounded tize" name="id_pelanggan" required>
                <option></option>
                <?php foreach ($data['pelanggan'] as $p) { ?>
                    <option value="<?= $p['id_pelanggan'] ?>" <?= ($data['parse'] == $p['id_pelanggan'] ? "selected" : "") ?>><?= $this->dToko[$p['id_toko']]['inisial'] ?> <?= strtoupper($p['nama']) ?> #<?= substr($p['id_pelanggan'], -2) ?></option>
                <?php } ?>
            </select>
        </div>
        <?php if ($data['parse_2'] <> 0) { ?>
            <div class="col pe-0" style="min-width: 90px; max-width: 100px">
                <select class="border tize" name="y" required>
                    <?php
                    $yNow = date("Y");
                    for ($x = 2023; $x <= $yNow; $x++) { ?>
                        <option value="<?= $x ?>" <?= ($data['parse_2'] == $x) ? "selected" : "" ?>><?= $x ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
        <div class="col pt-auto mt-auto pe-0">
            <button type="submit" class="cek btn btn-light border">Cek</button>
        </div>
    </div>

    <!-- Main page content-->
    <div class="row mt-1 mx-2 pb-2">
        <?php
        $id_pelanggan = $data['parse'];
        $id_pelanggan_jenis = 0;
        $arr_tuntas = [];
        $loadRekap = [];
        $markRekap = [];

        $user_id = 0;

        foreach ($data['refs'] as $ref) { ?>
            <div class="col px-1 text-sm" style="min-width:400px;">
                <?php
                $no = 0;
                $bill = 0;
                $charge[$ref] = 0;
                $ambil = false;
                $ambil_all = true;

                $lunas = false;
                $verify_payment = 0;
                $pending_bayar = false;

                $dibayar = 0;
                $showMutasi = "";
                $xtraDiskon = 0;

                $showSurcharge = "";

                if (isset($data['charge'][$ref])) {
                    foreach ($data['charge'][$ref] as $ds) {
                        if ($ds['cancel'] == 0) {
                            $bill += $ds['jumlah'];
                            $charge[$ref] = $ds['jumlah'];
                            if (in_array($this->userData['user_tipe'], PV::PRIV[2])) {
                                if ($data['ref'][$ref]['tuntas'] == 0) {
                                    $showSurcharge .= "<i class='fa-regular fa-circle-xmark cancel_charge' data-id='" . $ds['id'] . "' data-bs-toggle='modal' style='cursor:pointer' data-bs-target='#modalCancelCharge'></i> <span class='text-primary'><small>Surcharge#" . $ds['id'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                                } else {
                                    $showSurcharge .= "<span class='text-primary'><small>Surcharge#" . $ds['id'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                                }
                            } else {
                                $showSurcharge .= "<span class='text-primary'><small>Surcharge#" . $ds['id'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                            }
                        } else {
                            $showMutasi .= "<span><small>Surcharge#" . $ds['id'] . " <span class='text-danger'>" . $ds['cancel_reason'] . " <i class='fa-solid fa-xmark'></i></span></small> <del>Rp" . number_format($ds['jumlah']) . "</del></span><br>";
                        }
                    }
                }

                if (isset($data['kas'][$ref])) {
                    foreach ($data['kas'][$ref] as $dk) {
                        if ($dk['status_mutasi'] == 0 || $dk['status_mutasi'] == 1) {
                            $dibayar += $dk['jumlah'];
                        }
                        if ($dk['status_mutasi'] == 0) {
                            $pending_bayar = true;
                        }

                        if ($dk['metode_mutasi'] == 1 && $dk['status_mutasi'] == 1 &&  $dk['status_setoran'] == 1) {
                            $verify_payment += $dk['jumlah'];
                        }

                        if (($dk['metode_mutasi'] == 2 || $dk['metode_mutasi'] == 3 || $dk['metode_mutasi'] == 4) && $dk['status_mutasi'] == 1) {
                            $verify_payment += $dk['jumlah'];
                        }

                        $metod = "";
                        switch ($dk['metode_mutasi']) {
                            case 1:
                                $metod = "Tunai";
                                break;
                            case 2:
                                $n_office = $dk['note_office'];
                                $metod = "NonTunai";
                                break;
                            case 3:
                                $n_office = $dk['note_office'];
                                $metod = "Afiliasi";
                                if (strlen($n_office) > 0) {
                                    $metod = "Afiliasi-" . $n_office;
                                }
                                break;
                            case 4:
                                $metod = "Saldo";
                                break;
                        }

                        $bayar = $dk['bayar'];
                        $kembali = $dk['kembali'];
                        if ($kembali > 0) {
                            $kembali_text = " (" . number_format($bayar) . "-" . number_format($kembali) . ")";
                        } else {
                            $kembali_text = '';
                        }

                        $show_charge = "";
                        if ($dk['charge'] > 0) {
                            $show_charge = "<small>(+" . $dk['charge'] * ($dk['jumlah'] / 100) . ")</small>";
                        }

                        switch ($dk['status_mutasi']) {
                            case 0:
                                $statusP = "<small class='text-warning'>Office Checking</small> ";
                                $jumlahShow = "-Rp" . number_format($dk['jumlah']) . $show_charge . "<br>";
                                break;
                            case 1:
                                $statusP = '<small><i class="fa-solid fa-check text-success"></i></small> ';
                                $jumlahShow = "-Rp" . number_format($dk['jumlah']) . $show_charge . "<br>";
                                break;
                            default:
                                $statusP = '<small><span class="text-danger">' . $dk['note_batal'] . '</span> <i class="fa-solid fa-xmark text-danger"></i></small> ';
                                $jumlahShow = "<del>-Rp" . number_format($dk['jumlah']) . $show_charge . "</del><br>";
                                break;
                        }

                        if (isset($data['payment_account'][$dk['pa']]['payment_account'])) {
                            $payment_account = $data['payment_account'][$dk['pa']]['payment_account'] . " ";
                        } else {
                            $payment_account = "";
                        }

                        $showMutasi .= "<small>" . $metod . "#" . $dk['id_kas'] . " " . $payment_account . $dk['note'] . " " . $statusP . $kembali_text . "</small> " . $jumlahShow;
                    }
                }

                $list_xdiskon = [];
                if (isset($data['diskon'][$ref])) {
                    foreach ($data['diskon'][$ref] as $ds) {
                        $list_xdiskon[$ref] = false;
                        if ($ds['cancel'] == 0) {
                            $xtraDiskon += $ds['jumlah'];
                            $dibayar += $ds['jumlah'];
                            $verify_payment += $ds['jumlah'];
                            if (in_array($this->userData['user_tipe'], PV::PRIV[2])) {
                                if (isset($data['order'][$ref]) && is_array($data['order'][$ref])) {
                                    foreach ($data['order'][$ref] as $do) {
                                        if ($do['ref'] == $ref) {
                                            $list_xdiskon[$ref] = true;
                                            if ($do['tuntas'] == 0) {
                                                $showMutasi .= "<i class='fa-regular fa-circle-xmark cancel_diskon' data-id='" . $ds['id_diskon'] . "' data-bs-toggle='modal' style='cursor:pointer' data-bs-target='#modalCancelDiskon'></i> <span class='text-success'><small>XtraDiskon#" . $ds['id_diskon'] . "</small> -Rp" . number_format($ds['jumlah']) . "<br></span>";
                                                break;
                                            } else {
                                                $showMutasi .= "<span class='text-success'><small>XtraDiskon#" . $ds['id_diskon'] . "</small> -Rp" . number_format($ds['jumlah']) . "<br></span>";
                                                break;
                                            }
                                        }
                                    }
                                }
                                if (isset($data['mutasi'][$ref]) && is_array($data['mutasi'][$ref])) {
                                    foreach ($data['mutasi'][$ref] as $do) {
                                        if ($list_xdiskon[$ref] == true) {
                                            continue;
                                        }

                                        if ($do['ref'] == $ref) {
                                            if ($do['tuntas'] == 0) {
                                                $showMutasi .= "<i class='fa-regular fa-circle-xmark cancel_diskon' data-id='" . $ds['id_diskon'] . "' data-bs-toggle='modal' style='cursor:pointer' data-bs-target='#modalCancelDiskon'></i> <span class='text-success'><small>XtraDiskon#" . $ds['id_diskon'] . "</small> -Rp" . number_format($ds['jumlah']) . "<br></span>";
                                                break;
                                            } else {
                                                $showMutasi .= "<span class='text-success'><small>XtraDiskon#" . $ds['id_diskon'] . "</small> -Rp" . number_format($ds['jumlah']) . "<br></span>";
                                                break;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $showMutasi .= "<span class='text-success'><small>XtraDiskon#" . $ds['id_diskon'] . "</small> -Rp" . number_format($ds['jumlah']) . "<br></span>";
                            }
                        } else {
                            $showMutasi .= "<span><small>XtraDiskon#" . $ds['id_diskon'] . " <span class='text-danger'>" . $ds['cancel_reason'] . " <i class='fa-solid fa-xmark'></i></span></small> <del>-Rp" . number_format($ds['jumlah']) . "</del><br></span>";
                        }
                    }
                }
                ?>
                <div class="pt-2 ps-0 pe-0">
                    <div class="border p-0">
                        <small>
                            <table class="table table-sm mb-0 text-sm">
                                <?php
                                $pelanggan = $data['pelanggan'][$id_pelanggan]['nama'];
                                $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];

                                $in_toko = "";
                                if ($id_toko_pelanggan <> $this->userData['id_toko']) {
                                    $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
                                }

                                $dh = $data['head'][$ref];

                                $cs = $data['karyawan'][$dh['cs']]['nama'];
                                $cs_to = "...";
                                if ($dh['cs_to'] <> 0) {
                                    $cs_to = $this->dKaryawanAll[$dh['cs_to']]['nama'];
                                }

                                $dRef = $data['ref'][$ref];
                                $mark = "";
                                $mark = strtoupper($dRef['mark']);
                                ?>
                                <tr class="">
                                    <td colspan="5" class="table-light <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                        <table class="w-100 p-0 m-0 text-sm">
                                            <tr>
                                                <td>
                                                    <span class="text-danger"><?= substr($ref, -4) ?></span> <b><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></b> <small><span class="fw-bold text-success"><?= $mark ?></span>#<?= substr($data['pelanggan'][$id_pelanggan]['id_pelanggan'], -2) ?></small>
                                                </td>
                                                <?php if ($dh['id_afiliasi'] == 0 || $dh['id_afiliasi'] <> $this->userData['id_toko']) { ?>
                                                    <td class="text-end text-purple"><b><?= strtoupper($cs) ?></b></span>
                                                    </td> <?php } else { ?>
                                                    <td class="text-end text-purple"><b><?= strtoupper($cs) ?>/<?= strtoupper($cs_to) ?></b></span>
                                                    </td>
                                                <?php }
                                                ?>
                                                <td class="text-end ps-1" style="width: 1%; white-space:nowrap"><small><?= date('d/m/y H:i', strtotime($dh['insertTime'])) ?></small></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tbody>
                                    <?php
                                    if (isset($data['order'][$ref])) {
                                        foreach ($data['order'][$ref] as $do) {
                                            $id_pelanggan_jenis = $do['id_pelanggan_jenis'];
                                            $id = $do['id_order_data'];
                                            $jumlah = $do['harga'] * $do['jumlah'];
                                            $id_ambil = $do['id_ambil'];
                                            $user_id = $do['id_user'];

                                            $cancel = $do['cancel'];
                                            $id_cancel = $do['id_cancel'];

                                            if ($cancel == 0 && $do['stok'] == 0) {
                                                $bill += $jumlah + $do['margin_paket'];
                                            }

                                            $bill -= $do['diskon'];

                                            $id_order_data = $do['id_order_data'];
                                            $id_produk = $do['id_produk'];
                                            $detail_arr = unserialize($do['produk_detail']);

                                            $dateTime = substr($do['insertTime'], 0, 10);
                                            $today = date("Y-m-d");

                                            $produk = strtoupper($do['produk']);

                                            $divisi_arr = unserialize($do['spk_dvs']);
                                            $divisi = [];
                                            $countSPK = count($divisi_arr);
                                            foreach ($divisi_arr as $key => $dv) {
                                                foreach ($data['divisi'] as $dv_) {
                                                    if ($dv_['id_divisi'] == $key) {
                                                        $divisi[$key] = $dv_['divisi'];
                                                    }
                                                }
                                            }
                                    ?>
                                            <tr style="<?= ($cancel == 1) ? 'color:silver' : '' ?>">
                                                <td>
                                                    <table class="text-sm">
                                                        <?php
                                                        if ($cancel <> 0) {
                                                            $canceler = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $id_cancel); ?>
                                                            <tr>
                                                                <td><span class="badge text-dark border border-dark"><?= $canceler ?> : <?= $do['cancel_reason'] ?></span></td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>
                                                            <td colspan="10">
                                                                <small><span class="badge bg-danger"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                                <?php
                                                                if ($cancel == 1) { ?>
                                                                    <span class="text-nowrap text-success"><small><del><?= $id . "# " . ucwords($produk) ?></del></small></span>
                                                                <?php } else { ?>
                                                                    <span class="text-nowrap text-success"><small><?= $id . "# " . ucwords($produk) ?></small></span>
                                                                <?php } ?>
                                                                <div class="btn-group">
                                                                    <button type="button" class="border-0 bg-white ps-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <span class="visually-hidden">Toggle Dropdown</span>
                                                                    </button>
                                                                    <ul class="dropdown-menu p-0 border-0 shadow-sm text-sm">
                                                                        <?php if ($id_ambil == 0 && $do['tuntas'] == 0 && $cancel == 0 && $do['id_afiliasi'] <> $this->userData['id_toko']) { ?>
                                                                            <li><a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="dropdown-item px-2 cancel" data-id="<?= $id ?>" href="#">Cancel</a></li>
                                                                        <?php } ?>
                                                                        <?php if ($do['tuntas'] == 1 && $do['refund'] == 0 && $cancel == 0 && $do['stok'] == 0 && in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                                                            <li><a data-bs-toggle="modal" data-bs-target="#modalRefund" class="dropdown-item px-2 refund" data-id="<?= $id ?>" href="#">Refund</a></li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                                <?php if ($do['id_afiliasi'] <> 0 && $do['id_afiliasi'] <> $this->userData['id_toko']) {
                                                                    $toko_aff = $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $do['id_afiliasi']);
                                                                    if ($do['status_order'] == 1) { ?>
                                                                        <span class="badge text-danger"><?= $toko_aff ?> - Checking</span></span>
                                                                    <?php } else {
                                                                        $cs_aff = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $do['id_user_afiliasi']); ?>
                                                                        <span class="badge text-success"><i class="fa-solid fa-circle-check"></i> <?= $cs_aff ?> - <?= $toko_aff ?></span>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="line-height: 100%;">
                                                                <div class="row mx-0">
                                                                    <?php
                                                                    foreach ($detail_arr as $da) { ?>
                                                                        <div class="col ps-0 pe-1"><?= "<small class='text-nowrap'><u>" . $da['group_name'] . "</u><br><span class='text-nowrap'>" . strtoupper($da['detail_name']) . "</span></small>" ?></div>
                                                                    <?php } ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div class="row bor mx-0">
                                                        <?php if (strlen($do['note'] > 0)) { ?>
                                                            <div class="col-auto ps-0">
                                                                <span>
                                                                    <small><b>Utama</b><br><span class="text-danger"><?= $do['note'] ?></span></small>
                                                                </span>
                                                            </div>
                                                        <?php } ?>

                                                        <?php
                                                        $spkR = [];
                                                        if (strlen($do['pending_spk']) > 1) {
                                                            $spkR = unserialize($do['pending_spk']);
                                                        }

                                                        foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                            if (strlen($ns) > 0 || isset($spkR[$ks])) { ?>
                                                                <div class="col px-0 text-sm">
                                                                    <small><span class="fw-bold"><?= $this->dDvs_all[$ks]["divisi"] ?></span></small>
                                                                    <small>
                                                                        <?php if (isset($spkR[$ks])) {
                                                                            $pendReady = explode("-", $spkR[$ks]); ?>
                                                                            <span class="badge bg-<?= $pendReady[1] == 'r' ? 'success' : 'danger' ?>"><?= $data['spk_pending'][$pendReady[0]][$pendReady[1]] ?></span>
                                                                        <?php } ?>
                                                                    </small>
                                                                    <br>
                                                                    <span data-id="<?= $id_order_data ?>" data-col="<?= $ks ?>" data-mode="<?= $ks ?>" class="cell_edit text-primary"><?= $ns ?></span>
                                                                </div>
                                                        <?php }
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="text-nowrap" style="line-height: 120%;"><small>
                                                        <?php
                                                        foreach ($divisi as $key => $dvs) {
                                                            if ($divisi_arr[$key]['status'] == 1) {
                                                                $karyawan = $this->dKaryawanAll[$divisi_arr[$key]['user_produksi']]["nama"];
                                                                echo '<i class="fa-solid fa-check text-success"></i> ' . $dvs . " (" . $karyawan . ")<br>";
                                                            } else {
                                                                echo '<i class="fa-regular fa-circle"></i> ' . $dvs . "<br>";
                                                            }

                                                            if ($divisi_arr[$key]['cm'] == 1) {
                                                                if ($divisi_arr[$key]['cm_status'] == 1) {
                                                                    $karyawan = $this->dKaryawanAll[$divisi_arr[$key]['user_cm']]["nama"];
                                                                    echo '<i class="fa-solid text-success fa-check-double"></i> ' . $dvs . " (" . $karyawan . ")<br>";
                                                                } else {
                                                                    echo '<i class="fa-regular fa-circle"></i> ' . $dvs . '<br>';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <?php
                                                        if ($id_ambil == 0 && $cancel == 0) {
                                                            $ambil = true;
                                                            if ($countSPK > 0 && $cancel == 0) {
                                                                $ambil_all = false;
                                                                if ($do['id_afiliasi'] == 0) { ?>
                                                                    <span class="btnAmbil" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal4" data-id="<?= $id ?>"><i class="fa-regular fa-circle"></i> Ambil</span>
                                                                <?php } ?>
                                                        <?php }
                                                        } else {
                                                            if ($cancel == 0) {
                                                                $karyawan = $this->dKaryawanAll[$id_ambil]["nama"];
                                                                echo '<span class="text-purple"><i class="fa-solid fa-check"></i> Ambil (' . $karyawan . ")</span>";
                                                            }
                                                        } ?>
                                                    </small>
                                                </td>
                                                <td class="text-end">
                                                    <?= number_format($do['jumlah']) ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php
                                                    if ($do['margin_paket'] == 0) {
                                                        if ($do['diskon'] > 0) { ?>
                                                            <del>Rp<?= number_format($jumlah) ?></del><br><small>Disc. Rp<?= number_format($do['diskon']) ?></small><br>Rp<?= number_format($jumlah - $do['diskon']) ?>
                                                        <?php } else { ?>
                                                            <?= number_format($jumlah) ?>
                                                        <?php }
                                                    } else {
                                                        if ($do['diskon'] > 0) { ?>
                                                            <del>Rp<?= number_format($jumlah + $do['margin_paket']) ?></del><br><small>Disc. Rp<?= number_format($do['diskon']) ?></small><br>Rp<?= number_format($jumlah - $do['diskon'] + $do['margin_paket']) ?>
                                                        <?php } else { ?>
                                                            <?= number_format($jumlah + $do['margin_paket']) ?>
                                                    <?php }
                                                    } ?>
                                                    <br>
                                                    <?php if ($do['refund'] > 0) { ?>
                                                        <span class="text-danger text-sm"><small>RF<?= str_replace("-", "", $do['refund_date']) ?><br><?= number_format($do['refund']) ?></small></span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php }
                                    }

                                    if (isset($data['mutasi'][$ref])) {
                                        foreach ($data['mutasi'][$ref] as $do) {
                                            $no += 1;
                                            $user_id = $do['user_id'];
                                            $cancel_barang = $do['stat'];
                                            $jumlah = $do['qty'];
                                            $id_pelanggan_jenis = $do['jenis_target'];
                                            $dp = $data['barang'][$do['id_barang']];

                                            if ($cancel_barang <> 2) {
                                                $bill += (($jumlah * $do['harga_jual']) + $do['margin_paket']);
                                                $bill -= ($do['diskon'] * $jumlah);
                                            }

                                            $jumlah_semula = "";
                                            if ($do['diskon'] > 0) {
                                                $jumlah_semula = "<s>" . number_format(($jumlah * $do['harga_jual']) + $do['margin_paket']) . "</s><br><small>Disc. " . number_format($do['diskon'] * $jumlah) . "</small><br>";
                                            }
                                            $jumlah_real = ($jumlah * $do['harga_jual']) + $do['margin_paket'] - ($do['diskon'] * $jumlah); ?>
                                            <tr style="<?= ($cancel_barang == 2) ? 'color:silver' : '' ?>">
                                                <td class="align-top">
                                                    <small><span class="badge bg-danger"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                    <small><span class="badge bg-success"><?= $do['fp'] == 1 ? "FP" : "" ?></span></small>
                                                    <?= trim($dp['brand'] . " " . $dp['model']) ?><?= $dp['product_name'] ?>

                                                    <div class="btn-group">
                                                        <button type="button" class="border-0 bg-white ps-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu p-0 border-0 shadow-sm text-sm">
                                                            <li><a class="dropdown-item px-2 ajax" href="<?= PV::BASE_URL ?>Data_Operasi/faktur_pajak/<?= $do['id'] ?>/<?= $do['fp'] == 1 ? 0 : 1 ?>">Faktur Pajak (<?= $do['fp'] == 1 ? "-" : "+" ?>)</a></li>
                                                            <?php if ($dibayar == 0 && $do['stat'] == 1) { ?>
                                                                <li><a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="dropdown-item cancelBarang px-2" data-id="<?= $do['id'] ?>" href="#">Cancel</a></li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td class=""><small>
                                                        <?= $do['sds'] == 1 ? "S" : "" ?>#<?= $do['sn'] ?>
                                                </td>
                                                <td class="text-end"><?= number_format($jumlah) ?></td>
                                                <td class="text-end">
                                                    <?= $jumlah_semula ?>
                                                    <?= number_format($jumlah_real) ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    }

                                    $sisa = $bill - $dibayar;

                                    if ($sisa <= 0 && $pending_bayar == false) {
                                        $lunas = true;
                                    }

                                    if ($sisa <> 0) {
                                        $loadRekap[$id_pelanggan . "_" . $ref] = $sisa;
                                        $markRekap[$id_pelanggan . "_" . $ref] = $mark;
                                    }

                                    if ($dibayar > 0 && $lunas == false && $sisa > 0) {
                                        $showMutasi .= "<span class='text-danger'><b>Sisa Rp" . number_format($sisa) . "</b></span>";
                                    }

                                    ?>
                                    <tr class="border-top">
                                        <td class="text-end text border-0 pb-0" colspan="3">
                                            <?php if (($dh['id_afiliasi'] == 0 || $dh['id_afiliasi'] <> $this->userData['id_toko'])) { ?>
                                                <table>
                                                    <tr>
                                                        <td class="text-end pe-1"><small><a href="<?= PV::BASE_URL; ?>Data_Order/print/<?= $ref ?>" target="_blank" class="btnBayar rounded border-0 px-1 text-dark text-decoration-none"><i class="fa-solid fa-print"></i></a></small></td>
                                                        <?php
                                                        if ($ambil_all == false) { ?>
                                                            <td class="text-end pe-1"><small><span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal3" class="btnAmbilSemua rounded border-0 text-primary px-1" data-ref="<?= $do['ref'] ?>">Ambil</span></small></td>
                                                        <?php } ?>
                                                        <td class="text-end pe-1">
                                                            <button type="button" class="border-0 bg-white ps-0 dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <small>
                                                                    <span data-bs-toggle="modal" data-bs-target="#modalDiskon" data-bs-toggle="dropdown" class="border-0 rounded text-info px-1 dropdown-toggle dropdown-toggle-split">
                                                                        <i class="fa-solid fa-sliders"></i>
                                                                    </span>
                                                                </small>
                                                                <span class="visually-hidden">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu p-0 border-0 shadow rounded-0">
                                                                <?php if ($do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalMark" class="dropdown-item markRef px-2" data-ref="<?= $ref ?>" href="#"><small>Mark</small></a></li>
                                                                <?php } ?>
                                                                <?php if ($user_id == $this->userData['id_user'] && $do['tuntas'] == 0) { ?>
                                                                    <li><a class="dropdown-item px-2" href="<?= PV::BASE_URL ?>Buka_Order/Edit_order/<?= $ref ?>/<?= $id_pelanggan_jenis ?>/<?= $dibayar ?>/<?= $id_pelanggan ?>"><small>Tambah Order</small></a></li>
                                                                <?php } else { ?>
                                                                    <li><a class="dropdown-item px-2" href="#"><small>CreatorID #<?= $user_id ?></small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalCharge" class="dropdown-item tambahCharge px-2" data-ref="<?= $ref ?>" href="#"><small>Surcharge</small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $sisa > 0 && $do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalDiskon" class="dropdown-item xtraDiskon px-2" data-sisa="<?= $sisa ?>" data-ref="<?= $ref ?>" href="#"><small>Extra Diskon</small></a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </td>
                                                        <td class="text-sm pe-1">
                                                            <small><?= $dh['user_id'] ?>#</small>
                                                        </td>
                                                        <td class="text-sm align-middle" style="cursor: pointer;">
                                                            <?php if ($data['cust_wa']) { ?>
                                                                <span onclick="copy('<?= $data['cust_wa'] ?>', <?= $ref ?>)" class="text-success"><i class="fa-brands fa-whatsapp"></i></span>
                                                            <?php } ?>
                                                            &nbsp;
                                                            <span onclick="copy('<?= $ref ?>', <?= $ref ?>)" class="text-primary"><i class="fa-solid fa-receipt"></i></i></span>
                                                            <small><span id="span_copy_<?= $ref ?>" class="text-success fw-bold" style="display: none;">Copied!</span></small>
                                                        </td>
                                                        <td class="ps-2 text-sm">
                                                            <?php if (isset($data['karyawan'][$data['ref'][$ref]['ready_cs']])) { ?>
                                                                <i class="fa-solid fa-check-double"></i> <?= $data['karyawan'][$data['ref'][$ref]['ready_cs']]['nama'] ?>
                                                            <?php } ?>
                                                            &nbsp;
                                                            <?php if (isset($data['karyawan'][$data['ref'][$ref]['ready_aff_cs']])) { ?>
                                                                <i class="fa-solid fa-check-double"></i> <?= $data['karyawan'][$data['ref'][$ref]['ready_aff_cs']]['nama'] ?>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            <?php } else { ?>
                                                <table>
                                                    <tr>
                                                        <td class="text-end pe-1"><small><a href="<?= PV::BASE_URL; ?>Data_Order/print/<?= $ref ?>" target="_blank" class="btnBayar border btn btn-sm px-1"><i class="fa-solid fa-print"></i></a></small></td>
                                                        <td class="text-sm pe-1">
                                                            <small><?= $dh['user_id'] ?>#</small>
                                                        </td>
                                                        <td class="text-sm align-middle" style="cursor: pointer;">
                                                            <?php if ($data['cust_wa']) { ?>
                                                                <span onclick="copy('<?= $data['cust_wa'] ?>', <?= $ref ?>)" class="text-success"><i class="fa-brands fa-whatsapp"></i></span>
                                                            <?php } ?>
                                                            <span onclick="copy('<?= $ref ?>', <?= $ref ?>)" class="text-primary"><i class="fa-solid fa-receipt"></i></i></span>
                                                            <small><span id="span_copy_<?= $ref ?>" class="text-success fw-bold" style="display: none;">Copied!</span></small>
                                                        </td>
                                                        <td>
                                                            <?php if (isset($data['karyawan'][$data['ref'][$ref]['ready_cs']])) { ?>
                                                                <?= $data['karyawan'][$data['ref'][$ref]['ready_cs']]['nama'] ?>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            <?php } ?>
                                        </td>
                                        <td class="text-end border-0" nowrap>
                                            <?php if ($charge[$ref] > 0) { ?>
                                                <?= $showSurcharge ?>
                                            <?php } ?>
                                            <?= ($lunas == true) ? '<i class="fa-solid text-success fa-circle-check"></i>' : '' ?> <b>Rp<?= number_format($bill) ?></b>
                                        </td>
                                    </tr>
                                    <?php if (strlen($showMutasi) > 0) { ?>
                                        <tr>
                                            <td class="text-end text border-0 pt-0" colspan="4">
                                                <?= $showMutasi ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </small>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row mx-0 px-2 mt-2">
        <?php if (isset($dh)) { ?>
            <?php if (($dh['id_afiliasi'] == 0 || $dh['id_afiliasi'] <> $this->userData['id_toko']) && $dh['tuntas'] == 0) { ?>
                <div class="col px-1 text-sm" id="loadMulti" style="max-width: 600px;">
                    <form action="<?= PV::BASE_URL; ?>Data_Operasi/bayar_multi" method="POST">
                        <div class="border px-1 pb-1">
                            <small>
                                <table class="table table-sm mb-0 table-borderless text-sm">
                                    <tr class="">
                                        <td colspan="5" class="text-end py-2"><b>PEMBAYARAN MULTI</b></td>
                                    </tr>
                                    <tr>
                                        <td>Metode</td>
                                        <td class="pb-2 pt-2" colspan="2">
                                            <select name="metode_multi" class="form-select metodeBayar_multi" required>
                                                <option value=""></option>
                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                                    <option value="1">Tunai</option>
                                                <?php } ?>
                                                <option value="2">Non Tunai</option>
                                                <option value="3">Afiliasi</option>
                                                <?php if ($data['saldo'] > 0) { ?>
                                                    <option selected value="4">Saldo [ <?= number_format($data['saldo']) ?> ]</option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td id="clearCheck" valign="bottom" class="text-end text-info" style="cursor: pointer;">
                                            Clear <i class="fa-regular fa-square"></i>
                                        </td>
                                    </tr>
                                    <tr id="noteBayar_multi" class="border-top" style="display:none">
                                        <td class="pe-2 text-danger" nowrap>Catatan Transaksi</td>
                                        <td colspan="4" class="pb-2 pt-2">
                                            <input type="text" name="note_multi" class="form-control border border-danger">
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td colspan="3" class="pb-1"></td>
                                    </tr>
                                    <?php
                                    $totalTagihan = 0;
                                    foreach ($loadRekap as $key => $value) { ?>
                                        <tr class='hoverBill'>
                                            <td colspan="2"><span class='text-dark'><?= $key ?></span></td>
                                            <td class="text-end align-middle">
                                                <span class="fw-bold text-success me-1"><small><?= $markRekap[$key] ?></small></span>
                                                <input type='checkbox' class='cek_multi form-check-input' name="ref_multi[]" value="<?= $key ?>_<?= $value ?>" data-jumlah='<?= $value ?>' data-ref='<?= $key ?>' checked>
                                            </td>
                                            <td class='text-end ps-2'>Rp<?= number_format($value) ?></td>
                                        </tr>
                                    <?php
                                        $totalTagihan += $value;
                                    } ?>
                                    <tr>
                                        <td class="pb-2 pr-2" nowrap>
                                            <b>TOTAL TAGIHAN</b>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end">
                                            <span data-total=''><b>Rp<span id="totalBill" data-total="<?= $totalTagihan ?>"><?= number_format($totalTagihan) ?></span></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Bayar</td>
                                        <td class="pb-2" colspan="3">
                                            <span class="bayarPasMulti text-danger" style="cursor:pointer"><small>Bayar Pas (Click)</small></span>
                                            <input id="bayarBill" name="dibayar_multi" class="text-end form-control" type="number" min="1" value="" required />
                                        </td>
                                    </tr>
                                    <tr id="payment_account" class="border-top" style="display:none">
                                        <td colspan="10" class="p-0">
                                            <table class="table p-0 text-sm">
                                                <tr>
                                                    <td class="pe-1" style="width: 80px;">
                                                        <small>+Charge (%)</small>
                                                        <input name="charge" class="text-center form-control" type="number" step="0.1" min="1" max="100" value="" />
                                                    </td>
                                                    <td class="pe-1" style="width: 100px;">
                                                        <span class=""><small>Charge</small></span>
                                                        <input id='total_charge' name="total_charge" class="text-end form form-control" type="number" readonly />
                                                    </td>
                                                    <td class="" style="width: 110px;">
                                                        <span class=""><small>Total+Charge</small></span>
                                                        <input id='total_aftercas' name="total_aftercas" class="text-end form form-control" type="number" readonly />
                                                    </td>
                                                    <td class="ps-1">
                                                        <span class="text-success"><small>Akun Pembayaran</small></span>
                                                        <select name="payment_account" class="border border-success rounded tize">
                                                            <option value=""></option>
                                                            <?php foreach ($data['payment_account'] as $pa) { ?>
                                                                <option value="<?= $pa['id'] ?>"><?= strtoupper($pa['payment_account']) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kembalian</td>
                                        <td colspan="2"><input id='kembalianBill' name="kembalianBill" class="text-end form form-control" type="number" readonly /></td>
                                        <td class="text-end" nowrap>
                                            <button type="submit" id="btnBayarBill" class='btn btn-primary w-100'>Bayar</button>
                                        </td>
                                    </tr>
                                </table>
                            </small>
                        </div>
                    </form>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (count($data['r_kas']) > 0) { ?>
            <div class="col px-1 text-sm" style="min-width: 400px;">
                <div class="mb-1 text-success"><small>Riwayat Pembayaran</small></div>
                <table class="table table-sm border text-sm">
                    <?php
                    foreach ($data['r_kas'] as $rk) {
                        $cl_tb = "";
                        switch ($rk['status_mutasi']) {
                            case 0:
                                $statusP = "<small class='text-warning'>Office Checking</small> ";
                                break;
                            case 1:
                                $statusP = '<small><i class="fa-solid fa-check text-success"></i></small> ';
                                break;
                            default:
                                $statusP = '<small><span class="text-danger"><i class="fa-solid fa-xmark text-danger"></i></small> ';
                                $cl_tb = "table-secondary";
                                break;
                        }

                        $metod = "";
                        switch ($rk['metode_mutasi']) {
                            case 1:
                                $metod = "Tunai";
                                break;
                            case 2:
                                $metod = "NonTunai";
                                break;
                            case 3:
                                $metod = "Afiliasi";
                                break;
                            case 4:
                                $metod = "Saldo Deposit";
                                break;
                        } ?>
                        <tr class="<?= $cl_tb ?>">
                            <td>
                                <?= substr($rk['ref_bayar'], 0, 4) . "-" . substr($rk['ref_bayar'], 4, 2) . "-" . substr($rk['ref_bayar'], 6, 2) . " " . substr($rk['ref_bayar'], 8, 2) . ":" . substr($rk['ref_bayar'], 10, 2) ?>
                                <br><?= $metod ?>
                            </td>
                            <td class="text-end">
                                Bayar: <?= number_format($rk['bayar']) ?><br>
                                Kembali: <?= number_format($rk['kembali']) ?></td>
                            <td class="text-end">
                                Total: <?= number_format($rk['total']) ?><br>
                                <?= $statusP ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </div>
</main>

<?php require_once('form.php') ?>
<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    var totalBill = 0;
    $(document).ready(function() {
        $('select.tize').selectize();

        var parse_2 = <?= $data['parse_2'] ?>;
        //MULTI
        totalBill = $("span#totalBill").attr("data-total");
        json_rekap = [<?= json_encode($loadRekap) ?>];

        totalBill = $("span#totalBill").attr("data-total");
        if (totalBill == 0) {
            $("div#loadMulti").fadeOut('fast');
        }
    });

    $("a.ajax").click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.ajax({
            url: href,
            type: 'POST',
            data: {},
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    })

    $('button.cek').click(function() {
        var parse = $("select[name=id_pelanggan]").val();
        var parse_2 = $("select[name=y]").val() || 0;
        location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + parse + "/" + parse_2;
    });

    $("a.xtraDiskon").click(function() {
        var ref = $(this).attr("data-ref");
        var max_diskon = $(this).attr("data-sisa");
        $("input[name=ref_diskon]").val(ref);
        $("input[name=max_diskon]").val(max_diskon);
    })

    $("a.tambahCharge").click(function() {
        var ref = $(this).attr("data-ref");
        $("input[name=ref_charge]").val(ref);
    })

    $("a.markRef").click(function() {
        var ref_ = $(this).attr("data-ref");
        $("input[name=ref_mark]").val(ref_);
    })

    var bill = 0;
    $("span.btnBayar").click(function() {
        bill = $(this).attr("data-bill");
        client = $(this).attr("data-client");
        $("input.bill").val(bill);
        var ref = $(this).attr("data-ref");
        $("input#refBayar").val(ref);
        $("input#client").val(client);
    })

    $("span.bayarPas").click(function() {
        bill = $("input[name=bill]").val();
        $("input.dibayar").val(bill);
        kembalian();
    })


    $("span.btnAmbil").click(function() {
        id = $(this).attr("data-id");
        $("input[name=ambil_id]").val(id);
    })

    $("a.cancel").click(function() {
        id = $(this).attr("data-id");
        $("input[name=cancel_id]").val(id);
        $("input[name=tb]").val(0);
    })

    $("a.refund").click(function() {
        id = $(this).attr("data-id");
        $("input[name=refund_id]").val(id);
    })

    $("a.cancelBarang").click(function() {
        id = $(this).attr("data-id");
        $("input[name=cancel_id]").val(id);
        $("input[name=tb]").val(1);
    })

    $(".cancel_diskon").click(function() {
        var id = $(this).attr("data-id");
        $("input[name=cancel_id_diskon]").val(id);
    })

    $(".cancel_charge").click(function() {
        var id = $(this).attr("data-id");
        $("input[name=cancel_id_charge]").val(id);
    })

    $("span.btnAmbilSemua").click(function() {
        ref = $(this).attr("data-ref");
        $("input[name=ambil_ref]").val(ref);
    })

    function kembalian() {
        var kembalian = 0;
        var dibayar = $("input.dibayar").val();
        kembalian = dibayar - bill;
        if (kembalian < 0) {
            kembalian = 0;
        }
        $("input.kembalian").val(kembalian);
    }

    $("input.dibayar").on("keyup change", function() {
        kembalian();
    });

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            }
        });
    });

    $("select.metodeBayar_multi").on("keyup change", function() {
        if ($(this).val() == 1 || $(this).val() == 2 || $(this).val() == 3) {
            $("tr#noteBayar_multi").show();
        } else {
            $("tr#noteBayar_multi").hide();
        }

        if ($(this).val() == 2) {
            $("tr#payment_account").show();
        } else {
            $("input[name=charge]").val("");
            total_aftercas();
            $("tr#payment_account").hide();
        }
    });

    //MULTI
    $("input.cek_multi").change(function() {
        var jumlah = $(this).attr("data-jumlah");
        let refRekap = $(this).attr("data-ref");

        if ($(this).is(':checked')) {
            totalBill = parseInt(totalBill) + parseInt(jumlah);
            json_rekap[0][refRekap] = jumlah;
        } else {
            delete json_rekap[0][refRekap];
            totalBill = parseInt(totalBill) - parseInt(jumlah);
        }

        $("span#totalBill").html(totalBill.toLocaleString('en-US')).attr("data-total", totalBill);

        bayarBill();
    })

    $("span.bayarPasMulti").on('click', function() {
        $("input#bayarBill").val(totalBill);
        bayarBill();
    });

    function bayarBill() {
        var dibayar = parseInt($('input#bayarBill').val());
        var kembalian = parseInt(dibayar) - parseInt(totalBill);
        if (kembalian > 0) {
            $('input#kembalianBill').val(kembalian);
        } else {
            $('input#kembalianBill').val(0);
        }

        total_aftercas();
    }

    $("input[name=charge]").on("keyup change", function() {
        total_aftercas();
    })

    function total_aftercas() {
        var dibayar = parseInt($('input#bayarBill').val());
        var charge = $("input[name=charge]").val();
        $("input#total_aftercas").val(parseInt(dibayar) + (parseInt(dibayar) * (parseFloat(charge) / 100)));
        $("input#total_charge").val((parseInt(dibayar) * (parseFloat(charge) / 100)));
    }

    $("input#bayarBill").on("keyup change", function() {
        bayarBill();
    });

    $("td#clearCheck").click(function() {
        $("input.cek_multi").prop('checked', false);
        totalBill = 0;
        $("span#totalBill").html(totalBill.toLocaleString('en-US')).attr("data-total", totalBill);
        bayarBill();
    })

    function copy(text, ref) {
        var temp = $("<input id=temp />");
        $("body").append(temp);
        temp.val(text)
        temp.select();
        document.execCommand("copy");
        temp.remove();

        $("span#span_copy_" + ref).fadeIn(200);
        $("span#span_copy_" + ref).fadeOut(1000);
    }
</script>