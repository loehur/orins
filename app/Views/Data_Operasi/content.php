<style>
    .filter-select-wrap {
        position: relative;
        max-width: 600px;
        min-height: 38px;
    }

    .filter-select-wrap.is-loading .filter-select-fields {
        opacity: 0;
        pointer-events: none;
    }

    .filter-select-mini-loader {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0 0.5rem;
        z-index: 2;
    }

    .filter-select-wrap.is-ready .filter-select-mini-loader {
        display: none;
    }

    .filter-select-wrap.is-ready .filter-select-fields {
        opacity: 1;
    }

    .filter-select-mini-loader .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }

    #loadMulti .multi-pay-row td {
        font-size: 1rem;
        font-weight: 700;
    }

    #loadMulti #bayarBill,
    #loadMulti #kembalianBill {
        font-size: 1.15rem;
        font-weight: 700;
    }

    .tukar-barang-cek-ok {
        border: 1px solid #198754;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.12) 0%, rgba(25, 135, 84, 0.04) 100%);
        padding: 0.75rem 0.9rem;
    }

    .tukar-barang-cek-ok .tukar-barang-nama {
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .tukar-barang-cek-fail {
        border: 1px solid #dc3545;
        border-radius: 0.5rem;
        background: rgba(220, 53, 69, 0.08);
        padding: 0.65rem 0.9rem;
        color: #842029;
        font-size: 0.9rem;
    }
</style>

<main>
    <div class="filter-select-wrap is-loading mx-2" id="filterSelectWrap">
        <div class="filter-select-mini-loader" aria-live="polite" aria-busy="true">
            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
            <small class="text-muted">Memuat pilihan...</small>
        </div>
        <div class="filter-select-fields row mx-0">
            <div class="col px-0">
                <select class="border rounded tize ajax-pelanggan" name="id_pelanggan" required>
                    <option></option>
                    <?php foreach ($data['pelanggan'] as $p) { ?>
                        <option value="<?= $p['id_pelanggan'] ?>" <?= ($data['parse'] == $p['id_pelanggan'] ? "selected" : "") ?>><?= $this->dToko[$p['id_toko']]['inisial'] ?> <?= strtoupper($p['nama']) ?> #<?= substr($p['id_pelanggan'], -2) ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php if ($data['parse_2'] <> 0) { ?>
                <div class="col pe-0" style="min-width: 90px; max-width: 100px">
                    <select class="border tize filter-year" name="y" required>
                        <?php
                        $yNow = date("Y");
                        for ($x = 2023; $x <= $yNow; $x++) { ?>
                            <option value="<?= $x ?>" <?= ($data['parse_2'] == $x) ? "selected" : "" ?>><?= $x ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
            <div class="col-auto pt-auto mt-auto pe-0">
                <button type="button" class="cek btn btn-primary">Cek Order</button>
            </div>
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
        $sdsRekap = [];
        $showBayarCard = false;
        $refFinanceCache = [];

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
                            if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { // hanya kasir
                                if ($data['ref'][$ref]['tuntas'] == 0) {
                                    $showSurcharge .= "<i class='fa-regular fa-circle-xmark cancel_charge' data-id='" . $ds['id'] . "' data-bs-toggle='modal' style='cursor:pointer' data-bs-target='#modalCancelCharge'></i> <span class='text-primary'><small>Surcharge#" . $ds['id'] . " " . $ds['note'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                                } else {
                                    $showSurcharge .= "<span class='text-primary'><small>Surcharge#" . $ds['id'] . " " . $ds['note'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                                }
                            } else {
                                $showSurcharge .= "<span class='text-primary'><small>Surcharge#" . $ds['id'] . " " . $ds['note'] . "</small> Rp" . number_format($ds['jumlah']) . "</span><br>";
                            }
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

                                            if ($do['id_afiliasi'] <> 0) {
                                                $id_afiliasi[$ref] = $do['id_afiliasi'];
                                                $id_user_afiliasi[$ref] = $do['id_user_afiliasi'];
                                                $readyAFF[$ref] = $do['ready_aff_cs'];
                                            }

                                            $cancel = $do['cancel'];
                                            $id_cancel = $do['id_cancel'];

                                            if ($cancel == 0 && $do['stok'] == 0) {
                                                $paket_qty_val = isset($do['paket_qty']) && $do['paket_qty'] > 0 ? $do['paket_qty'] : 1;
                                                $bill += $jumlah + ($do['harga_paket'] * $paket_qty_val);
                                            }

                                            $listDetail = unserialize($do['detail_harga']);
                                            $akum_diskon_unit = 0;
                                            if (is_array($listDetail)) {
                                                foreach ($listDetail as $ld_o) {
                                                    $disk = isset($ld_o['d']) ? $ld_o['d'] : 0;
                                                    $akum_diskon_unit += $disk;
                                                }
                                            }
                                            $total_diskon_row = $akum_diskon_unit * $do['jumlah'];
                                            
                                            // Override diskon column logic with calculated value
                                            $do['diskon'] = $total_diskon_row;
                                            // Hanya kurangi diskon dari bill jika item belum cancel (agar item cancel tidak mengganggu jumlah tagihan)
                                            if ($cancel == 0 && $do['stok'] == 0) {
                                                $bill -= $total_diskon_row;
                                            }

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

                                            <tr style="<?= ($cancel == 1) ? 'color:silver' : '' ?>">
                                                <td>
                                                    <table class="text-sm">
                                                        <?php
                                                        if ($cancel <> 0) {
                                                            $canceler = $this->dKaryawanAll[$id_cancel]['nama']; ?>
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

                                                                <div class="btn-group">
                                                                    <button type="button" class="border-0 bg-white ps-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <span class="visually-hidden">Toggle Dropdown</span>
                                                                    </button>
                                                                    <ul class="dropdown-menu p-0 border-0 shadow-sm text-sm">
                                                                        <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { // hanya kasir
                                                                            if ($do['tuntas'] == 0 && $cancel == 0 && $do['id_afiliasi'] <> $this->userData['id_toko']) { ?>
                                                                                <li><a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="dropdown-item px-2 cancel" data-id="<?= $id ?>" href="#">Cancel</a></li>
                                                                            <?php } ?>
                                                                        <?php } ?>
                                                                        <?php if ($do['tuntas'] == 1 && $do['refund'] == 0 && $cancel == 0 && $do['stok'] == 0 && in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                                                            <li><a data-bs-toggle="modal" data-bs-target="#modalRefund" class="dropdown-item px-2 refund" data-id="<?= $id ?>" href="#">Refund</a></li>
                                                                        <?php } ?>
                                                                        <?php if ($cancel == 0) { ?>
                                                                            <li><a data-bs-toggle="modal" data-bs-target="#modalTransfer" class="dropdown-item px-2 transfer" data-type="order" data-id="<?= $id ?>" href="#">Transfer</a></li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                                <?php if ($do['id_afiliasi'] <> 0 && $do['id_afiliasi'] <> $this->userData['id_toko']) {
                                                                    $toko_aff = $this->dToko[$do['id_afiliasi']]['inisial'];
                                                                    if ($do['status_order'] == 1) { ?>
                                                                        <span class="badge fw-normal text-danger"><?= $toko_aff ?> <i class="fa-solid fa-question"></i></span></span>
                                                                    <?php } else {
                                                                        $cs_aff = $this->dKaryawanAll[$do['id_user_afiliasi']]['nama']; ?>
                                                                        <span class="badge fw-normal text-purple">
                                                                            <span class="fw-bold"><?= $toko_aff ?></span>&nbsp;<i class="fa-solid fa-check"></i>&nbsp;<?= $cs_aff ?>
                                                                            <?php if (isset($data['karyawan'][$do['ready_aff_cs']])) { ?>
                                                                                &nbsp;<i class="fa-solid fa-check-double"></i>&nbsp;<?= $data['karyawan'][$do['ready_aff_cs']]['nama'] ?>
                                                                            <?php } ?>
                                                                        </span>
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
                                                            <div class="col ps-0">
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
                                                                    <small>
                                                                        <span data-id="<?= $id_order_data ?>" data-col="<?= $ks ?>" data-mode="<?= $ks ?>" class="cell_edit text-primary"><?= $ns ?></span>
                                                                    </small>
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

                                                        <?php $driver_name = $id_ambil_driver <> 0 ? "/" . ucwords($this->dKaryawanAll[$id_ambil_driver]['nama']) : ""; ?>

                                                        <?php if ($do['id_afiliasi'] == $this->userData['id_toko']) {
                                                            if ($id_ambil_aff == 0 && $cancel == 0) {
                                                                if ($countSPK > 0 && $cancel == 0) {
                                                                    $ambil_all_aff[$ref] = false; ?>
                                                                    <span class="btnAmbil" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal4" data-id="<?= $id ?>"><i class="fa-regular fa-circle"></i> Ambil</span><br>
                                                        <?php }
                                                            } else {
                                                                if ($cancel == 0) {
                                                                    $karyawan = $this->dKaryawanAll[$id_ambil_aff]["nama"];
                                                                    echo '<span class="text-dark"><i class="fa-solid fa-check"></i> Ambil (' . ucwords($karyawan) . $driver_name .  ")</span><br>";
                                                                }
                                                            }
                                                        } else {
                                                            if ($do['id_afiliasi'] <> 0 && $id_ambil_aff <> 0) {
                                                                if ($cancel == 0) {
                                                                    $karyawan = $this->dKaryawanAll[$id_ambil_aff]["nama"];
                                                                    echo '<span class="text-dark"><i class="fa-solid fa-check"></i> Ambil (' . ucwords($karyawan) . $driver_name . ")</span><br>";
                                                                }
                                                            }
                                                        } ?>

                                                        <?php if ($id_ambil == 0 && $cancel == 0) { ?>
                                                            <?php if ($countSPK > 0 && $cancel == 0) { ?>
                                                                <?php $ambil_all[$ref] = false; ?>
                                                                <?php if ($id_toko[$ref] == $this->userData['id_toko']) { ?>
                                                                    <span class="btnAmbil" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal4" data-id="<?= $id ?>"><i class="fa-regular fa-circle"></i> Ambil</span>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <?php if ($cancel == 0) { ?>
                                                                <?php $karyawan = $this->dKaryawanAll[$id_ambil]["nama"]; ?>
                                                                <span class="text-primary"><i class="fa-solid fa-check-double"></i> Ambil (<?= ucwords($karyawan) . $driver_name ?>)</span>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </small>
                                                </td>
                                                <td class="text-end">
                                                    <?= number_format($do['jumlah']) ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php
                                                    $paket_qty_val = isset($do['paket_qty']) && $do['paket_qty'] > 0 ? $do['paket_qty'] : 1;
                                                    if ($do['harga_paket'] == 0) {
                                                        if ($do['diskon'] > 0) { ?>
                                                            <del>Rp<?= number_format($jumlah) ?></del><br><small>Disc. Rp<?= number_format($do['diskon']) ?></small><br>Rp<?= number_format($jumlah - $do['diskon']) ?>
                                                        <?php } else { ?>
                                                            <?= number_format($jumlah) ?>
                                                        <?php }
                                                    } else {
                                                        if ($do['diskon'] > 0) { ?>
                                                            <del>Rp<?= number_format($jumlah + ($do['harga_paket'] * $paket_qty_val)) ?></del><br><small>Disc. Rp<?= number_format($do['diskon']) ?></small><br>Rp<?= number_format($jumlah - $do['diskon'] + ($do['harga_paket'] * $paket_qty_val)) ?>
                                                        <?php } else { ?>
                                                            <?= number_format($jumlah + ($do['harga_paket'] * $paket_qty_val)) ?>
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

                                            $paket_qty_val = isset($do['paket_qty']) && $do['paket_qty'] > 0 ? $do['paket_qty'] : 1;
                                            if ($cancel_barang <> 2) {
                                                $bill += (($jumlah * $do['harga_jual']) + ($do['harga_paket'] * $paket_qty_val));
                                                $bill -= ($do['diskon'] * $jumlah);
                                            }

                                            $jumlah_semula = "";
                                            if ($do['diskon'] > 0) {
                                                $jumlah_semula = "<s>" . number_format(($jumlah * $do['harga_jual']) + ($do['harga_paket'] * $paket_qty_val)) . "</s><br><small>Disc. " . number_format($do['diskon'] * $jumlah) . "</small><br>";
                                            }
                                            $jumlah_real = ($jumlah * $do['harga_jual']) + ($do['harga_paket'] * $paket_qty_val) - ($do['diskon'] * $jumlah); ?>
                                            <tr style="<?= ($cancel_barang == 2) ? 'color:silver' : '' ?>">
                                                <td class="align-top">
                                                    <small><span class="badge bg-light text-dark"><?= $do['paket_ref'] <> "" ? $data['paket'][$do['paket_ref']]['nama'] : "" ?></span></small>
                                                    <small><span class="badge bg-success"><?= $do['fp'] == 1 ? "FP" : "" ?></span></small>
                                                    <?= trim($dp['brand'] . " " . $dp['model']) ?><?= $dp['product_name'] ?>

                                                    <div class="btn-group">
                                                        <button type="button" class="border-0 bg-white ps-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu p-0 border-0 shadow-sm text-sm">
                                                            <li><a class="dropdown-item px-2 ajax" href="<?= PV::BASE_URL ?>Data_Operasi/faktur_pajak/<?= $do['id'] ?>/<?= $do['fp'] == 1 ? 0 : 1 ?>">Faktur Pajak (<?= $do['fp'] == 1 ? "-" : "+" ?>)</a></li>

                                                            <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2])) { ?>
                                                                <?php if ($do['stat'] == 1) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalTukarSN" class="dropdown-item tukarSN px-2" data-id="<?= $do['id'] ?>" data-id_barang="<?= $do['id_barang'] ?>" data-id_sumber="<?= $do['id_sumber'] ?>" data-sds="<?= (int)($do['sds'] ?? 0) ?>" data-has_sn="<?= (int)($dp['sn'] ?? 0) ?>" data-sn="<?= htmlspecialchars($do['sn'] ?? '', ENT_QUOTES) ?>" href="#">Tukar SN</a></li>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalTukarBarang" class="dropdown-item tukarBarang px-2" data-id="<?= $do['id'] ?>" data-id_sumber="<?= $do['id_sumber'] ?>" data-sds="<?= (int)($do['sds'] ?? 0) ?>" href="#">Tukar Barang</a></li>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="dropdown-item cancelBarang px-2" data-id="<?= $do['id'] ?>" href="#">Cancel (+)</a></li>
                                                                <?php } else { ?>
                                                                    <li><a class="dropdown-item px-2 ajax" href="<?= PV::BASE_URL ?>Data_Operasi/jadikan/<?= $do['id'] ?>">Cancel (-)</a></li>
                                                                <?php } ?>
                                                                <li><a data-bs-toggle="modal" data-bs-target="#modalTransfer" class="dropdown-item px-2 transfer" data-type="mutasi" data-id="<?= $do['id'] ?>" href="#">Transfer</a></li>
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
                                        <?php } ?>
                                    <?php }

                                    $sisa = $bill - $dibayar;

                                    if ($sisa <= 0 && $pending_bayar == false) {
                                        $lunas = true;
                                    }

                                    $headRef = $data['head'][$ref] ?? [];
                                    $refCanBayar = (($headRef['id_afiliasi'] ?? 0) == 0 || ($headRef['id_afiliasi'] ?? 0) != $this->userData['id_toko'])
                                        && (int)($headRef['tuntas'] ?? 0) === 0;
                                    if ($refCanBayar && $sisa > 0) {
                                        $refHasSds = false;
                                        $refHasToko = false;
                                        if (($charge[$ref] ?? 0) > 0) {
                                            $refHasToko = true;
                                        }
                                        if (isset($data['order'][$ref])) {
                                            foreach ($data['order'][$ref] as $doSds) {
                                                if ($doSds['cancel'] == 0 && $doSds['stok'] == 0) {
                                                    $refHasToko = true;
                                                }
                                            }
                                        }
                                        if (isset($data['mutasi'][$ref])) {
                                            foreach ($data['mutasi'][$ref] as $doSds) {
                                                if ($doSds['stat'] <> 2) {
                                                    if ((int)($doSds['sds'] ?? 0) === 1) {
                                                        $refHasSds = true;
                                                    } else {
                                                        $refHasToko = true;
                                                    }
                                                }
                                            }
                                        }
                                        $refSdsProfile = ($refHasSds && $refHasToko) ? 'MIX' : ($refHasSds ? 'SDS' : 'TOKO');

                                        $loadRekap[$id_pelanggan . "_" . $ref] = $sisa;
                                        $markRekap[$id_pelanggan . "_" . $ref] = $mark;
                                        $sdsRekap[$id_pelanggan . "_" . $ref] = $refSdsProfile;
                                        $showBayarCard = true;
                                    }

                                    if ($dibayar > 0 && $lunas == false && $sisa > 0) {
                                        $showMutasi .= "<span class='text-danger'><b>Sisa Rp" . number_format($sisa) . "</b></span>";
                                    }

                                    $fixKasId = 0;
                                    $fixKasJumlah = 0;
                                    if (isset($data['kas'][$ref])) {
                                        foreach ($data['kas'][$ref] as $dkFix) {
                                            if ($dkFix['status_mutasi'] == 0 || $dkFix['status_mutasi'] == 1) {
                                                if ((int)$dkFix['jumlah'] > $fixKasJumlah) {
                                                    $fixKasJumlah = (int)$dkFix['jumlah'];
                                                    $fixKasId = (int)$dkFix['id_kas'];
                                                }
                                            }
                                        }
                                    }

                                    $refFinanceCache[$ref] = [
                                        'bill' => (int)$bill,
                                        'dibayar' => (int)$dibayar,
                                        'sisa' => (int)$sisa,
                                        'fixKasId' => $fixKasId,
                                        'tuntas' => (int)($data['ref'][$ref]['tuntas'] ?? 0),
                                    ];

                                    $overpay = ($sisa < 0 && $fixKasId > 0 && (int)($data['ref'][$ref]['tuntas'] ?? 0) === 0);
                                    if ($overpay && in_array($this->userData['user_tipe'], PV::PRIV[2])) {
                                        $lebihBayar = abs($sisa);
                                        $showMutasi .= "<br><span class='badge bg-warning text-dark btnFixBayar' style='cursor:pointer' data-bs-toggle='modal' data-bs-target='#modalFixBayar' data-ref='" . $ref . "' data-id-kas='" . $fixKasId . "' data-bill='" . (int)$bill . "' data-dibayar='" . (int)$dibayar . "' data-lebih='" . $lebihBayar . "'><i class='fa-solid fa-wrench'></i> Fix Bayar (Lebih Rp" . number_format($lebihBayar) . ")</span>";
                                    }
                                    ?>

                                    <tr class="border-top">
                                        <td class="text-end text border-0 pb-0" colspan="3">
                                            <table>
                                                <tr>
                                                    <td class="text-end pe-1"><small><a href="#" class="btnPreviewOrder rounded border-0 px-1 text-dark text-decoration-none" data-ref="<?= $ref ?>" data-printed="<?= (int)($dRef['printed'] ?? 0) ?>" title="Preview Order<?= (int)($dRef['printed'] ?? 0) > 0 ? ' (x' . (int)$dRef['printed'] . ')' : '' ?>"><i class="fa-solid fa-eye"></i></a></small></td>
                                                    <?php if ($ambil_all[$ref] == false) { ?>
                                                        <?php if ($id_toko[$ref] == $this->userData['id_toko']) { ?>
                                                            <td class="text-end pe-1"><span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal3" class="btnAmbilSemua rounded badge text-primary px-0" data-ref="<?= $do['ref'] ?>">Ambil</span></td>
                                                        <?php } else { ?>
                                                            <?php if ($ambil_all_aff[$ref] == false) { ?>
                                                                <td class="text-end pe-1"><span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal3" class="btnAmbilSemua rounded badge text-primary px-0" data-ref="<?= $do['ref'] ?>">Ambil</span></td>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php if ($id_toko[$ref] == $this->userData['id_toko']) { ?>
                                                        <td class="text-end pe-1">
                                                            <button type="button" class="border-0 bg-white ps-0 dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <small>
                                                                    <i class="fa-solid fa-sliders"></i>
                                                                </small>
                                                                <span class="visually-hidden">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu p-0 border-0 shadow rounded-0">
                                                                <?php if ($do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalMark" class="dropdown-item markRef px-2" data-ref="<?= $ref ?>" href="#"><small>Mark</small></a></li>
                                                                <?php } ?>
                                                                <?php if ($user_id == $this->userData['id_user'] && $do['tuntas'] == 0) { ?>
                                                                    <li><a class="dropdown-item px-2" href="<?= PV::BASE_URL ?>Buka_Order/Edit_order/<?= $ref ?>/<?= $id_pelanggan_jenis ?>/<?= $dibayar ?>/<?= $id_pelanggan ?>"><small>Edit Order</small></a></li>
                                                                <?php } else { ?>
                                                                    <li><a class="dropdown-item px-2" href="#"><small>CreatorID #<?= $user_id ?></small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalCharge" class="dropdown-item tambahCharge px-2" data-ref="<?= $ref ?>" href="#"><small>Surcharge</small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $sisa > 0 && $do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalDiskon" class="dropdown-item xtraDiskon px-2" data-sisa="<?= $sisa ?>" data-ref="<?= $ref ?>" href="#"><small>Extra Diskon</small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#exampleModalRefundCash" class="dropdown-item refundCash px-2" data-client="<?= $id_pelanggan ?>" data-ref="<?= $ref ?>" href="#"><small>Refund</small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $ambil_all[$ref] == true && $do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#modalBatalAmbil" class="dropdown-item batalAmbil px-2" data-ref="<?= $ref ?>" href="#"><small>Batal Ambil</small></a></li>
                                                                <?php } ?>
                                                                <?php if (in_array($this->userData['user_tipe'], PV::PRIV[2]) && $do['tuntas'] == 0) { ?>
                                                                    <li><a data-bs-toggle="modal" data-bs-target="#modalUbahPelanggan" class="dropdown-item ubahPelanggan px-2" data-ref="<?= $ref ?>" data-pelanggan="<?= $id_pelanggan ?>" data-pelanggan-jenis="<?= (int)($data['pelanggan'][$id_pelanggan]['id_pelanggan_jenis'] ?? 0) ?>" href="#"><small>Ubah Pelanggan</small></a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </td>
                                                        <td class="text-sm pe-1">
                                                            <small><?= $dh['user_id'] ?>#</small>
                                                        </td>
                                                    <?php } ?>
                                                    <td class="text-sm align-middle" style="cursor: pointer;">
                                                        <small><span id="span_copy_<?= $ref ?>" class="text-success fw-bold" style="display: none;">Copied!</span></small>
                                                        <?php if ($id_toko[$ref] == $this->userData['id_toko']) { ?>
                                                            <?php if ($data['cust_wa']) { ?>
                                                                <span onclick="copy('<?= $data['cust_wa'] ?>', <?= $ref ?>)" class="text-success"><i class="fa-brands fa-whatsapp"></i></span>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        &nbsp;<span onclick="copy('<?= $ref ?>', <?= $ref ?>)" class="text-primary"><i class="fa-solid fa-receipt"></i></i></span>
                                                    </td>

                                                    <?php if ($ada_produksi[$ref] == true) { ?>
                                                        <td class="text-sm">
                                                            <?php if ($this->userData['id_toko'] == $id_toko[$ref]) { ?>
                                                                <?php if (isset($data['karyawan'][$readyAFF[$ref]])) { ?>
                                                                    &nbsp;<span class="text-sm"><i class="fa-solid fa-check-double"></i> <?= ucwords($data['karyawan'][$readyAFF[$ref]]['nama']) ?></span>
                                                                <?php } ?>
                                                                <?php if (isset($data['karyawan'][$data['ref'][$ref]['ready_cs']])) { ?>
                                                                    &nbsp;<span class="text-sm"><i class="fa-solid fa-check-double"></i> <?= ucwords($data['karyawan'][$data['ref'][$ref]['ready_cs']]['nama']) ?></span>
                                                                <?php } else { ?>
                                                                    &nbsp;<span class="btnReady text-sm fw-bold" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal11" data-cs="<?= $id_afiliasi[$ref] == $this->userData['id_toko'] ? $id_user_afiliasi[$ref] : $id_penerima[$ref] ?>" data-ref="<?= $ref ?>"> <small><i class="fa-solid fa-question"></i> Ready</small></span>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if ($id_afiliasi[$ref] == $this->userData['id_toko']) { ?>
                                                                    <?php if (isset($data['karyawan'][$readyAFF[$ref]])) { ?>
                                                                        &nbsp;<span class="text-sm"><i class="fa-solid fa-check-double"></i> <?= ucwords($data['karyawan'][$readyAFF[$ref]]['nama']) ?></span>
                                                                    <?php } else { ?>
                                                                        &nbsp;<span class="btnReady text-sm fw-bold" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal11" data-cs="<?= $id_afiliasi[$ref] == $this->userData['id_toko'] ? $id_user_afiliasi[$ref] : $id_penerima[$ref] ?>" data-ref="<?= $ref ?>"> <small><i class="fa-solid fa-question"></i> Ready</small></span>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            <?php if (isset($data['ea'][$data['ref'][$ref]['expedisi']])) { ?>
                                                                &nbsp;<span class="text-sm"><i class="fa-solid fa-truck-fast"></i> <?= ucwords($data['ea'][$data['ref'][$ref]['expedisi']]['name']) ?></span>
                                                            <?php } ?>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                            </table>
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
        <?php if ($showBayarCard && count($loadRekap) > 0) { ?>
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
                                                <input type='checkbox' class='cek_multi form-check-input' name="ref_multi[]" value="<?= $key ?>_<?= $value ?>" data-jumlah='<?= $value ?>' data-ref='<?= $key ?>' data-sds-profile='<?= $sdsRekap[$key] ?? 'TOKO' ?>' checked>
                                            </td>
                                            <td class='text-end ps-2'>Rp<?= number_format($value) ?></td>
                                        </tr>
                                    <?php
                                        $totalTagihan += $value;
                                    } ?>
                                    <tr>
                                        <td colspan="2" class="pb-2 pr-2" nowrap>
                                            <b>TOTAL TAGIHAN</b>
                                        </td>
                                        <td class="text-end align-middle">
                                            <span id="multiPayLokasi" class="badge bg-info d-none">SDS</span>
                                        </td>
                                        <td class="text-end">
                                            <span data-total=''><b>Rp<span id="totalBill" data-total="<?= $totalTagihan ?>"><?= number_format($totalTagihan) ?></span></b></span>
                                        </td>
                                    </tr>
                                    <tr class="multi-pay-row">
                                        <td>Jumlah Bayar</td>
                                        <td class="pb-2" colspan="3">
                                            <span class="bayarPasMulti text-danger" style="cursor:pointer"><small>Bayar Pas (Click)</small></span>
                                            <input id="bayarBill" name="dibayar_multi" class="text-end form-control money-input fw-bold" type="text" inputmode="numeric" value="" required />
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
                                                        <select name="payment_account" id="paymentAccountMulti" class="border border-success rounded tize">
                                                            <option value=""></option>
                                                            <?php foreach ($data['payment_account'] as $pa) { ?>
                                                                <option value="<?= $pa['id'] ?>" data-sds="<?= (int)($pa['sds'] ?? 0) ?>"><?= strtoupper($pa['payment_account']) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="multi-pay-row">
                                        <td>Kembalian</td>
                                        <td colspan="2"><input id="kembalianBill" name="kembalianBill" class="text-end form form-control money-display fw-bold" type="text" readonly /></td>
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
                                <?= date('d/m/y H:i', strtotime($rk['insertTime'])) ?>
                                <br><?= $metod ?>
                            </td>
                            <td class="text-end">
                                Bayar<br>
                                Kembali</td>
                            <td class="text-end">
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
</main>


<?php require_once('form.php') ?>

<script>
    var refFinanceCache = <?= json_encode($refFinanceCache ?? []) ?>;
    var paymentAccountsMulti = <?= json_encode(array_values(array_map(function ($pa) {
        return [
            'id' => (string)$pa['id'],
            'name' => strtoupper($pa['payment_account']),
            'sds' => (int)($pa['sds'] ?? 0),
        ];
    }, $data['payment_account'] ?? []))) ?>;
</script>

<span data-custom-loader="true" class="d-none"></span>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
<script>
    function showToast(message, type) {
        type = type || 'danger';
        var container = document.querySelector('.toast-container');
        if (!container) return;
        var bgClass = type === 'danger' ? 'bg-danger text-white' : type === 'success' ? 'bg-success text-white' : type === 'warning' ? 'bg-warning text-dark' : 'bg-info text-white';
        var icon = type === 'danger' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
        var bodyHtml = String(message).replace(/\n/g, '<br>');
        var toastDelay = bodyHtml.length > 100 ? 9000 : 4500;
        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center border-0 shadow ' + bgClass;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = '<div class="d-flex">' +
            '<div class="toast-body d-flex align-items-start">' +
            '<i class="fas ' + icon + ' me-2 fs-5 flex-shrink-0 mt-1"></i>' +
            '<span class="text-sm">' + bodyHtml + '</span>' +
            '</div>' +
            '<button type="button" class="btn-close ' + (type === 'warning' ? '' : 'btn-close-white') + ' me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div>';
        container.appendChild(toastEl);
        var toast = new bootstrap.Toast(toastEl, { delay: toastDelay });
        toastEl.addEventListener('hidden.bs.toast', function() { toastEl.remove(); });
        toast.show();
    }

    var totalBill = 0;
    var json_rekap = [];
    var printOrderBaseUrl = '<?= PV::BASE_URL ?>Data_Order/print/';

    function openPrintPreview(ref) {
        window.open(printOrderBaseUrl + ref + '?preview=1', '_blank');
    }

    function parseMoneyNum(str) {
        return parseInt(String(str).replace(/\D/g, ''), 10) || 0;
    }

    function formatMoneyNum(num) {
        var n = parseInt(num, 10) || 0;
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function setMoneyVal($el, num) {
        $el.val(formatMoneyNum(num));
    }

    function updateMultiPayLokasi() {
        var profiles = [];
        $("input.cek_multi:checked").each(function() {
            profiles.push($(this).attr("data-sds-profile") || "TOKO");
        });

        var mode = "TOKO";
        if (profiles.length === 0) {
            mode = "TOKO";
        } else if (profiles.indexOf("MIX") >= 0) {
            mode = "MIX";
        } else {
            var hasSds = profiles.indexOf("SDS") >= 0;
            var hasToko = profiles.indexOf("TOKO") >= 0;
            if (hasSds && hasToko) {
                mode = "MIX";
            } else if (hasSds) {
                mode = "SDS";
            } else {
                mode = "TOKO";
            }
        }

        var $badge = $("#multiPayLokasi");
        if (mode === "TOKO") {
            $badge.addClass("d-none");
        } else {
            $badge.removeClass("d-none");
            $badge.text(mode);
            $badge.removeClass("bg-secondary bg-info bg-warning text-dark");
            if (mode === "SDS") {
                $badge.addClass("bg-info");
            } else if (mode === "MIX") {
                $badge.addClass("bg-warning text-dark");
            }
        }

        refreshPaymentAccountOptions(mode);
    }

    function refreshPaymentAccountOptions(mode) {
        var $sel = $("#paymentAccountMulti");
        if (!$sel.length || !paymentAccountsMulti || !paymentAccountsMulti.length) {
            return;
        }

        var currentVal = "";
        if ($sel[0].selectize) {
            currentVal = $sel[0].selectize.getValue();
        } else {
            currentVal = $sel.val() || "";
        }

        var filtered = paymentAccountsMulti.filter(function(pa) {
            if (mode === "MIX") {
                return true;
            }
            if (mode === "SDS") {
                return pa.sds === 1;
            }
            return pa.sds === 0;
        });

        $sel.empty().append('<option value=""></option>');
        filtered.forEach(function(pa) {
            $sel.append(
                $('<option></option>')
                    .val(pa.id)
                    .attr("data-sds", pa.sds)
                    .text(pa.name)
            );
        });

        if ($sel[0].selectize) {
            var selize = $sel[0].selectize;
            selize.clearOptions();
            selize.addOption({ value: "", text: "" });
            filtered.forEach(function(pa) {
                selize.addOption({ value: pa.id, text: pa.name });
            });
            selize.refreshOptions(false);
            if (currentVal && selize.options[currentVal]) {
                selize.setValue(currentVal, true);
            } else {
                selize.clear(true);
            }
        } else if (currentVal && !filtered.some(function(pa) { return pa.id === currentVal; })) {
            $sel.val("");
        }
    }

    function updateTotalFromCheckboxes() {
        var sum = 0;
        $("input.cek_multi:checked").each(function() {
            sum += parseInt($(this).attr("data-jumlah")) || 0;
        });
        totalBill = sum;
        $("span#totalBill").html(sum.toLocaleString('en-US')).attr("data-total", sum);
        updateMultiPayLokasi();
        bayarBill();
    }

    function markFilterSelectReady() {
        var wrap = document.getElementById('filterSelectWrap');
        if (wrap) {
            wrap.classList.remove('is-loading');
            wrap.classList.add('is-ready');
            wrap.querySelector('.filter-select-mini-loader')?.setAttribute('aria-busy', 'false');
        }
        if (typeof hideContentLoader === 'function') {
            hideContentLoader();
        }
    }

    function pelangganSelectizeOptions() {
        return {
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'no_hp', 'id'],
            create: false,
            render: {
                option: function(item, escape) {
                    return '<div style="padding: 6px 15px;">' +
                        '<span>' + escape(item.inisial || '') + ' ' + escape(item.nama) + '</span>' +
                        ' #<small>' + (item.id ? escape(String(item.id)).substring(String(item.id).length - 2) : '') + '</small>' +
                        ' <br><small>' + escape(item.no_hp || '') + '</small>' +
                        '</div>';
                },
                item: function(item, escape) {
                    return '<div style="padding: 2px 10px;">' + escape(item.inisial || '') + ' ' + escape(item.nama) + '</div>';
                }
            }
        };
    }

    function initSelectizeOnce($el, options) {
        if (!$el.length || $el[0].selectize) {
            return;
        }
        $el.selectize(options || {});
    }

    function initFilterSelectize() {
        initSelectizeOnce($('select.filter-year'));

        var pelangganOpts = pelangganSelectizeOptions();
        pelangganOpts.options = <?= $data['pelanggan_init'] ?>;
        pelangganOpts.load = function(query, callback) {
            if (query.length < 2) {
                return callback();
            }
            $.ajax({
                url: '<?= PV::BASE_URL ?>Data_Operasi/search_pelanggan',
                type: 'GET',
                dataType: 'json',
                data: { q: query },
                error: function() { callback(); },
                success: function(res) { callback(res); }
            });
        };
        initSelectizeOnce($('select.ajax-pelanggan'), pelangganOpts);
        markFilterSelectReady();
    }

    function initModalSelectize() {
        $('select.tize:not(.ajax-pelanggan):not(.ajax-pelanggan-ubah):not(#paymentAccountMulti)').each(function() {
            initSelectizeOnce($(this));
        });

        $('select.ajax-pelanggan-ubah').each(function() {
            var $sel = $(this);
            if ($sel[0].selectize) {
                return;
            }
            var ubahOpts = pelangganSelectizeOptions();
            ubahOpts.options = JSON.parse($sel.attr('data-options') || '[]');
            ubahOpts.load = function(query, callback) {
                if (query.length < 2) {
                    return callback();
                }
                var pelangganJenis = $sel.attr('data-pelanggan-jenis') || '';
                $.ajax({
                    url: '<?= PV::BASE_URL ?>Data_Operasi/search_pelanggan_ubah',
                    type: 'GET',
                    dataType: 'json',
                    data: { q: query, id_pelanggan_jenis: pelangganJenis },
                    error: function() { callback(); },
                    success: function(res) { callback(res); }
                });
            };
            $sel.selectize(ubahOpts);
        });
    }

    $(document).ready(function() {
        json_rekap = <?= json_encode($loadRekap) ?>;
        updateTotalFromCheckboxes();

        if (Object.keys(json_rekap || {}).length === 0) {
            $("div#loadMulti").hide();
        }

        initFilterSelectize();

        var deferModalSelect = window.requestIdleCallback || function(cb) {
            setTimeout(cb, 1);
        };
        deferModalSelect(function() {
            initModalSelectize();
            if ($("#paymentAccountMulti").length) {
                initSelectizeOnce($("#paymentAccountMulti"));
                updateMultiPayLokasi();
            }
        });
    });

    $(document).on("click", "a.ajax", function(e) {
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
                    showToast(res, 'danger');
                }
            }
        });
    });

    $(document).on("click", "span.btnReady", function() {
        var ref = $(this).attr("data-ref");
        var cs = $(this).attr("data-cs");
        $("input#ref").val(ref);
    });

    $(document).on("click", 'button.cek', function() {
        var parse = getSelectizeVal('select[name=id_pelanggan]');
        var parse_2 = getSelectizeVal('select[name=y]') || 0;
        if (!isValidPelangganId(parse)) {
            alert('Pilih pelanggan terlebih dahulu');
            var el = $('select[name=id_pelanggan]')[0];
            if (el && el.selectize) {
                el.selectize.focus();
            }
            return;
        }
        location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + parse + "/" + parse_2;
    });

    $(document).on("click", "a.xtraDiskon", function() {
        var ref = $(this).attr("data-ref");
        var max_diskon = $(this).attr("data-sisa");
        $("input[name=ref_diskon]").val(ref);
        $("input[name=max_diskon]").val(max_diskon);
    });

    $(document).on("click", "a.refundCash", function() {
        var ref = $(this).attr("data-ref");
        var client = $(this).attr("data-client");
        $("input[name=ref_refund]").val(ref);
        $("input[name=id_client]").val(client);
    });

    $(document).on("click", "a.tambahCharge", function() {
        var ref = $(this).attr("data-ref");
        $("input[name=ref_charge]").val(ref);
    });

    $(document).on("click", "a.markRef", function() {
        var ref_ = $(this).attr("data-ref");
        $("input[name=ref_mark]").val(ref_);
    });

    $(document).on("click", "span.btnBayar", function() {
        var bill_val = $(this).attr("data-bill");
        var client_val = $(this).attr("data-client");
        $("input.bill").val(bill_val);
        var ref = $(this).attr("data-ref");
        $("input#refBayar").val(ref);
        $("input#client").val(client_val);
    });

    $(document).on("click", "span.bayarPas", function() {
        var bill_input = parseMoneyNum($("input[name=bill]").val());
        setMoneyVal($("input.dibayar"), bill_input);
        kembalian();
    });

    $(document).on("input", ".money-input", function() {
        var raw = $(this).val().replace(/\D/g, '');
        $(this).val(raw === '' ? '' : formatMoneyNum(raw));
        if ($(this).is('#bayarBill')) {
            bayarBill();
        } else if ($(this).is('.dibayar')) {
            kembalian();
        }
    });

    $(document).on("click", "span.btnAmbil", function() {
        var id = $(this).attr("data-id");
        $("input[name=ambil_id]").val(id);
    });

    $(document).on("click", "a.cancel", function() {
        var id = $(this).attr("data-id");
        $("input[name=cancel_id]").val(id);
        $("input[name=tb]").val(0);
    });

    $(document).on("click", "a.cancelBarang", function() {
        var id = $(this).attr("data-id");
        $("input[name=cancel_id]").val(id);
        $("input[name=tb]").val(1);
    });

    var tukarSnList = [];
    var tukarSnSds = 0;
    var tukarSnCurrent = '';

    function setTukarSnMode(useSelect) {
        if (useSelect) {
            $("#tukarSN_input").hide().prop("disabled", true).val("");
            $("#tukarSN_select").show().prop("disabled", false).prop("required", true);
        } else {
            $("#tukarSN_select").hide().prop("disabled", true).prop("required", false).val("");
            $("#tukarSN_input").show().prop("disabled", false).prop("required", true);
            tukarSnList = [];
        }
    }

    function renderTukarSnOptions() {
        var $sel = $("#tukarSN_select").empty().append('<option value="">Pilih SN</option>');
        var count = 0;
        $.each(tukarSnList, function(i, item) {
            if (String(item.sds) === String(tukarSnSds) && item.sn !== tukarSnCurrent) {
                $sel.append('<option value="' + item.sn + '">' + item.sn + '</option>');
                count++;
            }
        });
        if (count === 0) {
            $sel.append('<option value="" disabled>SN tidak tersedia</option>');
        }
    }

    function loadTukarSnList(id_barang, id_sumber) {
        $("#tukarSN_select").empty().append('<option value="">Memuat...</option>');
        $.getJSON('<?= PV::BASE_URL ?>Data_Operasi/stok_sn/' + id_barang + '/' + id_sumber, function(data) {
            tukarSnList = data || [];
            renderTukarSnOptions();
        });
    }

    $(document).on("click", "a.tukarSN", function() {
        var id = $(this).attr("data-id");
        var id_barang = $(this).attr("data-id_barang");
        var id_sumber = $(this).attr("data-id_sumber");
        var has_sn = $(this).attr("data-has_sn");
        tukarSnSds = $(this).attr("data-sds") || "0";
        tukarSnCurrent = $(this).attr("data-sn") || "";
        $("input[name=tukarSN_id]").val(id);
        $("#exampleModalTukarSN input[name=reason]").val("");
        if (has_sn == "1") {
            setTukarSnMode(true);
            loadTukarSnList(id_barang, id_sumber);
        } else {
            setTukarSnMode(false);
        }
    });

    var tukarBarangVerifiedKey = '';
    var tukarBarangPending = null;

    function $tukarBarangModal($from) {
        if ($from && $from.length) {
            var $scoped = $from.closest('#exampleModalTukarBarang');
            if ($scoped.length) {
                return $scoped;
            }
        }
        var $shown = $('#exampleModalTukarBarang.show');
        if ($shown.length) {
            return $shown.last();
        }
        return $('#exampleModalTukarBarang').last();
    }

    function resetTukarBarangCek($modal) {
        $modal = $modal || $tukarBarangModal();
        tukarBarangVerifiedKey = '';
        $modal.find('.tukar-barang-cek-result').addClass('d-none').empty();
        $modal.find('#btnTukarBarang').prop('disabled', true);
    }

    function tukarBarangFormKey($modal) {
        $modal = $modal || $tukarBarangModal();
        return [
            $modal.find('#tukarBarang_id_baru').val(),
            $modal.find('#tukarBarang_sn').val(),
            $modal.find('#tukarBarang_sds').val(),
            $modal.find('input[name=tukarBarang_id]').val()
        ].join('|');
    }

    function renderTukarBarangCekOk(data) {
        var lokasiBadge = data.lokasi === 'SDS'
            ? '<span class="badge bg-danger">SDS</span>'
            : '<span class="badge bg-primary">TOKO</span>';
        return '<div class="tukar-barang-cek-ok">' +
            '<div class="d-flex align-items-center mb-2">' +
            '<i class="fas fa-check-circle text-success fs-5 me-2"></i>' +
            '<span class="fw-bold text-success">Barang Tersedia</span>' +
            '</div>' +
            '<div class="tukar-barang-nama text-dark mb-1">' + $('<div>').text(data.nama).html() + '</div>' +
            '<div class="small text-secondary mb-2">SN: <span class="fw-semibold text-dark">' + $('<div>').text(data.sn).html() + '</span></div>' +
            '<div class="d-flex align-items-center gap-2 flex-wrap">' +
            lokasiBadge +
            '<small class="text-muted">Stok tersedia: <strong>' + data.qty + '</strong></small>' +
            '</div></div>';
    }

    function renderTukarBarangCekFail(message) {
        return '<div class="tukar-barang-cek-fail">' +
            '<i class="fas fa-times-circle me-1"></i>' + $('<div>').text(message).html() +
            '</div>';
    }

    $(document).on("click", "a.tukarBarang", function() {
        tukarBarangPending = {
            id: $(this).attr("data-id"),
            sds: $(this).attr("data-sds") || "0",
            id_sumber: $(this).attr("data-id_sumber") || "0"
        };
    });

    $(document).on("shown.bs.modal", "#exampleModalTukarBarang", function() {
        var $modal = $(this);
        if (tukarBarangPending) {
            $modal.find("input[name=tukarBarang_id]").val(tukarBarangPending.id);
            $modal.find("#tukarBarang_id_sumber").val(tukarBarangPending.id_sumber);
            $modal.find("#tukarBarang_sds").val(tukarBarangPending.sds);
            $modal.find("#tukarBarang_id_baru").val('');
            $modal.find("#tukarBarang_sn").val('');
            $modal.find("#tukarBarang_reason").val('');
            tukarBarangPending = null;
        }
        resetTukarBarangCek($modal);
    });

    $(document).on("input change", "#exampleModalTukarBarang #tukarBarang_id_baru, #exampleModalTukarBarang #tukarBarang_sn, #exampleModalTukarBarang #tukarBarang_sds", function() {
        var $modal = $tukarBarangModal($(this));
        if (tukarBarangVerifiedKey && tukarBarangVerifiedKey !== tukarBarangFormKey($modal)) {
            resetTukarBarangCek($modal);
        }
    });

    $(document).on("click", "#exampleModalTukarBarang #btnCekBarangTukar", function() {
        var $modal = $tukarBarangModal($(this));
        var $btn = $(this).prop('disabled', true);
        var $result = $modal.find('.tukar-barang-cek-result');
        tukarBarangVerifiedKey = '';
        $modal.find('#btnTukarBarang').prop('disabled', true);
        $result.removeClass('d-none').html('<div class="text-muted small py-2"><span class="spinner-border spinner-border-sm me-1"></span>Memeriksa ketersediaan...</div>');

        $.ajax({
            url: '<?= PV::BASE_URL ?>Data_Operasi/cek_barang_tukar',
            type: 'POST',
            dataType: 'json',
            data: {
                tukarBarang_id: $modal.find('input[name=tukarBarang_id]').val(),
                id_baru: $modal.find('#tukarBarang_id_baru').val(),
                sn_baru: $modal.find('#tukarBarang_sn').val(),
                sds_baru: $modal.find('#tukarBarang_sds').val()
            },
            success: function(res) {
                if (res && res.ok) {
                    $result.html(renderTukarBarangCekOk(res));
                    tukarBarangVerifiedKey = tukarBarangFormKey($modal);
                    $modal.find('#btnTukarBarang').prop('disabled', false);
                } else {
                    $result.html(renderTukarBarangCekFail((res && res.message) ? res.message : 'Stok barang tidak tersedia'));
                }
            },
            error: function() {
                $result.html(renderTukarBarangCekFail('Gagal memeriksa ketersediaan. Coba lagi.'));
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on("submit", "#exampleModalTukarBarang form", function(e) {
        var $modal = $tukarBarangModal($(this));
        if (!$modal.find('#btnTukarBarang').prop('disabled') && tukarBarangVerifiedKey === tukarBarangFormKey($modal)) {
            return;
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        showToast('Cek ketersediaan barang terlebih dahulu.', 'warning');
        return false;
    });

    $(document).on("click", "a.refund", function() {
        var id = $(this).attr("data-id");
        $("input[name=refund_id]").val(id);
    });

    $(document).on("click", ".cancel_diskon", function() {
        var id = $(this).attr("data-id");
        $("input[name=cancel_id_diskon]").val(id);
    });

    $(document).on("click", ".cancel_charge", function() {
        var id = $(this).attr("data-id");
        $("input[name=cancel_id_charge]").val(id);
    });

    $(document).on("click", "span.btnAmbilSemua", function() {
        var ref = $(this).attr("data-ref");
        $("input[name=ambil_ref]").val(ref);
    });

    $(document).on("click", "a.batalAmbil", function() {
        var ref = $(this).attr("data-ref");
        $("input[name=batal_ambil_ref]").val(ref);
    });

    $(document).on('click', 'a.btnPreviewOrder', function(e) {
        e.preventDefault();
        openPrintPreview($(this).data('ref'));
    });

    $(document).on("click", "a.ubahPelanggan", function() {
        var ref = $(this).attr("data-ref");
        var pelanggan = $(this).attr("data-pelanggan");
        var pelangganJenis = $(this).attr("data-pelanggan-jenis") || "";
        $("input[name=ubah_ref]").val(ref);
        $("input[name=pelanggan_lama]").val(pelanggan);
        $("select.ajax-pelanggan-ubah").attr("data-pelanggan-jenis", pelangganJenis);
        var $sel = $("select.ajax-pelanggan-ubah");
        if ($sel[0] && $sel[0].selectize) {
            $sel[0].selectize.clear();
        }
    });

    $(document).on("click", "td#clearCheck", function() {
        $("input.cek_multi").prop('checked', false);
        updateTotalFromCheckboxes();
    });

    $(document).on('click', 'span.btnFixBayar', function() {
        var $btn = $(this);
        var ref = $btn.data('ref');
        var idKas = $btn.data('id-kas');
        var bill = parseInt($btn.data('bill'), 10) || 0;
        var dibayar = parseInt($btn.data('dibayar'), 10) || 0;
        var lebih = parseInt($btn.data('lebih'), 10) || 0;

        $('#fixBayarRef').val(ref);
        $('#fixBayarKasId').val(idKas);
        $('#fixBayarLebih').val(lebih);
        $('#fixBayarInfo').html('Ref <b>' + ref + '</b> — Tagihan <b>Rp' + bill.toLocaleString('en-US') + '</b>, Terbayar <b>Rp' + dibayar.toLocaleString('en-US') + '</b>, Kelebihan <b class="text-danger">Rp' + lebih.toLocaleString('en-US') + '</b>');

        var html = '';
        $.each(refFinanceCache, function(targetRef, fin) {
            if (targetRef === ref) return;
            if (!fin || fin.tuntas !== 0 || fin.sisa <= 0) return;
            html += '<div class="form-check">' +
                '<input class="form-check-input fix-bayar-target" type="checkbox" value="' + targetRef + '" id="fixTarget_' + targetRef + '">' +
                '<label class="form-check-label" for="fixTarget_' + targetRef + '">' +
                'Ref ' + targetRef.substr(-4) + ' — Sisa <b>Rp' + parseInt(fin.sisa, 10).toLocaleString('en-US') + '</b>' +
                '</label></div>';
        });
        if (!html) {
            html = '<span class="text-muted">Tidak ada tagihan lain yang belum lunas.</span>';
        }
        $('#fixBayarTargetList').html(html);
        $('#fixBayarModeSplit').prop('checked', true);
    });

    $(document).on('click', '#btnConfirmFixBayar', function() {
        var mode = $('input[name=fix_bayar_mode]:checked').val();
        var ref = $('#fixBayarRef').val();
        var idKas = $('#fixBayarKasId').val();
        var lebih = parseInt($('#fixBayarLebih').val(), 10) || 0;
        var url = mode === 'split'
            ? '<?= PV::BASE_URL ?>Data_Operasi/fix_bayar_split'
            : '<?= PV::BASE_URL ?>Data_Operasi/fix_bayar_adjust';
        var postData = {
            source_ref: ref,
            id_kas: idKas
        };

        if (mode === 'split') {
            var targets = [];
            $('input.fix-bayar-target:checked').each(function() {
                targets.push($(this).val());
            });
            if (targets.length === 0) {
                showToast('Pilih minimal satu ref tujuan pembayaran', 'warning');
                return;
            }
            postData.target_refs = targets;
        }

        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: postData,
            success: function(res) {
                $btn.prop('disabled', false);
                if (res == 0) {
                    var modalEl = document.getElementById('modalFixBayar');
                    if (modalEl) {
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }
                    showToast('Pembayaran berhasil diperbaiki', 'success');
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    showToast(res, 'danger');
                }
            },
            error: function() {
                $btn.prop('disabled', false);
                showToast('Gagal memproses perbaikan pembayaran', 'danger');
            }
        });
    });

    $(document).on("click", "span.bayarPasMulti", function() {
        setMoneyVal($("input#bayarBill"), totalBill);
        bayarBill();
    });

    $(document).on("change", "input.cek_multi", function() {
        var jumlah = parseInt($(this).attr("data-jumlah")) || 0;
        var refRekap = $(this).attr("data-ref");

        if ($(this).is(':checked')) {
            json_rekap[refRekap] = jumlah;
        } else {
            delete json_rekap[refRekap];
        }

        updateTotalFromCheckboxes();
    });

    $(document).on("keyup change", "input[name=charge]", function() {
        total_aftercas();
    });

    $(document).on("keyup change", "select.metodeBayar_multi", function() {
        if ($(this).val() == 1 || $(this).val() == 2 || $(this).val() == 3) {
            $("tr#noteBayar_multi").show();
        } else {
            $("tr#noteBayar_multi").hide();
        }

        if ($(this).val() == 2) {
            $("tr#payment_account").show();
            updateMultiPayLokasi();
        } else {
            $("input[name=charge]").val("");
            total_aftercas();
            $("tr#payment_account").hide();
        }
    });

    $(document).on("submit", "form", function(e) {
        if ($(this).closest('#modalTransfer').length) return;
        if ($(this).attr('action') && $(this).attr('action').indexOf('ubahPelanggan') >= 0) return;

        e.preventDefault();
        var $form = $(this);
        var moneyBackup = [];
        $form.find('.money-input, .money-display').each(function() {
            moneyBackup.push({ el: this, val: $(this).val() });
            var raw = parseMoneyNum($(this).val());
            $(this).val($(this).hasClass('money-input') && raw === 0 && $(this).val() === '' ? '' : raw);
        });

        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: $form.attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    var toastType = String(res).indexOf('input ganda') >= 0 ? 'warning' : 'danger';
                    showToast(res, toastType);
                }
            },
            complete: function() {
                moneyBackup.forEach(function(item) {
                    $(item.el).val(item.val);
                });
            }
        });
    });

    function kembalian() {
        var dibayar = parseMoneyNum($("input.dibayar").val());
        var bill_val = parseMoneyNum($("input.bill").val());
        var kembalianVal = dibayar - bill_val;
        if (kembalianVal < 0) {
            kembalianVal = 0;
        }
        setMoneyVal($("input.kembalian"), kembalianVal);
    }

    function bayarBill() {
        var dibayar = parseMoneyNum($('input#bayarBill').val());
        var kembalianVal = dibayar - parseInt(totalBill, 10);
        setMoneyVal($('input#kembalianBill'), kembalianVal > 0 ? kembalianVal : 0);
        total_aftercas();
    }

    function total_aftercas() {
        var dibayar = parseMoneyNum($('input#bayarBill').val());
        var charge = $("input[name=charge]").val() || 0;
        $("input#total_aftercas").val(parseInt(dibayar) + (parseInt(dibayar) * (parseFloat(charge) / 100)));
        $("input#total_charge").val((parseInt(dibayar) * (parseFloat(charge) / 100)));
    }

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
<div class="modal fade" id="modalTransfer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Transfer Item</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>Data_Operasi/transfer_item" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="item_type" value="">
                    <input type="hidden" name="item_id" value="">
                    <div class="mb-2">
                        <label class="form-label">Ref Tujuan</label>
                        <select name="dest_ref" class="form-select form-select-sm">
                            <?php foreach ($data['refs'] as $r) {
                                if ($data['head'][$r]['tuntas'] == 0) { ?>
                                    <option value="<?= $r ?>"><?= $r ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        var t = e.target;
        if (t.classList.contains('transfer')) {
            var mt = t.getAttribute('data-type');
            var id = t.getAttribute('data-id');
            document.querySelector('#modalTransfer input[name=item_type]').value = mt;
            document.querySelector('#modalTransfer input[name=item_id]').value = id;
        }
    });
</script>