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
                        </tr>
                        <?php
                        $count = 1;
                        $sum = 0;
                        $client_old = 0;
                        $rows = count($data['kas']);
                        $no = 0;
                        $total = 0;
                        foreach ($data['kas'] as $a) {
                            $no += 1;

                            $client = $a['id_client'];
                            $jumlah = $a['jumlah'];
                            $total += $jumlah;

                            if ($client_old == $client) {
                                $count += 1;
                                $sum += $jumlah;
                            }

                            $pelanggan = "Non";
                            foreach ($data['pelanggan'] as $dp) {
                                if ($dp['id_pelanggan'] == $client) {
                                    $pelanggan = $dp['nama'];
                                }
                            }

                        ?>
                            <?php
                            if (($count > 1 && $client_old <> $client)) { ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td align="right">Rp<?= number_format($sum) ?></td>
                                    <td></td>
                                </tr>
                            <?php } ?>

                            <tr>
                                <td align="right">#<?= $a['id_kas'] ?></td>
                                <td><?= strtoupper($pelanggan) ?></td>
                                <td><?= $a['ref_transaksi'] ?></td>
                                <td><?= $a['insertTime'] ?></td>
                                <td align="right">Rp<?= number_format($jumlah) ?></td>
                            </tr>

                            <?php
                            if ($client_old <> $client) {
                                $count = 1;
                                $sum = 0;
                            }

                            if (($count > 1 && $no == $rows)) { ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td align="right">Rp<?= number_format($sum) ?></td>
                                    <td></td>
                                </tr>
                        <?php }
                            $client_old = $client;
                        } ?>
                    </table>

                </div>
            </div>
        </div>
    <?php } ?>
</main>