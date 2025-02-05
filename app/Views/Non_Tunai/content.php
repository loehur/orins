<main>
    <div class="p-2 ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col">
                <div class="row border-bottom">
                    <div class="col">
                        <span>Antrian Pengecekan Non Tunai</span>
                    </div>
                </div>
                <small>
                    <table class="table table-sm table-hover mt-2 text-sm">
                        <?php if (count($data['kas']) > 0) {
                            foreach ($data['kas'] as $rb => $ref_bayar) { ?>
                                <?php
                                $no = 0;
                                $id_multi = "";
                                foreach ($ref_bayar as  $a) {
                                    $no += 1;
                                    $id =  $a['id_kas'];

                                    $client = $a['id_client'];
                                    $jumlah = $a['jumlah'];

                                    if (isset($data['payment_account'][$a['pa']]['payment_account'])) {
                                        $payment_account = $data['payment_account'][$a['pa']]['payment_account'] . " ";
                                    } else {
                                        $payment_account = "";
                                    }

                                    $pelanggan = "Non";
                                    $pelanggan = $data['pelanggan'][$client]['nama']; ?>
                                    <tr class="rb<?= $rb ?>">
                                        <td class="px-0">
                                            <span class="text-purple"><?= $data['toko'][$a['id_toko']]['nama_toko'] ?></span><br>
                                            <?php if ($a['jenis_transaksi'] == 1) { ?>
                                                <span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalCek" data-pelanggan="<?= $client ?>" class="cekTrx" data-ref="<?= $a['ref_transaksi'] ?>"><small><?= $a['ref_transaksi'] ?></small></span>
                                            <?php } ?>
                                            <?php if ($a['jenis_transaksi'] == 2) { ?>
                                                <small>Topup Deposit</small>
                                            <?php } ?>
                                        </td>
                                        <td class="">#<?= $a['id_kas'] ?><br><?= strtoupper($pelanggan) ?></td>
                                        <td align="right" class="pe-2">Rp<?= number_format($jumlah) ?><br><?= $a['note'] ?></td>
                                    </tr>
                                <?php
                                } ?>
                                <tr class="rb<?= $rb ?>">
                                    <td colspan="2" class="px-0"><button data-id="<?= $rb ?>" data-val="2" class="border-0 actionMulti btn btn-sm btn-outline-danger">Reject - <b>Rp<?= number_format($data['kas_group'][$rb]['jumlah']) ?></b></button></td>
                                    <td class="px-0 text-end"><button data-id="<?= $rb ?>" data-val="1" class="border-0 actionMulti btn btn-sm btn-outline-success">Verify - <b>Rp<?= number_format($data['kas_group'][$rb]['jumlah']) ?></b></button></td>
                                    <td></td>
                                </tr>
                                <tr class="rb<?= $rb ?>">
                                    <td colspan="10" class="bg-light"></td>
                                </tr>
                        <?php }
                        } ?>
                    </table>
                </small>
            </div>
        </div>
    </div>
    <pre>
        <?php
        print_r($data['ref']);
        ?>
    </pre>
    <div class="p-2 ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col">
                <div class="row border-bottom">
                    <div class="col">
                        <span>Riwayat Pembayaran Terkonfirmasi</span>
                    </div>
                </div>
                <small>
                    <table class="table table-sm text-sm table-hover mt-2">
                        <?php
                        $no = 0;
                        foreach ($data['kas_done'] as $rb => $ref_bayar) {
                            foreach ($ref_bayar as $a) {
                                $no += 1;
                                $id =  $a['id_kas'];

                                $client = $a['id_client'];
                                $jumlah = $a['jumlah'];
                                $ref = $a['ref_transaksi'];
                                $tuntas = 0;
                                //$tuntas = $data['ref'][$ref]['tuntas'];

                                $pelanggan = "Non";
                                $pelanggan = $data['pelanggan'][$client]['nama']; ?>
                                <tr>
                                    <td>
                                        <span class="text-purple"><?= $data['toko'][$a['id_toko']]['nama_toko'] ?></span><br>
                                        <span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalCek" data-pelanggan="<?= $client ?>" class="cekTrx" data-ref="<?= $a['ref_transaksi'] ?>"><small><?= $a['ref_transaksi'] ?></small></span>
                                    </td>
                                    <td>#<?= $a['id_kas'] ?><br><?= strtoupper($pelanggan) ?></td>
                                    <td align="right" class="pe-2">Rp<?= number_format($jumlah) ?><br><?= $a['note'] ?></td>
                                    <td class="text-end">
                                        <?php
                                        switch ($a['status_mutasi']) {
                                            case 1:
                                                echo '<span class="text-success"><i class="fa-solid fa-check-to-slot"></i> Verified</span>';
                                                break;
                                            default:
                                                echo '<span class="text-danger"><i class="fa-solid fa-xmark"></i> Rejected</span>';
                                                break;
                                        }
                                        ?>
                                        <br>
                                        <?= $a['updateTime'] ?>
                                    </td>

                                    <?php

                                    if ($tuntas == 0) {
                                        switch ($a['status_mutasi']) {
                                            case 1: ?>
                                                <td align="right">
                                                    <button data-id="<?= $id ?>" data-val="2" class="action btn btn-sm btn-outline-secondary px-2 py-0 border-0">Reject</button>
                                                </td>
                                            <?php break;
                                            default: ?>
                                                <td align="right">
                                                    <button data-id="<?= $id ?>" data-val="1" class="action btn btn-sm btn-outline-secondary px-2 py-0 border-0">Verify</button>
                                                    <br>
                                                    <span class="pe-2"><?= $a['note_batal'] ?></span>
                                                </td>
                                            <?php break;
                                        }
                                    } else {
                                        switch ($a['status_mutasi']) {
                                            case 1: ?>
                                                <td align="right" class="text-secondary">
                                                    <small><span class="pe-2">Transaction Complete<br><?= $a['note_office'] ?></span></small>
                                                </td>
                                            <?php break;
                                            default: ?>
                                                <td align="right">
                                                    <small><span class="pe-2">Transaction Complete<br><?= $a['note_batal'] ?></span></small>
                                                </td>
                                    <?php break;
                                        }
                                    }
                                    ?>
                                </tr>
                        <?php }
                        }
                        ?>
                    </table>
                </small>
            </div>
        </div>
    </div>
</main>

<div class="modal" id="modalCek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="cekOrder"></div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("button.action").click(function() {
        var id_ = $(this).attr("data-id");
        var value = $(this).attr("data-val");
        var note = "";

        if (value == 2) {
            var note = prompt("Catatan", "");
            if (note === null) {
                return;
            }
        }

        $.ajax({
            url: "<?= PV::BASE_URL ?>Non_Tunai/action",
            data: {
                id: id_,
                val: value,
                note: note
            },
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

    $("button.actionMulti").click(function() {
        var id_ = $(this).attr("data-id");
        var value = $(this).attr("data-val");
        $.ajax({
            url: "<?= PV::BASE_URL ?>Non_Tunai/actionMulti",
            data: {
                id: id_,
                val: value
            },
            type: "POST",
            success: function(result) {
                if (result == 0) {
                    $('.rb' + id_).remove();
                } else {
                    alert(result);
                }
            },
        });
    });

    $('span.cekTrx').click(function() {
        var ref = $(this).attr("data-ref");
        var id_pelanggan = $(this).attr("data-pelanggan");
        $("div#cekOrder").load('<?= PV::BASE_URL  ?>Afiliasi/cekOrder/' + ref + '/' + id_pelanggan);
    });
</script>