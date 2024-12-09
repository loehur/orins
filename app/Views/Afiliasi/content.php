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
        }

    ?>
        <div class="p-2 ms-3 mt-3 me-3 bg-white">
            <div class="row">
                <div class="col">
                    <div class="row border-bottom">
                        <div class="col ms-2">
                            <span><b>Antrian Pengecekan Afiliasi</b></span>
                        </div>
                    </div>
                    <small>
                        <table class="table table-sm table-hover mt-2">
                            <tr>
                                <th>Toko/Ref</th>
                                <th>Customer</th>
                                <th class="text-end">Jumlah/Via</th>
                                <th colspan="2" class="text-center"></th>
                            </tr>
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


                                $pelanggan = "Non";
                                foreach ($data['pelanggan'] as $dp) {
                                    if ($dp['id_pelanggan'] == $client) {
                                        $pelanggan = $dp['nama'];
                                    }
                                }

                            ?>
                                <tr>
                                    <td>
                                        <span class="text-purple"><?= $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $a['id_toko']) ?></span><br>
                                        <small><?= $a['ref_transaksi'] ?></small>
                                    </td>
                                    <td>#<?= $a['id_kas'] ?><br><?= strtoupper($pelanggan) ?></td>
                                    <td align="right" class="pe-2">Rp<?= number_format($jumlah) ?><br><?= $a['note'] ?></td>
                                    <td align="left">
                                        <button data-id="<?= $id ?>" data-val="1" class="action btn btn-sm btn-outline-success border-0">Verify</button>
                                    </td>
                                    <td align="right">
                                        <button data-id="<?= $id ?>" data-val="2" class="action btn btn-sm btn-outline-danger border-0">Reject</button>
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
                                            <td></td>
                                            <td class="pe-0" align="right"><button data-id="<?= $id_multi ?>" data-val="1" class="border-0 actionMulti btn btn-sm btn-outline-primary">Multi Verify - <b>Rp<?= number_format($total) ?></b></button></td>
                                            <td></td>
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
        </div>
    <?php } ?>
    <div class="p-2 ms-3 mt-3 me-3 bg-white">
        <div class="row">
            <div class="col">
                <div class="row border-bottom">
                    <div class="col ms-2">
                        <span><b>Riwayat Transaksi Terkonfirmasi</b></span> <small>(Last 20)</small>
                    </div>
                </div>
                <small>
                    <table class="table table-sm table-hover mt-2">
                        <tr>
                            <th>Toko/Ref</th>
                            <th>Customer</th>
                            <th class="text-end">Jumlah/Via</th>
                            <th class="text-end">Updated</th>
                            <th class="text-end">Re-Action</th>
                        </tr>
                        <?php
                        $no = 0;
                        foreach ($data['kas_done'] as $a) {
                            $no += 1;
                            $id =  $a['id_kas'];

                            $client = $a['id_client'];
                            $jumlah = $a['jumlah'];
                            $ref = $a['ref_transaksi'];

                            $pelanggan = "Non";
                            foreach ($data['pelanggan'] as $dp) {
                                if ($dp['id_pelanggan'] == $client) {
                                    $pelanggan = $dp['nama'];
                                }
                            }

                        ?>
                            <tr>
                                <td>
                                    <span class="text-purple"><?= $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $a['id_toko']) ?></span><br>
                                    <span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalCek" class="cekTrx" data-ref="<?= $a['ref_transaksi'] ?>"><small><?= $a['ref_transaksi'] ?></small></span>
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
                                            echo '<span><i class="text-danger fa-solid fa-xmark"></i> Rejected</span>';
                                            break;
                                    }
                                    ?>
                                    <br>
                                    <?= $a['updateTime'] ?>
                                </td>
                                <?php
                                $tuntas = $this->db(0)->get_where_row("order_data", "ref = '" . $ref . "'")['tuntas'];
                                switch ($a['status_mutasi']) {
                                    case 1:
                                ?>
                                        <td align="right">
                                            <button data-id="<?= $id ?>" data-val="2" class="action btn btn-sm btn-outline-secondary px-2 py-0 border-0">Reject</button>
                                            <br>
                                            <small><span class="pe-2"><?= $a['note_office'] ?></span></small>
                                        </td>
                                    <?php break;
                                    default: ?>
                                        <td align="right">
                                            <button data-id="<?= $id ?>" data-val="1" class="action btn btn-sm btn-outline-secondary px-2 py-0 border-0">Verify</button>
                                            <br>
                                            <small><span class="pe-2"><?= $a['note_batal'] ?></span></small>
                                        </td>
                                <?php break;
                                }
                                ?>
                            </tr>
                        <?php } ?>
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
        var note = prompt("Catatan", "");
        if (note === null) {
            return;
        }
        var id_ = $(this).attr("data-id");
        var value = $(this).attr("data-val");
        $.ajax({
            url: "<?= PV::BASE_URL . $data['_c'] ?>/action",
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
        var note = prompt("Catatan", "");
        if (note === null) {
            return;
        }

        var id_ = $(this).attr("data-id");
        var value = $(this).attr("data-val");
        $.ajax({
            url: "<?= PV::BASE_URL . $data['_c'] ?>/actionMulti",
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

    $('span.cekTrx').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cekOrder").load('<?= PV::BASE_URL . $data['_c'] ?>/cekOrder/' + ref);
    });
</script>