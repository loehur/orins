<main>
    <?php $total = 0 ?>
    <?php if (count($data['kas']) > 0) { ?>
        <div class="p-2 ms-3 mt-3 me-3 bg-white">
            <div class="row">
                <div class="col">
                    <table class="table table-sm mb-0 text-sm">
                        <tr>
                            <th colspan="10" class="text-success">Penjualan Tunai</th>
                        </tr>
                        <?php
                        $no = 0;
                        foreach ($data['kas'] as $a) {
                            $no += 1;

                            $client = $a['id_client'];
                            $jumlah = $a['jumlah'];
                            $total += $jumlah;

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
                                    <?php if ($a['status_mutasi'] == 2) { ?>
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
    <?php if (count($data['pengeluaran']) > 0) { ?>
        <div class="p-2 ms-3 mt-3 me-3 bg-white">
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
                                    <?php if ($a['status_mutasi'] == 2) { ?>
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
                        $total_refund = 0;
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
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</main>