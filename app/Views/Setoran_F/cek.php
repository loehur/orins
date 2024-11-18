<main>
    <?php $total = 0 ?>
    <?php if (count($data['kas']) > 0) { ?>
        <div class="p-2 ms-3 mt-3 me-3 bg-white">
            <div class="row">
                <div class="col">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th class="text-end">ID</th>
                            <th>Customer</th>
                            <th>Referensi</th>
                            <th>Tanggal</th>
                            <th class="text-end">Jumlah</th>
                            <th></th>
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
</main>