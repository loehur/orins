<main>
    <div class="row ms-1 me-2 mt-3">
        <div class="col pe-0" style="min-width: 250px; max-width: 300px">
            <select class="border tize" name="id_pelanggan" required>
                <option></option>
                <?php foreach ($data['pelanggan'] as $p) { ?>
                    <option value="<?= $p['id_pelanggan'] ?>" <?= ($data['parse'] == $p['id_pelanggan'] ? "selected" : "") ?>><?= strtoupper($p['nama']) ?></option>
                <?php } ?>
            </select>
        </div>
        <?php if ($data['parse_2'] <> 0) { ?>
            <div class="col pe-0" style="min-width: 90px; max-width: 100px">
                <select class="border tize" name="y" required>
                    <?php
                    $yNow = date("Y");
                    for ($x = 2023; $x <= $yNow; $x++) { ?>
                        <option value="<?= $x ?>" <?= ($data['parse_2'] == $x ? "selected" : "") ?>><?= $x ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
        <div class="col pt-auto mt-auto pe-0">
            <button type="submit" class="cek btn btn-sm btn-primary">Cek</button>
        </div>
    </div>

    <!-- Main page content-->
    <div class="row me-2 ps-4">
        <?php
        $arr_tuntas = [];

        for ($x = 1; $x <= 2; $x++) {
            if (count($data['order'][$x]) > 0) {
        ?>
                <div class="col ps-0 pe-2">
                    <?php foreach ($data['order'][$x] as $ref => $data['order_']) {
                        $bill = 0;
                        $total = 0;
                        $ambil = false;
                        $ambil_all = true;

                        $tuntas = true;
                        $lunas = false;
                        $pending_bayar = false;

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
                    ?>
                        <div class="container-fluid pt-2 ps-0 pe-0">
                            <div class="card p-0">
                                <small>
                                    <table class="table table-sm mb-0">
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            foreach ($data['order_'] as $do) {
                                                $no++;
                                                $id = $do['id_order_data'];
                                                $jumlah = $do['harga'] * $do['jumlah'];

                                                $cancel = $do['cancel'];
                                                $id_cancel = $do['id_cancel'];

                                                if ($cancel == 0) {
                                                    $bill += $jumlah;
                                                    $total += $jumlah;
                                                }

                                                $id_order_data = $do['id_order_data'];
                                                $id_produk = $do['id_produk'];
                                                $detail_arr = unserialize($do['produk_detail']);

                                                $dateTime = substr($do['insertTime'], 0, 10);
                                                $today = date("Y-m-d");

                                                foreach ($this->dProdukAll as $dp) {
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
                                                                    <?php if ($dibayar == 0 && $cancel == 0) { ?>
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
                                                                    <small>Catatan Utama</small><br><span><?= $do['note'] ?></span>
                                                                </span>
                                                            </div>
                                                            <div class="col-auto">
                                                                <span class="text-nowrap">
                                                                    <small>Catatan Produksi</small><br>
                                                                    <span>
                                                                        <?php
                                                                        foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                                            echo $this->model('Arr')->get($this->dDvsAll, "id_divisi", "divisi", $ks) . ": " . $ns . ", ";
                                                                        }
                                                                        ?>
                                                                    </span>
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
                                                                $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $id_ambil);
                                                                echo '<span class="text-purple"><i class="fa-solid fa-check"></i> Ambil (' . $karyawan . ")</span>";
                                                            } ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-end"><?= number_format($do['jumlah']) ?></td>
                                                    <td class="text-end">
                                                        <?php
                                                        if ($cancel == 0) { ?>
                                                            Rp<?= number_format($jumlah) ?>
                                                        <?php } else { ?>
                                                            <del>Rp<?= number_format($jumlah) ?></del>
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
                                                    <?php if (($do['id_afiliasi'] == 0 || $do['id_afiliasi'] <> $this->userData['id_toko'])) { ?>
                                                        <table>
                                                            <tr>
                                                                <td class="text-end pe-1"><small><a href="<?= $this->BASE_URL; ?>Data_Order/print/<?= $ref ?>" target="_blank" class="btnBayar border btn btn-sm px-1"><i class="fa-solid fa-print"></i> <?= $print_mode ?></a></small></td>
                                                                <?php
                                                                if ($ambil_all == false) { ?>
                                                                    <td class="text-end pe-1"><small><span data-bs-toggle="modal" data-bs-target="#exampleModal3" class="btnAmbilSemua border border-purple text-purple btn btn-sm px-1" data-ref="<?= $do['ref'] ?>">Ambil</span></small></td>
                                                                <?php } ?>
                                                                <td class="text-end pe-1"><small><span data-bs-toggle="modal" data-bs-target="#exampleModalSurcharge" class="btnSurcharge border border-info text-info btn btn-sm px-1" data-ref="<?= $do['ref'] ?>">Surcharge</span></small></td>
                                                                <?php
                                                                if (in_array($this->userData['user_tipe'], $this->pCS) && $sisa > 0) { ?>
                                                                    <td class="text-end pe-1"><small><span data-ref="<?= $ref ?>" data-client="<?= $id_pelanggan ?>" data-bill="<?= $sisa ?>" data-bs-toggle="modal" data-bs-target="#exampleModal2" class="btnBayar border border-danger text-danger btn btn-sm py-1 px-1">Bayar</span></small></td>
                                                                <?php } ?>
                                                            </tr>
                                                        </table>
                                                    <?php } ?>
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
                    <?php
                        if ($lunas == true && $ambil_all == true) {
                            array_push($arr_tuntas, $ref);
                        }
                    } ?>
                </div>
        <?php }
        }
        ?>
    </div>
</main>

<form action="<?= $this->BASE_URL; ?>Data_Order/ambil_semua" method="POST">
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

<form action="<?= $this->BASE_URL; ?>Data_Order/ambil" method="POST">
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

<form action="<?= $this->BASE_URL; ?>Data_Order/cancel" method="POST">
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

<form action="<?= $this->BASE_URL; ?>Data_Order/bayar" method="POST">
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
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();

        var parse_2 = <?= $data['parse_2'] ?>;
        if (parse_2 == 0) {
            clearTuntas();
        }
    });

    function clearTuntas() {
        var dataNya = '<?= serialize($arr_tuntas) ?>';
        var countArr = <?= count($arr_tuntas) ?>;

        if (countArr > 0) {
            $.ajax({
                url: '<?= $this->BASE_URL ?>Data_Operasi/clearTuntas',
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
        location.href = "<?= $this->BASE_URL ?>Data_Operasi/index/" + parse + "/" + parse_2;
    });

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
</script>