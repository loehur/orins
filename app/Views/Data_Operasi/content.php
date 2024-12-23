<main>
    <?php if ($data['mode'] == 0) { ?>
        <div class="row mx-2" style="max-width:600px">
            <div class="col px-1">
                <select class="border rounded tize" name="id_pelanggan" required>
                    <option></option>
                    <?php foreach ($data['pelanggan'] as $p) { ?>
                        <option value="<?= $p['id_pelanggan'] ?>" <?= ($data['parse'] == $p['id_pelanggan'] ? "selected" : "") ?>><?= strtoupper($p['nama']) ?> #<?= substr($p['id_pelanggan'], -2) ?></option>
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
    <?php } ?>

    <!-- Main page content-->
    <div class="row mt-1 mx-2 pb-2">
        <?php
        $id_pelanggan = $data['parse'];
        $id_pelanggan_jenis = 0;
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

                        $showMutasi .= "<small>" . $metod . "#" . $dk['id_kas'] . " " . $dk['note'] . " " . $statusP . $kembali_text . "</small> " . $jumlahShow;
                    }
                }

                $list_xdiskon = [];
                foreach ($data['diskon'] as $ds) {
                    if ($ds['ref_transaksi'] == $ref) {
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
                                                    <span class="text-danger"><?= substr($ref, -4) ?></span> <b><span class="text-success"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></b> #<?= substr($data['pelanggan'][$id_pelanggan]['id_pelanggan'], -2) ?>
                                                </td>
                                                <?php if ($dh['id_afiliasi'] == 0 || $dh['id_afiliasi'] <> $this->userData['id_toko']) { ?>
                                                    <td class="text-end text-purple"><small><?= $dh['user_id'] ?>#<b><?= strtoupper($cs) ?></b></span></small></td>
                                                <?php } else { ?>
                                                    <td class="text-end text-purple"><small><?= $dh['user_id'] ?>#<b><?= strtoupper($cs) ?>/<?= strtoupper($cs_to) ?></b></span></small></td>
                                                <?php }
                                                ?>
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
                                            $countSPK =  count($divisi_arr);
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
                                                                <?php
                                                                if ($cancel == 1) { ?>
                                                                    <span class="text-nowrap text-success"><small><del><?= $id . "# " . ucwords($produk) ?></del></small></span>
                                                                <?php } else { ?>
                                                                    <span class="text-nowrap text-success"><small><?= $id . "# " . ucwords($produk) ?></small></span>
                                                                <?php } ?>
                                                                <small><span class="badge bg-danger"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                                <?php if ($dibayar == 0 && $cancel == 0 && $do['id_afiliasi'] <> $this->userData['id_toko']) { ?>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="border-0 bg-white ps-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                                        </button>
                                                                        <ul class="dropdown-menu p-0">
                                                                            <li><a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="dropdown-item cancel" data-id="<?= $id ?>" href="#">Cancel</a></li>
                                                                        </ul>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($do['id_afiliasi'] <> 0 && $do['id_afiliasi'] <> $this->userData['id_toko']) {
                                                                    $toko_aff = $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $do['id_afiliasi']);
                                                                    if ($do['status_order'] == 1) { ?>
                                                                        <span class="badge text-danger"><?= $toko_aff ?> - Checking</span></span>
                                                                    <?php } else {
                                                                        $cs_aff = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $do['id_user_afiliasi']);
                                                                    ?>
                                                                        <span class="badge text-success"><i class="fa-solid fa-circle-check"></i> <?= $cs_aff ?> - <?= $toko_aff ?></span>
                                                                    <?php } ?>
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
                                                <td class="text-nowrap" style="line-height: 120%;"><small>
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
                                                                    <span class="btnAmbil" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal4" data-id="<?= $id ?>"><i class="fa-regular fa-circle"></i> Ambil</span>
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
                                                <td class="text-end"><?= number_format($do['jumlah']) ?></td>
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
                                                </td>
                                            </tr>
                                        <?php }
                                    }

                                    if (isset($data['mutasi'][$ref])) {
                                        foreach ($data['mutasi'][$ref] as $do) {
                                            $no += 1;
                                            $user_id = $do['user_id'];
                                            $jumlah = $do['qty'];
                                            $id_pelanggan_jenis = $do['jenis_target'];
                                            $dp = $data['barang'][$do['kode_barang']];
                                            $bill += (($jumlah * $do['harga_jual']) + $do['margin_paket']);
                                            $bill -= ($do['diskon'] * $jumlah);

                                            $jumlah_semula = ($jumlah * $do['harga_jual']) + $do['margin_paket'];
                                            if ($do['diskon'] > 0) {
                                                $jumlah_semula = "<s>" . number_format(($jumlah * $do['harga_jual']) + $do['margin_paket']) . "</s><br><small>Disc. " . number_format($do['diskon'] * $jumlah) . "</small><br>";
                                            }
                                            $jumlah_real = ($jumlah * $do['harga_jual']) + $do['margin_paket'] - ($do['diskon'] * $jumlah);

                                        ?>
                                            <tr>
                                                <td>
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


                                    if ($dibayar > 0 && $lunas == false) {
                                        $showMutasi .= "<span class='text-danger'><b>Sisa Rp" . number_format($sisa) . "</b></span>";
                                    }

                                    $print_mode = "A4";
                                    if ($no <= 6) {
                                        $print_mode = "&#189;";
                                    }

                                    ?>
                                    <tr class="border-top">
                                        <td class="text-end text border-0" colspan="3">
                                            <?php if (($dh['id_afiliasi'] == 0 || $dh['id_afiliasi'] <> $this->userData['id_toko']) && $dh['tuntas'] == 0) { ?>
                                                <table>
                                                    <tr>
                                                        <td class="text-end pe-1"><small><a href="<?= PV::BASE_URL; ?>Data_Order/print/<?= $ref ?>" target="_blank" class="btnBayar rounded border px-1 text-dark text-decoration-none"><i class="fa-solid fa-print"></i> <?= $print_mode ?></a></small></td>
                                                        <?php
                                                        if ($ambil_all == false) { ?>
                                                            <td class="text-end pe-1"><small><span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal3" class="btnAmbilSemua rounded border text-purple px-1" data-ref="<?= $do['ref'] ?>">Ambil</span></small></td>
                                                        <?php } ?>
                                                        <td class="text-end pe-1">
                                                            <button type="button" class="border-0 bg-white ps-1 dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <small>
                                                                    <span data-bs-toggle="modal" data-bs-target="#modalDiskon" data-bs-toggle="dropdown" class="border rounded text-info px-2 dropdown-toggle dropdown-toggle-split">
                                                                        <i class="fa-solid fa-sliders"></i>
                                                                    </span>
                                                                </small>
                                                                <span class="visually-hidden">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu p-0">
                                                                <?php if ($user_id == $this->userData['id_user'] && $do['tuntas'] == 0) { ?>
                                                                    <li><a class="dropdown-item" href="<?= PV::BASE_URL ?>Buka_Order/Edit_order/<?= $ref ?>/<?= $id_pelanggan_jenis ?>/<?= $dibayar ?>/<?= $id_pelanggan ?>"><small>Edit Order</small></a></li>
                                                                <?php } else { ?>
                                                                    <li><a class="dropdown-item" href="#"><small>CreatorID #<?= $user_id ?></small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $sisa > 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalDiskon" class="dropdown-item xtraDiskon" data-sisa="<?= $sisa ?>" data-ref="<?= $ref ?>" href="#"><small>Extra Diskon</small></a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                </table>
                                            <?php } else { ?>
                                                <table>
                                                    <tr>
                                                        <td class="text-end pe-1"><small><a href="<?= PV::BASE_URL; ?>Data_Order/print/<?= $ref ?>" target="_blank" class="btnBayar border btn btn-sm px-1"><i class="fa-solid fa-print"></i> <?= $print_mode ?></a></small></td>
                                                    </tr>
                                                </table>
                                            <?php } ?>
                                        </td>
                                        <td class="text-end border-0" nowrap><?= ($lunas == true) ? '<i class="fa-solid text-success fa-circle-check"></i>' : '' ?> <b>Rp<?= number_format($bill) ?></b></td>
                                    </tr>
                                    <?php if (strlen($showMutasi) > 0) { ?>
                                        <tr>
                                            <td class="text-end text border-0" colspan="4">
                                                <?= $showMutasi ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </small>
                    </div>
                </div>
                <?php
                if ($verify_payment >= $bill && $ambil_all == true) {
                    array_push($arr_tuntas, $ref);
                }
                ?>
            </div>
        <?php } ?>
    </div>

    <div class="row mx-0 px-2 mt-2">
        <?php if (isset($dh)) { ?>
            <?php if (($dh['id_afiliasi'] == 0 || $dh['id_afiliasi'] <> $this->userData['id_toko']) && $dh['tuntas'] == 0) { ?>
                <div class="col px-1 text-sm" id="loadMulti" style="max-width: 600px;">
                    <form action="<?= PV::BASE_URL; ?>Data_Operasi/bayar_multi" method="POST">
                        <div class="border px-0">
                            <small>
                                <table class="table table-sm mb-0 table-borderless text-sm">
                                    <tr class="table-info">
                                        <td colspan="4" class="p-2 text-center"><b>PEMBAYARAN MULTI</b></td>
                                    </tr>
                                    <tr>
                                        <td>Metode</td>
                                        <td class="pb-2">
                                            <select name="metode_multi" class="form-select metodeBayar_multi" required>
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
                                    <tr id="noteBayar_multi" class="border-top">
                                        <td class="pe-2 text-danger" nowrap>Catatan Transaksi</td>
                                        <td colspan="2" class="pb-2 pt-2">
                                            <input type="text" name="note_multi" class="form-control border border-danger">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="border-top">
                                        <td colspan="3" class="pb-1"></td>
                                    </tr>
                                    <?php
                                    $totalTagihan = 0;
                                    foreach ($loadRekap as $key => $value) { ?>
                                        <tr class='hoverBill'>
                                            <td><span class='text-dark'><?= $key ?></span></td>
                                            <td class="text-end align-middle"><input type='checkbox' class='cek_multi' name="ref_multi[]" value="<?= $key ?>_<?= $value ?>" data-jumlah='<?= $value ?>' data-ref='<?= $key ?>' checked></td>
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
                                        <td class="text-end">
                                            <span data-total=''><b>Rp<span id="totalBill" data-total="<?= $totalTagihan ?>"><?= number_format($totalTagihan) ?></span></b>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td></td>
                                        <td class="pt-2 pb-1"><span class="bayarPasMulti text-danger" style="cursor:pointer"><small>Bayar Pas (Click)</small></span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Bayar</td>
                                        <td class="pb-1"><input id="bayarBill" name="dibayar_multi" class="text-end form-control" type="number" min="1" value="" required /></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Kembalian</td>
                                        <td><input id='kembalianBill' name="kembalianBill" class="text-end form form-control" type="number" readonly /></td>
                                        <td class="text-end ps-2" nowrap>
                                            <button type="submit" id="btnBayarBill" class='btn btn-primary  '>Bayar</button>
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
            <div class="col px-1 text-sm">
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
        if (parse_2 == 0) {
            clearTuntas();
        }

        //MULTI
        totalBill = $("span#totalBill").attr("data-total");
        json_rekap = [<?= json_encode($loadRekap) ?>];

        totalBill = $("span#totalBill").attr("data-total");
        if (totalBill == 0) {
            $("div#loadMulti").fadeOut('fast');
        }
    });

    function clearTuntas() {
        var dataNya = '<?= serialize($arr_tuntas) ?>';
        var countArr = <?= count($arr_tuntas) ?>;

        if (countArr > 0) {
            $.ajax({
                url: '<?= PV::BASE_URL ?>Data_Operasi/clearTuntas',
                data: {
                    'data': dataNya,
                },
                type: 'POST',
                success: function() {
                    content();
                }
            });
        }
    }

    $('button.cek').click(function() {
        var parse = $("select[name=id_pelanggan]").val();
        var parse_2 = $("select[name=y]").val() || 0;
        location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + parse + "/" + parse_2;
    });

    $("a.xtraDiskon").click(function() {
        ref = $(this).attr("data-ref");
        max_diskon = $(this).attr("data-sisa");
        $("input[name=ref_diskon]").val(ref);
        $("input[name=max_diskon]").val(max_diskon);
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
    })

    $(".cancel_diskon").click(function() {
        id = $(this).attr("data-id");
        $("input[name=cancel_id_diskon]").val(id);
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

    $("select.metodeBayar").on("keyup change", function() {
        if ($(this).val() == 2 || $(this).val() == 3) {
            $("div#noteBayar").show();
        } else {
            $("div#noteBayar").hide();
            $("input[name=note").val("");
        }
    });

    $("select.metodeBayar_multi").on("keyup change", function() {
        if ($(this).val() == 2 || $(this).val() == 3) {
            $("tr#noteBayar_multi").show();
        } else {
            $("tr#noteBayar_multi").hide();
            $("input[name=note_multi").val("");
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
</script>