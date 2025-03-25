<main>
    <?php $date_choose = $data['date'] == "" ? date('Y-m-d', strtotime("-1 days")) : $data['date'] ?>
    <div class="row mx-0 mb-2 px-2">
        <div class="col pe-0">
            <div class="input-group">
                <span class="input-group-text text-primary">Tanggal</span>
                <input id="inDate" name="month" type="date" min="2023-07-01" max="<?= date('Y-m-d', strtotime("-1 days")) ?>" value="<?= $date_choose ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                <button id="cek" class="btn btn-primary">Cek</button>
            </div>

        </div>
        <div class="col-auto">
            <button id="cekTotal" class="btn btn-success">Total</button>
        </div>
    </div>

    <?php $total = 0 ?>
    <?php if (count($data['kas']) > 0) { ?>
        <div class="p-2 ms-3 me-3 bg-white overflow-auto" style="max-height: 600px;">

            <div class="row mx-0">
                <div class="col">
                    <table class="table table-sm text-sm">
                        <tr>
                            <th colspan="10" class="text-success">Penjualan Tunai</th>
                        </tr>
                        <?php
                        $no = 0;
                        foreach ($data['kas'] as $a) {
                            $no += 1;

                            $client = $a['id_client'];
                            $jumlah = $a['jumlah'];
                            if ($a['status_mutasi'] == 1) {
                                $total += $jumlah;
                            }
                            $pelanggan = $data['pelanggan'][$client]['nama'];

                            $ref = $a['ref_transaksi'];
                            if ($a['jenis_transaksi'] == 2) {
                                $ref = "Topup Deposit";
                            }

                        ?>
                            <tr class="<?= ($a['status_mutasi'] == 2) ? 'text-secondary' : '' ?>">
                                <td align="right"><a href="<?= PV::BASE_URL ?>Cek/order/<?= $a['ref_transaksi'] ?>/<?= $a['id_client'] ?>" target="_blank">#<?= $a['id_kas'] ?></a></td>
                                <td><?= date('d/m/y H:i', strtotime($a['insertTime'])) ?></td>
                                <td><?= strtoupper($pelanggan) ?></td>
                                <td><?= $ref ?></td>
                                <td align="right"><?= number_format($jumlah) ?></td>
                                <td>
                                    <?php if ($a['status_mutasi'] == 1) { ?>
                                        <a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="px-2 text-decoration-none text-danger cancel rounded" data-id="<?= $a['id_kas'] ?>" href="#"><i class="fa-solid fa-square-xmark"></i></a>
                                    <?php } else { ?>
                                        <small class="text-primary"><?= $a['note_batal'] ?></small>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>
            </div>
        </div>
    <?php } ?>

    <?php $total_pengeluaran = 0; ?>
    <?php if (count($data['pengeluaran']) > 0) { ?>
        <div class="p-2 ms-3 me-3 bg-white overflow-auto" style="max-height: 600px;">
            <div class="row mx-0">
                <div class="col">
                    <table class="table table-sm text-sm">
                        <tr>
                            <th colspan="10" class="text-danger">Pengeluaran</th>
                        </tr>
                        <?php
                        $no = 0;
                        foreach ($data['pengeluaran'] as $a) {
                            $no += 1;

                            $jumlah = $a['jumlah'];

                            if ($a['status_mutasi'] <> 2) {
                                $total_pengeluaran += $jumlah;
                            }

                            $ref = $a['ref_transaksi'];
                            $jenis = $data['jkeluar'][$ref]['nama'];
                        ?>
                            <tr class="<?= ($a['status_mutasi'] == 2) ? 'text-secondary' : '' ?>">
                                <td align="right">#<?= $a['id_kas'] ?></td>
                                <td><?= date('d/m/y H:i', strtotime($a['insertTime'])) ?></td>
                                <td><?= strtoupper($jenis) ?></td>
                                <td><?= strtoupper($a['note']) ?></td>
                                <td align="right"><?= number_format($jumlah) ?></td>
                                <td>
                                    <?php if ($a['status_mutasi'] == 1) { ?>
                                        <a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="px-2 text-decoration-none text-danger cancel rounded" data-id="<?= $a['id_kas'] ?>" href="#"><i class="fa-solid fa-square-xmark"></i></a>
                                    <?php } else { ?>
                                        <small class="text-primary"><?= $a['note_batal'] ?></small>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>
            </div>
        </div>
    <?php } ?>

    <?php $total_refund = 0; ?>
    <?php if (count($data['refund']) > 0) { ?>
        <div class="p-2 ms-3 me-3 bg-white overflow-auto" style="max-height: 600px;">
            <div class="row mx-0">
                <div class="col">
                    <table class="table table-sm text-sm">
                        <tr>
                            <th colspan="10" class="text-primary">Refund</th>
                        </tr>
                        <?php
                        $no = 0;
                        foreach ($data['refund'] as $r) {
                            $no += 1;
                            $jumlah = $r['refund'];
                            $total_refund += $jumlah;
                            $pelanggan = $data['pelanggan'][$r['id_pelanggan']]['nama']; ?>
                            <tr>
                                <td align="right"><a href="<?= PV::BASE_URL ?>Cek/order/<?= $r['ref'] ?>/<?= $r['id_pelanggan'] ?>" target="_blank">#<?= $r['ref'] ?></a></td>
                                <td><?= date('d/m/y', strtotime($r['refund_date'])) ?></td>
                                <td><?= strtoupper($pelanggan) ?></td>
                                <td><?= strtoupper($r['refund_reason']) ?></td>
                                <td align="right"><?= number_format($jumlah) ?></td>
                                <td>
                                    <a class="px-2 text-decoration-none text-danger rounded" href="#"><i class="fa-solid fa-square-xmark"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $total_sds = 0;
    $total -= ($total_sds);
    foreach ($data['sds'] as $ds) {
        if (isset($data['kas_trx'][$ds['ref']])) {
            foreach ($data['kas_trx'][$ds['ref']] as $dsx) {
                $total_sds += $dsx['jumlah'];
            }
        }
    }
    ?>

    <?php if ($total > 0) { ?>
        <div class="pe-2 pb-0 mt-2 ms-3 me-3 bg-white">
            <div class="row">
                <div class="col">
                    <table class="table table-sm text-sm table-borderless mb-2">
                        <tr>
                            <td class="text-end">Penjualan Tunai <span class="text-success fw-bold"><?= strtoupper($this->dToko[$this->userData['id_toko']]['inisial']) ?></span></td>
                            <td class="text-end" style="width:100px"><b>Rp<?= number_format($total - $total_sds - $total_pengeluaran - $total_refund) ?></b></td>
                            <td rowspan="10" class="text-success text-sm align-middle" style="width: 100px;">
                                <?php if ($data['date'] <> "") { ?>
                                    <button id="setor" class="btn btn-outline-success py-3 rounded-1">Buat<br>Setoran</button>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php if ($total_sds > 0) { ?>
                            <tr>
                                <td class="text-end">Penjualan Tunai <span class="text-success fw-bold">SDS</span></td>
                                <td class="text-end" style="width:100px"><b>Rp<?= number_format($total_sds) ?></b></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td class="text-end">Total Tunai</td>
                            <td class="text-end" style="width:100px"><b>Rp<?= number_format($total - $total_pengeluaran - $total_refund) ?></b></td>
                        </tr>
                        <tr>
                            <td class="text-end">
                                <a data-bs-toggle="modal" data-bs-target="#modalPengeluaran" class="text-decoration-none" data-id="<?= $a['id_kas'] ?>" href="#"><i class="fa-solid text-danger fa-square-plus"></i> Pengeluaran</a>
                            </td>
                            <td class="text-end" style="width:100px"><b>Rp<?= number_format($total_pengeluaran) ?></b></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="pe-2 pb-0 ms-3 me-3 bg-white text-sm">
        <div class="row mb-1">
            <div class="col ms-2">
                <span class="text-purple">Riwayat Setoran</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-sm mb-2 ms-2 text-sm">
                    <?php foreach ($data['setor'] as $set) {
                        $st_setor = "";
                        $sds_done[$set['ref_setoran']] = 0;
                        if (isset($data['sds_done'][$set['ref_setoran']])) {
                            $sds_done[$set['ref_setoran']] = ($data['sds_done'][$set['ref_setoran']]['jumlah']);
                        }

                        switch ($set['status_setoran']) {
                            case 0:
                                $st_setor = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                break;
                            case 1:
                                $st_setor = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                break;
                            default:
                                $st_setor = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                break;
                        }

                        if (isset($data['keluar'][$set['ref_setoran']]['count'])) {
                            $count_keluar = $data['keluar'][$set['ref_setoran']]['count'];
                            $jumlah_keluar = $data['keluar'][$set['ref_setoran']]['jumlah'];
                        } else {
                            $count_keluar = 0;
                            $jumlah_keluar = 0;
                        }

                        if (isset($data['refund_done'][$set['ref_setoran']]['jumlah'])) {
                            $refund = $data['refund_done'][$set['ref_setoran']]['jumlah'];
                        } else {
                            $refund = 0;
                        }

                        $totalSetor = $set['jumlah'] - $jumlah_keluar; ?>
                        <tr>
                            <td>
                                <span data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#modalCek" class="cekTrx text-primary" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid fa-list-check mt-1"></i></small></span>
                                <br>
                                <?= $set['count'] + $count_keluar ?> Trx<br>
                                <?= $set['ref_setoran'] ?>
                            </td>
                            <td class="text-end">
                                <?php if ($set['status_setoran'] == 0) { ?>
                                <?php
                                    $boleh_split = true;
                                    if (isset($data['split'][$set['ref_setoran']])) {
                                        $boleh_split = false;
                                    }
                                    if (isset($data['setor_office'][$set['ref_setoran']])) {
                                        $boleh_split = false;
                                    }
                                    if (isset($data['sds_tarik'][$set['ref_setoran']])) {
                                        $boleh_split = false;
                                    }
                                } else {
                                    $boleh_split = false;
                                } ?>

                                <b>Rp<?= number_format($totalSetor) ?></b><br>
                                <?php
                                if (isset($data['split'][$set['ref_setoran']])) {
                                    $ds = $data['split'][$set['ref_setoran']];

                                    switch ($ds['st']) {
                                        case 0:
                                            $st_slip1 = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                            break;
                                        case 1:
                                            $st_slip1 = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                            break;
                                        default:
                                            $st_slip1 = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                            break;
                                    } ?>
                                    <?= $st_slip1 ?> Uang Kecil <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?></span><br>
                                    <?php $totalSetor -= $ds['jumlah'] ?>
                                <?php }
                                if (isset($data['setor_office'][$set['ref_setoran']])) {
                                    $ds = $data['setor_office'][$set['ref_setoran']];

                                    switch ($ds['st']) {
                                        case 0:
                                            $st_slip2 = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                            break;
                                        case 1:
                                            $st_slip2 = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                            break;
                                        default:
                                            $st_slip2 = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                            break;
                                    } ?>

                                    <?= $st_slip2 ?> Kas Kantor <small>(<?= $ds['note'] ?>)</small> <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?></span><br>
                                    <?php $totalSetor -= $ds['jumlah'] ?>
                                <?php }
                                if (isset($data['sds_tarik'][$set['ref_setoran']])) {
                                    $ds = $data['sds_tarik'][$set['ref_setoran']];

                                    switch ($ds['st']) {
                                        case 0:
                                            $st_slip2 = "<span class='text-warning'><i class='fa-regular fa-circle'></i></span>";
                                            break;
                                        case 1:
                                            $st_slip2 = "<span class='text-success'><i class='fa-solid fa-circle-check'></i></span>";
                                            break;
                                        default:
                                            $st_slip2 = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                            break;
                                    } ?>

                                    <?= $st_slip2 ?> Kas Kantor <small>(<?= $ds['note'] ?>)</small> <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?></span><br>
                                <?php } ?>


                                <?php
                                $sds_tarik = 0;
                                if (isset($data['sds_tarik'][$set['ref_setoran']])) {
                                    $ds = $data['sds_tarik'][$set['ref_setoran']];
                                    $sds_tarik = $ds['jumlah'];
                                }
                                ?>

                                <?php if (isset($data['sds_done'][$set['ref_setoran']])) { ?>
                                    <?php if ($boleh_split == true) { ?>
                                        <span style="cursor:pointer" data-bs-toggle="modal" onclick="ref('<?= $set['ref_setoran'] ?>',<?= ($sds_done[$set['ref_setoran']]) ?>,4)" data-bs-target="#modalSplit" class="badge bg-primary">Split</span>
                                    <?php } ?>
                                    <span><?= $st_setor ?> Setor SDS</span> <span class="text-success"><?= number_format($sds_done[$set['ref_setoran']] - $sds_tarik) ?></span><br>
                                <?php } ?>

                                <?php if ($boleh_split == true) { ?>
                                    <span style="cursor:pointer" data-bs-toggle="modal" onclick="ref('<?= $set['ref_setoran'] ?>',<?= ($totalSetor - $sds_done[$set['ref_setoran']]) ?>,0)" data-bs-target="#modalSplit" class="badge bg-primary">Split</span>
                                <?php } ?>
                                <span><?= $st_setor ?> Setor <span class=""><?= strtoupper($this->dToko[$this->userData['id_toko']]['inisial']) ?></span> <span class="text-success"><?= number_format($totalSetor - $sds_done[$set['ref_setoran']] - $refund) ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal" id="modalCek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="cek_load"></div>
    </div>
</div>

<form action="<?= PV::BASE_URL; ?>Setoran/cancel" method="POST">
    <div class="modal" id="exampleModalCancel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pembatalan!</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Alasan Cancel</label>
                                <input type="text" name="reason" class="form-control form-control-sm" required>
                                <input type="hidden" name="id_kas">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Cancel Pembayaran</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<form action="<?= PV::BASE_URL; ?>Setoran/tambah_pengeluaran" method="POST">
    <div class="modal" id="modalPengeluaran">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Pengeluaran</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control form-control-sm" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Jenis</label>
                                <select name="jenis" class="form-control form-control-sm" required>
                                    <option></option>
                                    <?php
                                    foreach ($data['jkeluar'] as $djk) { ?>
                                        <option value="<?= $djk['id'] ?>"><?= $djk['nama'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="note" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-danger">Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="<?= PV::BASE_URL; ?>Setoran/split" method="POST">
    <div class="modal" id="modalSplit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Split Setor</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col">
                                <label class="form-label">Kas Kantor</label>
                                <input type="hidden" id="inp_ref" name="ref">
                                <input type="hidden" id="tipe" name="tipe">
                                <input type="number" id="uangFinance" min="1" name="jumlah_finance" class="form-control form-control-sm text-end">
                            </div>
                            <div class="col">
                                <label class="form-label">Setoran <?= strtoupper($this->dToko[$this->userData['id_toko']]['inisial']) ?></label>
                                <input type="number" id="jumlah_bank" readonly class="form-control form-control-sm text-end">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="note" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Split</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("button#cek").click(function() {
        var date = $('#inDate').val();
        location.href = "<?= PV::BASE_URL ?>Setoran/index/" + date;
    });

    $("button#cekTotal").click(function() {
        location.href = "<?= PV::BASE_URL ?>Setoran";
    });

    $("button#setor").dblclick(function() {
        $.ajax({
            url: "<?= PV::BASE_URL ?>Setoran/setor/<?= $data['date'] ?>",
            data: [],
            type: "POST",
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result);
                }
            },
        });
    });

    $('span.cekTrx').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cek_load").load('<?= PV::BASE_URL ?>Setoran/cek/' + ref);
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

    var totalSetor = 0;

    function ref(ref_nya, total, tipe) {
        $("input#inp_ref").val(ref_nya);
        $("input#tipe").val(tipe);
        totalSetor = total;
        $("#jumlah_bank").val(totalSetor - $("#uangFinance").val())
        $("input#uangFinance").attr({
            "max": totalSetor
        });

    }

    $("a.cancel").click(function() {
        id = $(this).attr("data-id");
        $("input[name=id_kas]").val(id);
    })

    $("#uangFinance").on('change keyup keypress', function() {
        $("#jumlah_bank").val(totalSetor - $("#uangFinance").val())
    })
</script>