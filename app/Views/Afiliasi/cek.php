<main>
    <!-- Main page content-->
    <div class="row me-2 ps-4 pb-0 mb-0 mt-2">
        <div class="col ps-0 pe-2 pb-0">
            <?php foreach ($data['order'] as $do) {
                $bill = 0;
                $total = 0;
                $ambil = false;
                $ambil_all = true;

                $tuntas = true;
                $lunas = false;
                $pending_bayar = false;
                $ref = $do['ref'];
                $dibayar = 0;
                $showMutasi = "";

                foreach ($data['kas'] as $dk) {
                    if ($dk['ref_transaksi'] == $ref) {
                        if ($dk['status_mutasi'] == 0 || $dk['status_mutasi'] == 1) {
                            $dibayar += $dk['jumlah'];
                        }
                        if ($dk['status_mutasi'] == 0) {
                            $pending_bayar = true;
                        }

                        switch ($dk['status_mutasi']) {
                            case 0:
                                $statusP = "<small class='text-warning'>(Dalam Pengecekan)</small> ";
                                $showMutasi .= "<small>#" . $dk['id_kas'] . "</small> " . $dk['note'] . " " . $statusP .  " -Rp" . number_format($dk['jumlah']) . "<br>";
                                break;
                            case 1:
                                $statusP = '<small><i class="fa-solid fa-check text-success"></i></small> ';
                                $showMutasi .= "<small>#" . $dk['id_kas'] . "</small> " . $dk['note'] . " " . $statusP .  " -Rp" . number_format($dk['jumlah']) . "<br>";
                                break;
                            default:
                                $statusP = '<small><i class="fa-solid fa-xmark text-danger"></i></small> ';
                                $showMutasi .= "<small>#" . $dk['id_kas'] . "</small> " . $dk['note'] . " " . $statusP .  " -Rp" . number_format($dk['jumlah']) . "</del><br>";
                                break;
                        }
                    }
                }
            }
            ?>
            <div class="container-fluid pt-2 ps-0 pe-0 pb-0">
                <div class="card p-0 pb-0">
                    <small>
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php
                                $no = 0;
                                foreach ($data['order'] as $do) {
                                    $no++;
                                    $id = $do['id_order_data'];
                                    $jumlah = $do['harga'] * $do['jumlah'];

                                    $cancel = $do['cancel'];
                                    $id_cancel = $do['id_cancel'];

                                    if ($cancel == 0) {
                                        $bill += $jumlah;
                                        $total += $jumlah;
                                    }

                                    $bill -= $do['diskon'];
                                    $total -= $do['diskon'];

                                    $id_order_data = $do['id_order_data'];
                                    $id_produk = $do['id_produk'];
                                    $detail_arr = unserialize($do['produk_detail']);

                                    $dateTime = substr($do['insertTime'], 0, 10);
                                    $today = date("Y-m-d");

                                    foreach ($this->dProduk as $dp) {
                                        if ($dp['id_produk'] == $id_produk) {
                                            $produk = $dp['produk'];
                                        }
                                    }

                                    $divisi_arr = unserialize($do['spk_dvs']);
                                    $divisi = [];
                                    $countSPK =  count($divisi_arr);
                                    foreach ($divisi_arr as $key => $dv) {
                                        foreach ($this->dDvsAll as $dv_) {
                                            if ($dv_['id_divisi'] == $key) {
                                                $divisi[$key] = $dv_['divisi'];
                                            }
                                        }
                                    }

                                    $id_pelanggan = $do['id_pelanggan'];
                                    if ($no == 1) {
                                        foreach ($data['pelanggan'] as $dp) {
                                            if ($dp['id_pelanggan'] == $id_pelanggan) {
                                                $pelanggan = $dp['nama'];
                                            }
                                        }

                                        foreach ($data['karyawan'] as $dp) {
                                            if ($dp['id_karyawan'] == $do['id_penerima']) {
                                                $cs = $dp['nama'];
                                            }
                                            if ($dp['id_karyawan'] == $do['id_user_afiliasi']) {
                                                $cs_to = $dp['nama'];
                                            }
                                        }
                                ?>
                                        <tr class="">
                                            <td colspan="5" class="table-light <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                                                <table class="w-100 p-0 m-0">
                                                    <tr>
                                                        <td>
                                                            <span class="text-danger"><?= substr($ref, -4) ?></span> <b><?= strtoupper($pelanggan) ?></b>
                                                        </td>
                                                        <?php if ($do['id_afiliasi'] == 0 || $do['id_afiliasi'] <> $this->userData['id_toko']) { ?>
                                                            <td class="text-end"><small><b><?= $cs ?></b></span></small></td>
                                                        <?php } else { ?>
                                                            <td class="text-end"><small><b><?= $cs ?> -> <?= $cs_to ?></b></span></small></td>
                                                        <?php }
                                                        ?>
                                                        <td class="text-end ps-1" style="width: 1%; white-space:nowrap">[<?= substr($do['insertTime'], 2, -3) ?>]</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php }
                                    ?>
                                    <tr style="<?= ($cancel == 1) ? 'color:silver' : '' ?>">
                                        <td>
                                            <table class="border-bottom">
                                                <?php
                                                if ($cancel <> 0) {
                                                    $canceler = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $id_cancel); ?>
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
                                                        <?php if ($do['id_afiliasi'] <> 0 && $do['id_afiliasi'] <> $this->userData['id_toko']) {
                                                            $toko_aff = $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $do['id_afiliasi']);
                                                            if ($do['status_order'] == 1) { ?>
                                                                <span class="badge text-primary border border-warning"><?= $toko_aff ?> - <span class="text-danger">Checking</span></span>
                                                            <?php } else {
                                                                $cs_aff = $this->model('Arr')->get($this->dKaryawanAll, "id_karyawan", "nama", $do['id_user_afiliasi']);
                                                            ?>
                                                                <span class="badge text-dark border border-success"><span class="text-dark">Verified</span> by <?= $cs_aff ?> - <?= $toko_aff ?></span>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                <tr>
                                                <tr>
                                                    <?php
                                                    foreach ($detail_arr as $da) { ?>
                                                        <td class="pe-1" nowrap>
                                                            <?= "<small>" . $da['group_name'] . "</small> <br>" . strtoupper($da['detail_name']) ?>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                            </table>
                                            <div class="row">
                                                <div class="col-auto">
                                                    <span class="text-nowrap">
                                                        <small>Catatan Utama<br><span class="text-danger"><?= $do['note'] ?></span></small>
                                                    </span>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="text-nowrap">
                                                        <small>Catatan Produksi<br>
                                                            <span class="text-primary">
                                                                <?php
                                                                foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                                    if (strlen($ns) > 0) {
                                                                        echo "<b>" . $this->model('Arr')->get($this->dDvsAll, "id_divisi", "divisi", $ks) . ":</b> " . $ns . ", ";
                                                                    }
                                                                }
                                                                ?>
                                                            </span>
                                                        </small>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><small>
                                                <?php
                                                foreach ($divisi as $key => $dvs) {
                                                    if ($divisi_arr[$key]['status'] == 1) {
                                                        $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_produksi']);
                                                        echo '<i class="fa-solid fa-check text-success"></i> ' . $dvs . " (" . $karyawan . ")<br>";
                                                    } else {
                                                        echo '<i class="fa-regular fa-circle"></i> ' . $dvs . "<br>";
                                                    }

                                                    if ($divisi_arr[$key]['cm'] == 1) {
                                                        if ($divisi_arr[$key]['cm_status'] == 1) {
                                                            $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_cm']);
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
                                                ?>
                                                        <span class="btnAmbil" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#exampleModal4" data-id="<?= $id ?>"><i class="fa-regular fa-circle"></i> Ambil</span>

                                                <?php }
                                                } else {
                                                    if ($cancel == 0) {
                                                        $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $id_ambil);
                                                        echo '<span class="text-purple"><i class="fa-solid fa-check"></i> Ambil (' . $karyawan . ")</span>";
                                                    }
                                                } ?>
                                            </small>
                                        </td>
                                        <td class="text-end"><?= number_format($do['jumlah']) ?></td>
                                        <td class="text-end">
                                            <?php
                                            if ($do['diskon'] > 0) { ?>
                                                <del>Rp<?= number_format($jumlah) ?></del><br><small>Disc. Rp<?= number_format($do['diskon']) ?></small><br>Rp<?= number_format($jumlah - $do['diskon']) ?>
                                            <?php } else { ?>
                                                <?= number_format($jumlah) ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php }

                                $sisa = $bill - $dibayar;

                                if ($sisa <= 0 && $pending_bayar == false) {
                                    $lunas = true;
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
                                    <td class="text-end text" colspan="3">
                                    </td>
                                    <td class="text-end" nowrap><?= ($lunas == true) ? '<i class="fa-solid text-success fa-circle-check"></i>' : '' ?> <b>Rp<?= number_format($total) ?></b></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-end text" colspan="4">
                                        <?= $showMutasi ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </small>
                </div>
            </div>
        </div>
    </div>
</main>

<form action="<?= PV::BASE_URL; ?>Data_Order/ambil_semua" method="POST">
    <div class="modal" id="exampleModal3">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengambilan Semua</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label">Karyawan</label>
                                <input type="hidden" name="ambil_ref">
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Ambil Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Data_Order/ambil" method="POST">
    <div class="modal" id="exampleModal4">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengambilan</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col">
                                <label class=" form-label">Karyawan</label>
                                <input type="hidden" name="ambil_id">
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Ambil</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Data_Order/cancel" method="POST">
    <div class="modal" id="exampleModalCancel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pembatalan!</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label">Karyawan</label>
                                <input type="hidden" name="cancel_id">
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Alasan Cancel</label>
                                <input type="text" name="reason" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Cancel Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Data_Order/bayar" method="POST">
    <div class="modal" id="exampleModal2">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <label class="form-label">Jumlah Bill (Rp)</label>
                                <input type="number" name="bill" class="form-control bill" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Metode</label>
                                <select name="method" class="form-select metodeBayar" required>
                                    <?php if (in_array($this->userData['user_tipe'], $this->pKasir)) { ?>
                                        <option value="1">Tunai</option>
                                    <?php } ?>
                                    <option value="2">Non Tunai</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <label class="form-label">Bayar (Rp) <small><span style="cursor: pointer;" class="bayarPas text-danger">Bayar Pas (Click)</span></small></label>
                                <input type="number" name="jumlah" class="form-control dibayar" required>
                                <input type="hidden" name="ref" id="refBayar" required>
                                <input type="hidden" name="client" id="client" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Kembalian (Rp)</label>
                                <input type="number" class="form-control kembalian" readonly>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label"><span class="text-primary">Catatan</span> <small>(Contoh: BCA/Qris)</small></label>
                                <input type="text" name="note" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Bayar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>