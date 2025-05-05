<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=500, user-scalable=no">
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Orins | Cek Order</title>
    <link href="<?= PV::ASSETS_URL ?>css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="<?= PV::ASSETS_URL ?>assets/img/favicon.png" />
    <script src="<?= PV::ASSETS_URL ?>js/feather.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/fontawesome-free-6.4.0-web/css/all.css" rel="stylesheet">
    <link href="<?= PV::ASSETS_URL ?>plugins/toggle/css/bootstrap-toggle.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
    <!-- FONT -->
</head>

<?php $fontStyle = "'Titillium Web', sans-serif;" ?>

<style>
    html {
        height: 100%;
    }

    html .table {
        font-family: <?= $fontStyle ?>;
    }

    html .content {
        font-family: <?= $fontStyle ?>;
    }

    html body {
        font-family: <?= $fontStyle ?>;
    }

    main {
        margin-bottom: 20px;
    }

    .col-t {
        line-height: 100%;
    }

    .td-auto {
        width: 1px;
        white-space: nowrap;
    }
</style>
</head>

<main class="container overflow-auto" style="max-width: 900px;height: 700px;">
    <div class="row mt-1 mx-0 pb-2">
        <?php
        $id_pelanggan = $data['id_pelanggan'];
        $no_pelanggan = $data['pelanggan'][$id_pelanggan]['no_hp'];
        $arr_tuntas = [];
        $loadRekap = [];

        $user_id = 0;

        foreach ($data['refs'] as $ref) {
        ?>
            <div class="col px-1 text-sm" style="min-width:400px;">
                <?php
                $no = 0;
                $bill = 0;
                $ambil = false;
                $ambil_all = true;

                $lunas = false;
                $verify_payment = 0;
                $pending_bayar = false;

                $dibayar = 0;
                $showMutasi = "";
                $xtraDiskon = 0;

                foreach ($data['kas'] as $dk) {
                    if ($dk['ref_transaksi'] == $ref) {
                        if ($dk['status_mutasi'] == 0 || $dk['status_mutasi'] == 1) {
                            $dibayar += $dk['jumlah'];
                        }
                        if ($dk['status_mutasi'] == 0) {
                            $pending_bayar = true;
                        }

                        if ($dk['metode_mutasi'] == 1 && $dk['status_setoran'] == 1) {
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

                        switch ($dk['status_mutasi']) {
                            case 0:
                                $statusP = "<small class='text-warning'>Office Checking</small> ";
                                $jumlahShow = "-Rp" . number_format($dk['jumlah']) . "<br>";
                                break;
                            case 1:
                                $statusP = '<small><i class="fa-solid fa-check text-success"></i></small> ';
                                $jumlahShow = "-Rp" . number_format($dk['jumlah']) . "<br>";
                                break;
                            default:
                                $statusP = '<small><span class="text-danger">' . $dk['note_batal'] . '</span> <i class="fa-solid fa-xmark text-danger"></i></small> ';
                                $jumlahShow = "<del>-Rp" . number_format($dk['jumlah']) . "</del><br>";
                                break;
                        }

                        if (isset($data['payment_account'][$dk['pa']]['payment_account'])) {
                            $payment_account = strtoupper($data['payment_account'][$dk['pa']]['payment_account']) . " ";
                        } else {
                            $payment_account = "";
                        }

                        $showMutasi .= "<small>" . $metod . "#" . $dk['id_kas'] . " " . $payment_account . $dk['note'] . " " . $statusP . $kembali_text . "</small> " . $jumlahShow;
                    }
                }

                $showSurcharge = "";
                if (isset($data['charge'][$ref])) {
                    foreach ($data['charge'][$ref] as $ds) {
                        if ($ds['cancel'] == 0) {
                            $bill += $ds['jumlah'];
                            $charge[$ref] = $ds['jumlah'];
                            $showSurcharge .= "<span class='text-primary'><small>Surcharge#" . $ds['id'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                        } else {
                            $showMutasi .= "<span><small>Surcharge#" . $ds['id'] . " <span class='text-danger'>" . $ds['cancel_reason'] . " <i class='fa-solid fa-xmark'></i></span></small> <del>Rp" . number_format($ds['jumlah']) . "</del></span><br>";
                        }
                    }
                }

                foreach ($data['diskon'] as $ds) {
                    if ($ds['ref_transaksi'] == $ref) {
                        if ($ds['cancel'] == 0) {
                            $xtraDiskon += $ds['jumlah'];
                            $dibayar += $ds['jumlah'];
                            $verify_payment += $ds['jumlah'];
                            if (in_array($this->userData['user_tipe'], PV::PRIV[2])) {
                                if (isset($data['order'][$ref]) && is_array($data['order'][$ref])) {
                                    foreach ($data['order'][$ref] as $do) {
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
                                if (isset($data['mutasi'][$ref]) && is_array($data['mutasi'][$ref])) {
                                    foreach ($data['mutasi'][$ref] as $do) {
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

                                $in_toko = "";
                                $dh = $data['head'][$ref];

                                $cs = $this->dKaryawanAll[$dh['cs']]['nama'];
                                $cs_to = "...";
                                if ($dh['cs_to'] <> 0) {
                                    $cs_to = $this->dKaryawanAll[$dh['cs_to']]['nama'];
                                }
                                ?>
                                <tr class="">
                                    <td colspan="5" class="table-light <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                        <table class="w-100 p-0 m-0 ">
                                            <tr>
                                                <td>
                                                    <span class="text-danger"><?= substr($ref, -5) ?></span> <b><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></b> #<?= substr($data['pelanggan'][$id_pelanggan]['id_pelanggan'], 2) ?> <small><?= $no_pelanggan ?></small>
                                                </td>
                                                <td class="text-end text-purple"><small><b><?= strtoupper($cs) ?></b></span></small></td>
                                                <td class="text-end ps-1" style="width: 1%; white-space:nowrap"><small><?= substr($dh['insertTime'], 2, -3) ?></small></td>
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

                                            $user_id = $do['id_user'];

                                            $cancel = $do['cancel'];
                                            $id_cancel = $do['id_cancel'];

                                            if ($cancel == 0) {
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
                                            $countSPK =  count($divisi_arr);
                                            foreach ($divisi_arr as $key => $dv) {
                                                foreach ($data['divisi'] as $dv_) {
                                                    if ($dv_['id_divisi'] == $key) {
                                                        $divisi[$key] = $dv_['divisi'];
                                                    }
                                                }
                                            } ?>
                                            <tr style="<?= ($cancel == 1) ? 'color:silver' : '' ?>">
                                                <td>
                                                    <table class="border-bottom text-sm">
                                                        <?php
                                                        if ($cancel <> 0) {
                                                            $canceler = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $id_cancel); ?>
                                                            <tr>
                                                                <td><span class="badge text-dark border border-dark"><?= $canceler ?> : <?= $do['cancel_reason'] ?></span></td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>
                                                            <td colspan="10">
                                                                <small><span class="badge bg-light text-dark"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                                <?php
                                                                if ($cancel == 1) { ?>
                                                                    <span class="text-nowrap text-success"><small><del><?= $id . "# " . ucwords($produk) ?></del></small></span>
                                                                <?php } else { ?>
                                                                    <span class="text-nowrap text-success"><small><?= $id . "# " . ucwords($produk) ?></small></span>
                                                                <?php } ?>
                                                            </td>
                                                        <tr>
                                                        <tr>
                                                            <td>
                                                                <table class="text-sm">
                                                                    <tr>
                                                                        <?php
                                                                        foreach ($detail_arr as $da) { ?>
                                                                            <td class="pe-1" style="line-height: 100%;">
                                                                                <?= "<small>" . $da['group_name'] . "</small> <br>" . strtoupper($da['detail_name']) ?>
                                                                            </td>
                                                                        <?php } ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div class="row bor">
                                                        <div class="col-auto">
                                                            <span>
                                                                <small>Catatan Utama<br><span class="text-danger"><?= $do['note'] ?></span></small>
                                                            </span>
                                                        </div>
                                                        <div class="col-auto">
                                                            <span>
                                                                <small>Catatan Produksi<br>
                                                                    <span class="text-primary">
                                                                        <?php
                                                                        foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                                            if (strlen($ns) > 0) {
                                                                                echo "<b>" . $this->model('Arr')->get($this->dDvs, "id_divisi", "divisi", $ks) . ":</b> " . $ns . ", ";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </span>
                                                                </small>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-nowrap td-auto" style="line-height: 120%;"><small>
                                                        <?php
                                                        foreach ($divisi as $key => $dvs) {
                                                            if ($divisi_arr[$key]['status'] == 1) {
                                                                $karyawan = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $divisi_arr[$key]['user_produksi']);
                                                                echo '<i class="fa-solid fa-check text-success"></i> ' . $dvs . " (" . $karyawan . ")<br>";
                                                            } else {
                                                                echo '<i class="fa-regular fa-circle"></i> ' . $dvs . "<br>";
                                                            }

                                                            if ($divisi_arr[$key]['cm'] == 1) {
                                                                if ($divisi_arr[$key]['cm_status'] == 1) {
                                                                    $karyawan = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $divisi_arr[$key]['user_cm']);
                                                                    echo '<i class="fa-solid text-success fa-check-double"></i> ' . $dvs . " (" . $karyawan . ")<br>";
                                                                } else {
                                                                    echo '<i class="fa-regular fa-circle"></i> ' . $dvs . '<br>';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <?php
                                                        $id_ambil = $do['id_ambil'];
                                                        if ($id_ambil == 0 && $cancel == 0) {
                                                            $ambil = true;
                                                            if ($countSPK > 0 && $cancel == 0) {
                                                                $ambil_all = false;
                                                                if ($do['id_afiliasi'] == 0) { ?>
                                                                    <span><i class="fa-regular fa-circle"></i> Ambil</span>
                                                                <?php } ?>
                                                        <?php }
                                                        } else {
                                                            if ($cancel == 0) {
                                                                $karyawan = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $id_ambil);
                                                                echo '<span class="text-purple"><i class="fa-solid fa-check"></i> Ambil (' . $karyawan . ")</span>";
                                                            }
                                                        } ?>
                                                    </small>
                                                </td>
                                                <td class="text-end td-auto"><?= number_format($do['jumlah']) ?></td>
                                                <td class="text-end td-auto">
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
                                            $jumlah_real = ($jumlah * $do['harga_jual']) + $do['margin_paket'] - ($do['diskon'] * $jumlah);

                                        ?>
                                            <tr style="<?= ($cancel_barang == 2) ? 'color:silver' : '' ?>">
                                                <td class="align-top">
                                                    <small><span class="badge bg-light text-dark"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                    <?= trim($dp['brand'] . " " . $dp['model']) ?><?= $dp['product_name'] ?>
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
                                    }


                                    if ($dibayar > 0 && $lunas == false && $sisa > 0) {
                                        $showMutasi .= "<span class='text-danger'><b>Sisa Rp" . number_format($sisa) . "</b></span>";
                                    }

                                    ?>
                                    <tr class="border-top">
                                        <td class="text-end text border-0" colspan="3">
                                        </td>
                                        <td class="text-end border-0" nowrap><?= ($lunas == true) ? '<i class="fa-solid text-success fa-circle-check"></i>' : '' ?>
                                            <?php if (isset($charge[$ref]) && $charge[$ref] > 0) { ?>
                                                <?= $showSurcharge ?>
                                            <?php } ?>
                                            <b>Rp<?= number_format($bill) ?></b>
                                        </td>
                                    </tr>
                                    <?php if (strlen($showMutasi) > 0) { ?>
                                        <tr>
                                            <td colspan="10">
                                                <?php
                                                if ($do['tuntas'] == 1) { ?>
                                                    <span class="badge bg-success">TUNTAS</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end text border-0" colspan="5">
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
</main>