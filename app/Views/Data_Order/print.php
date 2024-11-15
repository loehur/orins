<div style="margin:auto; margin-top:5mm; width: 190mm; font-family: sans-serif;">
    <div class="header">
        <h2 style="margin:0;color:green"><b><?= $this->userData['nama_toko'] ?></b></h2>
        <b><?= $this->userData['sub_nama'] ?></b><br>
        <?= $this->userData['alamat'] ?>
    </div>
    <table style="width: 100%;margin-top:3px; margin-bottom:5px">
        <tr>
            <td style="border-bottom: 2px solid; border-color:red"></td>
            <td style="border-bottom: 2px solid; border-color:orange"></td>
            <td style="border-bottom: 2px solid; border-color:green"></td>
            <td style="border-bottom: 2px solid; border-color:blue"></td>
        </tr>
    </table>

    <?php

    foreach ($data['order'] as $do) {
        foreach ($data['pelanggan'] as $dp) {
            if ($dp['id_pelanggan'] == $do['id_pelanggan']) {
                $pelanggan = $dp['nama'];
            }
        }
        break;
    }

    foreach ($data['karyawan'] as $dp) {
        if ($dp['id_karyawan'] == $do['id_penerima']) {
            $cs = substr($dp['nama'], 0, 2) . "-" . $do['id_penerima'];
        }
    }
    ?>

    <table style="width: 100%;">
        <tr>
            <td><small>Pelanggan</small><br><b><?= strtoupper($pelanggan) ?></b></td>
            <td><small>CS</small><br><b><?= strtoupper($cs) ?></b></td>
            <td style="text-align: right;"><small>Tanggal Order</small><br><b><?= substr($do['insertTime'], 0, 16) ?></b></td>
            <td style="text-align: right;"><small>No. Referensi</small><br><b><?= substr($do['ref'], 0, -4) ?>-<span style="color: green;"><?= substr($do['ref'], -4) ?></span></b></td>
        </tr>
    </table>
    <br>

    <table style="width:100%; border-collapse:collapse">
        <tr style="border-bottom: 1px solid;">
            <th style="text-align: right;">No.</th>
            <th>Keterangan</th>
            <th style="text-align: right;">Qty</th>
            <th style="text-align: right;">Harga</th>
            <th style="text-align: right;">Total</th>
        </tr>
        <?php
        $no = 0;
        $total = 0;
        $total_disc = 0;
        $xtraDiskon = 0;
        $showMutasi = "";
        $dibayar = 0;
        foreach ($data['order'] as $do) {
            $no += 1;
            $akum_diskon = 0;
            $total += $do['harga'] * $do['jumlah'];
            $id_produk = $do['id_produk'];
            $detail_arr = unserialize($do['produk_detail']);
            $listDetail = unserialize($do['detail_harga']);

            $produk = ucwords($do['produk']);

            if ($no == 1) {
                foreach ($data['diskon'] as $ds) {
                    if ($ds['ref_transaksi'] == $do['ref']) {
                        if ($ds['cancel'] == 0) {
                            $xtraDiskon += $ds['jumlah'];
                            $showMutasi .= "<tr><td><small>* Extra Diskon " . $ds['insertTime'] . "</small></td><td align='right'><small>Rp" . number_format($ds['jumlah']) . "</small></tr>";
                        }
                    }
                }

                foreach ($data['kas'] as $dk) {
                    if ($dk['ref_transaksi'] == $do['ref'] && ($dk['status_mutasi'] == 1 || $dk['status_mutasi'] == 0)) {
                        $dibayar += $dk['jumlah'];
                        $note = ($dk['metode_mutasi'] == 1) ? "Cash" : $dk['note'];

                        if ($dk['status_mutasi'] == 0) {
                            $showMutasi .= "<tr><td><small>* " . $note . "</small></td><td><small>" . $dk['insertTime'] . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . "</small></td><td><small><b>*Dalam Pengecekan</b></small></td></tr>";
                        } else {
                            $showMutasi .= "<tr><td><small>* " . $note . "</small></td><td><small>" . $dk['insertTime'] . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . "</small></td></tr>";
                        }
                    }
                }
            }

            $sisa = $total - $dibayar - $xtraDiskon;
            foreach ($listDetail as $kl => $ld_o) {
                $disk = $ld_o['d'];
                $akum_diskon += $disk;
                $total_disc += $disk * $do['jumlah'];
            }
        ?>

            <tr style="border-bottom: 1px solid grey;">
                <td style="text-align: right; vertical-align:text-top;" valign="top">
                    <?= $no ?>.
                </td>
                <td style="padding-right: 5px;" valign='top'>
                    <?= $produk ?><br>
                    <?php
                    foreach ($detail_arr as $da) { ?>
                        <table class="border-bottom" style="float: left;margin:0;padding:0">
                            <tr>
                                <td class="pe-1" nowrap style="padding:0;">
                                    <?= "<small>" . ucwords($da['group_name']) . "</small> <br>" . strtoupper($da['detail_name']) ?>
                                </td>
                            </tr>
                        </table>
                    <?php } ?>
                </td>
                <td style="text-align: right;vertical-align:text-top; padding-left:7px">
                    <?= $do['jumlah'] ?>
                </td>
                <td style="text-align: right;vertical-align:text-top; padding-left:7px;">
                    <?php
                    if ($akum_diskon > 0) {
                        echo "<del>" . number_format($do['harga']) . "</del><br><small>Disc. " . number_format($akum_diskon) . "</small><br>" . number_format($do['harga'] - $akum_diskon);
                    } else {
                        echo number_format($do['harga']);
                    } ?>
                </td>
                <td style="text-align: right;vertical-align:text-top; padding-left:7px">
                    <?php
                    if ($akum_diskon > 0) {
                        echo "<del>" . number_format($do['harga'] * $do['jumlah']) . "</del><br><small>Disc. " . number_format($akum_diskon * $do['jumlah']) . "</small><br>" . number_format(($do['harga'] * $do['jumlah']) - ($akum_diskon * $do['jumlah']));
                    } else {
                        echo number_format($do['harga'] * $do['jumlah']);
                    } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <table style="width: 100%;">
        <tr>
            <td colspan="3" style="height: 20px;"></td>
        </tr>
        <tr>
            <td valign=top><small>Riwayat Pembayaran:</small>
                <table><?= $showMutasi ?></table>
            </td>
            <td align="right">
                <table>
                    <tr>
                        <td style="text-align:right">Total :</td>
                        <td style="text-align:right">
                            Rp<?= number_format($total) ?>
                        </td>
                    </tr>
                    <?php if ($total_disc > 0) { ?>
                        <tr>
                            <td style="text-align:right">Diskon :</td>
                            <td style="text-align:right">Rp<?= number_format($total_disc) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($xtraDiskon > 0) { ?>
                        <tr>
                            <td style="text-align:right">Extra Diskon :</td>
                            <td style="text-align:right">Rp<?= number_format($xtraDiskon) ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td style="text-align:right">Dibayar :</td>
                        <td style="text-align:right">Rp<?= number_format($dibayar) ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right"><b>Sisa :</b></td>
                        <td style="text-align:right"><b>Rp<?= number_format($sisa - $total_disc) ?></b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        window.print();

        setTimeout(function() {
            self.close();
        }, 20000);

    });
</script>