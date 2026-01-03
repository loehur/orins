<div class="container mx-auto pt-4" style="min-width: 450px;">
    <!-- Main page content-->
    <div class="row mx-0">
        <div class="col">
            <span class="fw-bold"></span><br>
        </div>
        <div class="col text-end">
            <i>Orins Pro</i> - Realtime Invoice
        </div>
    </div>

    <div class="row mx-0 pb-2">
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
                $ada_produksi[$ref] = false;
                $no = 0;
                $bill = 0;
                $charge[$ref] = 0;
                $ambil_all[$ref] = true;
                $ambil_all_aff[$ref] = true;
                $readyAFF[$ref] = 0;

                $lunas = false;
                $verify_payment = 0;
                $pending_bayar = false;

                $dibayar = 0;
                $showMutasi = "";
                $xtraDiskon = 0;
                $id_toko[$ref] = 0;
                $id_penerima[$ref] = 0;
                $id_afiliasi[$ref] = 0;
                $id_user_afiliasi[$ref] = 0;

                $showSurcharge = "";

                if (isset($data['charge'][$ref])) {
                    foreach ($data['charge'][$ref] as $ds) {
                        if ($ds['cancel'] == 0) {
                            $bill += $ds['jumlah'];
                            $charge[$ref] = $ds['jumlah'];
                            $showSurcharge .= "<span><small>Surcharge#" . $ds['id'] . " " . $ds['note'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                        } else {
                            $showMutasi .= "<span><small>Surcharge#" . $ds['id'] . " " . $ds['note'] . "<span class='text-danger'>" . $ds['cancel_reason'] . " <i class='fa-solid fa-xmark'></i></span></small> <del>Rp" . number_format($ds['jumlah']) . "</del></span><br>";
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
                                $statusP = "<small class='text-warning'>Checking</small> ";
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
                            $showMutasi .= "<span class='text-success'><small>XtraDiskon#" . $ds['id_diskon'] . "</small> -Rp" . number_format($ds['jumlah']) . "<br></span>";
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
                                $dh = $data['head'][$ref];

                                $dRef = $data['ref'][$ref];
                                $mark = "";
                                $mark = strtoupper($dRef['mark']);
                                ?>
                                <tr>
                                    <td colspan="5" class="table-light">
                                        <table class="w-100 p-0 m-0 text-sm">
                                            <tr>
                                                <td>
                                                    <?= substr($ref, 0, strlen($ref) - 4) ?>-<span class="text-success fw-bold"><?= substr($ref, -4) ?></span>
                                                    <small class="float-end"><?= date('d/m/y H:i', strtotime($dh['insertTime'])) ?></small>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tbody>
                                    <?php
                                    if (isset($data['order'][$ref])) {
                                        $ada_produksi[$ref] = true;
                                        foreach ($data['order'][$ref] as $do) {
                                            $id_pelanggan_jenis = $do['id_pelanggan_jenis'];
                                            $id = $do['id_order_data'];
                                            $jumlah = $do['harga'] * $do['jumlah'];
                                            $id_ambil = $do['id_ambil'];
                                            $id_ambil_aff = $do['id_ambil_aff'];
                                            $id_ambil_driver = $do['id_ambil_driver'];
                                            $user_id = $do['id_user'];
                                            $id_toko[$ref] = $do['id_toko'];
                                            $id_penerima[$ref] = $do['id_penerima'];

                                            $cancel = $do['cancel'];
                                            $id_cancel = $do['id_cancel'];

                                            if ($cancel == 0 && $do['stok'] == 0) {
                                                $bill += $jumlah + $do['harga_paket'];
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
                                            } ?>

                                            <tr>
                                                <td style="min-width: 250px;">
                                                    <table class="text-sm">
                                                        <tr>
                                                            <td colspan="10" style="line-height: 100%;">
                                                                <small><span class="badge bg-light text-dark"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                                <span class="text-nowrap text-primary"><small><?= ucwords($produk) ?></small></span>
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
                                                </td>
                                                <td class="text-nowrap" style="line-height: 120%;width:80px"><small>
                                                        <?php
                                                        foreach ($divisi as $key => $dvs) {

                                                            if ($divisi_arr[$key]['status'] == 1) {
                                                                echo '<i class="fa-solid fa-check text-success"></i> ';
                                                            } else {
                                                                echo '<i class="fa-regular fa-circle"></i> ';
                                                            }

                                                            if ($divisi_arr[$key]['cm'] == 1) {
                                                                if ($divisi_arr[$key]['cm_status'] == 1) {
                                                                    echo '<i class="fa-solid text-success fa-check-double"></i> ';
                                                                } else {
                                                                    echo '<i class="fa-regular fa-circle"></i> ';
                                                                }
                                                            }

                                                            echo $dvs . '<br>';
                                                        }
                                                        ?>
                                                        <?php if ($id_ambil == 0) { ?>
                                                            <?php if ($countSPK > 0) { ?>
                                                                <i class="fa-regular fa-circle"></i> Ambil</span>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <span class="text-primary"><i class="fa-solid fa-check-double"></i> Ambil</span>
                                                        <?php } ?>
                                                    </small>
                                                </td>
                                                <td class="text-end" style="width: 50px;">
                                                    <?= number_format($do['jumlah']) ?>
                                                </td>
                                                <td class="text-end" style="width: 95px;">
                                                    <?php
                                                    if ($do['harga_paket'] == 0) {
                                                        if ($do['diskon'] > 0) { ?>
                                                            <del>Rp<?= number_format($jumlah) ?></del><br><small>Disc. Rp<?= number_format($do['diskon']) ?></small><br>Rp<?= number_format($jumlah - $do['diskon']) ?>
                                                        <?php } else { ?>
                                                            <?= number_format($jumlah) ?>
                                                        <?php }
                                                    } else {
                                                        if ($do['diskon'] > 0) { ?>
                                                            <del>Rp<?= number_format($jumlah + $do['harga_paket']) ?></del><br><small>Disc. Rp<?= number_format($do['diskon']) ?></small><br>Rp<?= number_format($jumlah - $do['diskon'] + $do['harga_paket']) ?>
                                                        <?php } else { ?>
                                                            <?= number_format($jumlah + $do['harga_paket']) ?>
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
                                            $id_toko[$ref] = $do['id_sumber'];
                                            $id_penerima[$ref] = $do['cs_id'];

                                            if ($cancel_barang <> 2) {
                                                $bill += (($jumlah * $do['harga_jual']) + $do['harga_paket']);
                                                $bill -= ($do['diskon'] * $jumlah);
                                            }

                                            $jumlah_semula = "";
                                            if ($do['diskon'] > 0) {
                                                $jumlah_semula = "<s>" . number_format(($jumlah * $do['harga_jual']) + $do['harga_paket']) . "</s><br><small>Disc. " . number_format($do['diskon'] * $jumlah) . "</small><br>";
                                            }
                                            $jumlah_real = ($jumlah * $do['harga_jual']) + $do['harga_paket'] - ($do['diskon'] * $jumlah); ?>
                                            <tr style="<?= ($cancel_barang == 2) ? 'color:silver' : '' ?>">
                                                <td class="align-top">
                                                    <small><span class="badge bg-light text-dark"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                    <?= trim($dp['brand'] . " " . $dp['model']) ?><?= $dp['product_name'] ?>
                                                </td>
                                                <td class=""><small>
                                                        #<?= $do['sn'] ?>
                                                </td>
                                                <td class="text-end"><?= number_format($jumlah) ?></td>
                                                <td class="text-end">
                                                    <?= $jumlah_semula ?>
                                                    <?= number_format($jumlah_real) ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php }

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
                                        <td class="text-end border-0" nowrap colspan="4">
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

    <div class="row mx-0 mt-2">
        <?php if (count($data['r_kas']) > 0) { ?>
            <div class="col px-1 text-sm" style="min-width: 400px;">
                <div class=""><small><u>Riwayat Pembayaran</u></small></div>
                <table class="table table-sm text-sm">
                    <?php
                    foreach ($data['r_kas'] as $rk) {
                        $cl_tb = "";
                        switch ($rk['status_mutasi']) {
                            case 0:
                                $statusP = "<small class='text-warning'>Checking</small> ";
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
                                <?= date('d/m/y H:i', strtotime($rk['insertTime'])) ?>
                                <br><?= $metod ?>
                            </td>
                            <td style="width: 50px;">
                                Bayar<br>
                                Kembali</td>
                            <td style="width: 2px;">:<br>:</td>
                            <td class="text-end" style="width: 90px;">
                                <?= number_format($rk['bayar']) ?><br>
                                <?= number_format($rk['kembali']) ?></td>
                            <td class="text-end">
                                <?= number_format($rk['total']) ?><br>
                                <?= $statusP ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </div>
</div>