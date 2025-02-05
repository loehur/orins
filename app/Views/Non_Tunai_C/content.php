<main>
    <?php if (count($data['kas']) > 0) {
        $rekap = [];
        foreach ($data['kas'] as $a) {
            $client = $a['id_client'];
            $jumlah = $a['jumlah'];
            if (isset($rekap[$client])) {
                $rekap[$client]["c"] += 1;
                $rekap[$client]["t"] += $jumlah;
            } else {
                $rekap[$client]["c"] = 1;
                $rekap[$client]["t"] = $jumlah;
            }
        } ?>

        <div class="row mx-0">
            <div class="col">
                <span><b>Antrian Pengecekan Non Tunai</b></span>
                <small>
                    <table class="text-sm table-hover table table-sm">
                        <?php
                        $no = 0;
                        $id_multi = "";
                        foreach ($data['kas'] as $a) {
                            $no += 1;
                            $id =  $a['id_kas'];

                            $client = $a['id_client'];
                            $jumlah = $a['jumlah'];
                            $count = $rekap[$client]["c"];
                            $total = $rekap[$client]['t'];

                            if ($no == $count) {
                                $id_multi .= $id;
                            } else {
                                $id_multi .= $id . "_";
                            }

                            if (isset($data['payment_account'][$a['pa']]['payment_account'])) {
                                $payment_account = $data['payment_account'][$a['pa']]['payment_account'] . " ";
                            } else {
                                $payment_account = "";
                            }

                            $pelanggan = "Non";
                            foreach ($data['pelanggan'] as $dp) {
                                if ($dp['id_pelanggan'] == $client) {
                                    $pelanggan = $dp['nama'];
                                }
                            }

                        ?>
                            <tr>
                                <td>#<?= $a['id_kas'] ?>
                                    <?php if ($a['jenis_transaksi'] == 2) { ?>
                                        <small>Topup Deposit</small>
                                    <?php } else { ?>
                                        <small>Non Tunai</small>
                                    <?php } ?>
                                    <br><?= strtoupper($pelanggan) ?>
                                </td>
                                <td align="right">Rp<?= number_format($jumlah) ?><br><?= strtoupper($payment_account) . $a['note'] ?></td>
                                <td align="right" class="align-top">
                                    <button data-id="<?= $id ?>" data-val="2" class="action btn btn-sm btn-outline-danger border-0">Cancel</button>
                                </td>
                            </tr>
                            <?php
                            if (($no == $count)) {
                                $no = 0; ?>
                                <?php
                                if ($count > 1) {
                                ?>
                                    <tr>
                                        <td></td>
                                        <td class="" align="right"><b>Rp<?= number_format($total) ?></b></td>
                                        <td></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="10" class="bg-secondary"></td>
                                </tr>
                        <?php
                                $id_multi = "";
                            }
                        } ?>
                    </table>
                </small>
            </div>
        </div>
    <?php } ?>
    <div class="row mx-0">
        <div class="col">
            <span><b>Riwayat Pembayaran Terkonfirmasi</b></span>
            <small>
                <table class="table table-sm text-sm table-hover mt-2">
                    <?php
                    $no = 0;
                    foreach ($data['kas_done'] as $a) {
                        $no += 1;
                        $id =  $a['id_kas'];

                        $client = $a['id_client'];
                        $jumlah = $a['jumlah'];

                        $pelanggan = "Non";
                        foreach ($data['pelanggan'] as $dp) {
                            if ($dp['id_pelanggan'] == $client) {
                                $pelanggan = $dp['nama'];
                            }
                        }

                        if (isset($data['payment_account'][$a['pa']]['payment_account'])) {
                            $payment_account = $data['payment_account'][$a['pa']]['payment_account'] . " ";
                        } else {
                            $payment_account = "";
                        } ?>

                        <tr>
                            <td>#<?= $a['id_kas'] ?>
                                <?php if ($a['jenis_transaksi'] == 2) { ?>
                                    <small>Topup Deposit</small>
                                <?php } else { ?>
                                    <small>Non Tunai</small>
                                <?php } ?>
                                <br><?= strtoupper($pelanggan) ?>
                            </td>
                            <td align="right" class="pe-2">Rp<?= number_format($jumlah) ?><br><?= strtoupper($payment_account) . $a['note'] ?></td>
                            <td class="text-end">
                                <?php
                                switch ($a['status_mutasi']) {
                                    case 1:
                                        echo '<span class="text-success"><i class="fa-solid fa-check-to-slot"></i> Verified</span>';
                                        break;
                                    default:
                                        echo '<span><i class="fa-solid fa-xmark"></i> Rejected</span>';
                                        break;
                                }
                                ?>
                                <br>
                                <?= $a['updateTime'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </small>
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
    $('span.cekTrx').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cekOrder").load('<?= PV::BASE_URL . $data['c_'] ?>/cekOrder/' + ref);
    });

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
            url: "<?= PV::BASE_URL ?>Non_Tunai_C/action",
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
</script>