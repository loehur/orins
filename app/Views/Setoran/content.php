<main>
    <?php $total = 0 ?>
    <?php $total_masalah = 0 ?>
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
                            $pelanggan = "Non";
                            foreach ($data['pelanggan'] as $dp) {
                                if ($dp['id_pelanggan'] == $client) {
                                    $pelanggan = $dp['nama'];
                                }
                            }

                            $ref = $a['ref_transaksi'];
                            if ($a['jenis_transaksi'] == 2) {
                                $ref = "Topup Deposit";
                            }

                        ?>
                            <tr class="<?= ($a['status_mutasi'] == 2) ? 'text-secondary' : '' ?>">
                                <td align="right">#<?= $a['id_kas'] ?></td>
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

                            $total_pengeluaran += $jumlah;
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

    <?php if ($total > 0) { ?>
        <div class="pe-2 pb-0 ms-3 me-3 bg-white">
            <div class="row">
                <div class="col">
                    <table class="table table-sm text-sm table-borderless mb-2">
                        <tr>
                            <td class="text-end">Penjualan Tunai</td>
                            <td class="text-end" style="width:100px"><b>Rp<?= number_format($total) ?></b></td>
                            <td rowspan="10" class="text-success text-sm align-middle"><button id="setor" class="btn btn-outline-success py-3 rounded-1">Buat<br>Setoran</button></td>
                        </tr>
                        <tr>
                            <td class="text-end">
                                <a data-bs-toggle="modal" data-bs-target="#modalPengeluaran" class="text-decoration-none" data-id="<?= $a['id_kas'] ?>" href="#"><i class="fa-solid text-danger fa-square-plus"></i> Pengeluaran</a>
                            </td>
                            <td class="text-end" style="width:100px"><b>Rp<?= number_format($total_pengeluaran) ?></b></td>
                        </tr>
                        <tr>
                            <td class="text-end">Total</td>
                            <td class="text-end" style="width:100px"><b>Rp<?= number_format($total - $total_pengeluaran) ?></b></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (count($data['kas_reject']) > 0) { ?>
        <div class="p-2 ms-3 mt-3 me-3 bg-white">
            <div class="row mb-1">
                <div class="col ms-2">
                    <span class="text-purple">Setoran Bermasalah</span></small>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="table table-sm">
                        <tr>
                            <th class="text-end">ID</th>
                            <th>Customer</th>
                            <th>Referensi</th>
                            <th>Tanggal</th>
                            <th class="text-end">Jumlah</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        $no = 0;
                        foreach ($data['kas_reject'] as $a) {
                            $no += 1;

                            $client = $a['id_client'];
                            $jumlah = $a['jumlah'];
                            if ($a['status_mutasi'] == 1) {
                                $total_masalah += $jumlah;
                            }
                            $pelanggan = "Non";
                            foreach ($data['pelanggan'] as $dp) {
                                if ($dp['id_pelanggan'] == $client) {
                                    $pelanggan = $dp['nama'];
                                }
                            }
                        ?>
                            <tr class="<?= ($a['status_mutasi'] == 2) ? 'text-secondary' : '' ?>">
                                <td align="right">#<?= $a['id_kas'] ?></td>
                                <td><?= strtoupper($pelanggan) ?></td>
                                <td><?= $a['ref_transaksi'] ?></td>
                                <td><?= $a['insertTime'] ?></td>
                                <td align="right">Rp<?= number_format($jumlah) ?></td>
                                <td>
                                    <?php if ($a['status_mutasi'] == 1) { ?>
                                        <a data-bs-toggle="modal" data-bs-target="#exampleModalCancel" class="px-2 text-decoration-none text-danger cancel border rounded" data-id="<?= $a['id_kas'] ?>" href="#">Batalkan</a>
                                    <?php } else { ?>
                                        <small>Dibatalkan</small><br>
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
    <?php if ($total_masalah > 0) { ?>
        <div class="pe-2 pb-0 ms-3 me-3 bg-white">
            <div class="row">
                <div class="col">
                    <table class="table table-sm table-borderless mb-2">
                        <tr>
                            <td class="text-end text-success"><button id="setor_masalah" class="btn btn-outline-danger">Buat Setoran Ulang: <b class="ms-2">Total Rp<?= number_format($total_masalah) ?></b></button></td>
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
                        switch ($set['status_setoran']) {
                            case 0:
                                $st_setor = "<span class='text-warning'><i class='fa-regular fa-circle'></i> Checking</span>";
                                break;
                            case 1:
                                $st_setor = "<span class='text-success'><i class='fa-solid fa-circle-check'></i> Verified</span>";
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
                        $totalSetor = $set['jumlah'] - $jumlah_keluar;
                    ?>
                        <tr>
                            <td class="text-primary" style="cursor: pointer;"><span data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid fa-list-check mt-1"></i></small></span></td>
                            <td class="text-end"><?= $set['count'] + $count_keluar ?> Trx</td>
                            <td><?= $set['ref_setoran'] ?></td>
                            <td class="text-end">
                                <?php if ($set['status_setoran'] == 0) { ?>
                                    <span style="cursor:pointer" data-bs-toggle="modal" onclick="ref('<?= $set['ref_setoran'] ?>',<?= $totalSetor ?>)" data-bs-target="#modalSplit" class="badge bg-primary">Split</span>
                                <?php } ?>
                            </td>
                            <td class="text-end">
                                Rp<?= number_format($totalSetor) ?><br>
                                <?php
                                if (isset($data['split'][$set['ref_setoran']])) {
                                    $ds = $data['split'][$set['ref_setoran']];

                                    $st_slip = "";
                                    switch ($ds['st']) {
                                        case 0:
                                            $st_slip = "<span class='text-warning'><i class='fa-regular fa-circle'></i> Checking</span>";
                                            break;
                                        case 1:
                                            $st_slip = "<span class='text-success'><i class='fa-solid fa-circle-check'></i> Verified</span>";
                                            break;
                                        default:
                                            $st_slip = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                            break;
                                    }
                                ?>
                                    Uang Kecil: <span class="text-primary">Rp<?= number_format($ds['jumlah']) ?><br>
                                    </span>Setor Bank: <span class="text-success"><?= number_format($totalSetor - $ds['jumlah']) ?></span>
                                <?php } ?>
                            </td>
                            <td style="width: 1px; white-space: nowrap;">
                                <?php if (!isset($data['split'][$set['ref_setoran']])) { ?>
                                    <?= $st_setor ?>
                                <?php } else { ?>
                                    <br>
                                    <?= $st_slip ?><br>
                                    <?= $st_setor ?>
                                <?php } ?>
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
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Uang Kecil</label>
                                <input type="hidden" id="inp_ref" name="ref">
                                <input type="number" id="uangKecil" name="jumlah" class="form-control form-control-sm text-end" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Setoran Bank</label>
                                <input type="number" id="jumlah_bank" readonly class="form-control form-control-sm">
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
    $("button#setor").click(function() {
        $.ajax({
            url: "<?= PV::BASE_URL ?>Setoran/setor",
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

    $("button#setor_masalah").click(function() {
        $.ajax({
            url: "<?= PV::BASE_URL ?>Setoran/setor_masalah",
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

    function ref(ref_nya, total) {
        $("input#inp_ref").val(ref_nya);
        totalSetor = total;
    }

    $("a.cancel").click(function() {
        id = $(this).attr("data-id");
        $("input[name=id_kas]").val(id);
    })

    $("#uangKecil").on('change keyup keypress', function() {
        $("#jumlah_bank").val(totalSetor - $(this).val())
    })
</script>