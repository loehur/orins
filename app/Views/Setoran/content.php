<main>
    <?php $total = 0 ?>
    <?php $total_masalah = 0 ?>
    <?php if (count($data['kas']) > 0) { ?>
        <div class="p-2 ms-3 me-3 bg-white">
            <div class="row mb-1">
                <div class="col ms-2">
                    <span class="text-purple">Setoran Dalam Antrian</span></small>
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
                                <td><?= strtoupper($pelanggan) ?></td>
                                <td><?= $ref ?></td>
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

    <?php if ($total > 0) { ?>
        <div class="pe-2 pb-0 ms-3 me-3 bg-white">
            <div class="row">
                <div class="col">
                    <table class="table table-sm table-borderless mb-2">
                        <tr>
                            <td class="text-end text-success"><button id="setor" class="btn btn-outline-success">Buat Setoran: <b class="ms-2">Total Rp<?= number_format($total) ?></b></button></td>
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
    <div class="pe-2 pb-0 ms-3 me-3 bg-white">
        <div class="row mb-1">
            <div class="col ms-2">
                <span class="text-purple">Riwayat Setoran Kasir</span> <small>(Last 20)</small>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-sm mb-2 ms-2">
                    <?php foreach ($data['setor'] as $set) {
                        $st_setor = "";
                        switch ($set['status_setoran']) {
                            case 0:
                                $st_setor = "<span class='text-warning'><i class='fa-regular fa-circle'></i> Finance Checking</span>";
                                break;
                            case 1:
                                $st_setor = "<span class='text-success'><i class='fa-solid fa-circle-check'></i> Verified</span>";
                                break;
                            default:
                                $st_setor = "<span class='text-danger text-nowrap'><i class='fa-solid fa-circle-xmark'></i></i> Rejected</span>";
                                break;
                        }
                    ?>
                        <tr>
                            <td class="text-primary" style="cursor: pointer;"><span data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $set['ref_setoran'] ?>"><small><i class="fa-solid fa-list-check"></i></small></span></td>
                            <td><?= $set['count'] ?> Transaksi</td>
                            <td><?= $set['ref_setoran'] ?></td>
                            <td class="text-end">Rp<?= number_format($set['jumlah']) ?></td>
                            <td style="width: 1px; white-space: nowrap;"><?= $st_setor ?></td>
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

    $("a.cancel").click(function() {
        id = $(this).attr("data-id");
        $("input[name=id_kas]").val(id);
    })
</script>