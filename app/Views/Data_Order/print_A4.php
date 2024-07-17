<div style="margin:auto; width: 190mm; margin-top:10mm">
    <div class="header">
        <h2 style="margin:0"><?= $this->userData['nama_toko'] ?></h2>
        <?= $this->userData['sub_nama'] ?><br>
        <?= $this->userData['alamat'] ?>
    </div>
    <hr>

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
            <td>Pelanggan<br><b><?= strtoupper($pelanggan) ?></b></td>
            <td>CS<br><b><?= strtoupper($cs) ?></b></td>
            <td style="text-align: right;">Tanggal Order<br><b><?= substr($do['insertTime'], 0, 15) ?></b></td>
            <td style="text-align: right;">No. Referensi<br><b><?= $do['ref'] ?></b></td>
        </tr>
    </table>
    <br>

    <table style="width:100%; border-collapse:collapse">
        <tr style="border-bottom: 1px solid;">
            <td style="text-align: right;">No.</td>
            <td>Keterangan</td>
            <td style="text-align: right;">Qty</td>
            <td style="text-align: right;">Harga</td>
            <td style="text-align: right;">Total</td>
        </tr>
        <?php
        $no = 0;
        $total = 0;
        foreach ($data['order'] as $do) {
            $no += 1;
            $total += $do['harga'] * $do['jumlah'];
            $id_produk = $do['id_produk'];
            $detail_arr = unserialize($do['produk_detail']);

            foreach ($this->dProduk as $dp) {
                if ($dp['id_produk'] == $id_produk) {
                    $produk = $dp['produk'];
                }
            }

            $dibayar = 0;
            $showMutasi = "";
            foreach ($data['kas'] as $dk) {
                if ($dk['ref_transaksi'] == $do['ref']) {
                    $dibayar += $dk['jumlah'];
                    $showMutasi .= "Rp" . number_format($dk['jumlah']) . " (" . $dk['insertTime'] . ")<br>";
                }
            }

            $sisa = $total - $dibayar;
        ?>
            <tr style="border-bottom: 1px solid;">
                <td style="text-align: right;">
                    <?= $no ?>
                </td>
                <td>
                    <table class="border-bottom">
                        <tr>
                            <?php
                            foreach ($detail_arr as $da) { ?>
                                <td class="pe-1" nowrap>
                                    <?= "<small>" . $da['group_name'] . "</small> <br>" . strtoupper($da['detail_name']) ?>
                                </td>
                            <?php } ?>
                        </tr>
                    </table>
                </td>
                <td style="text-align: right;">
                    <?= $do['jumlah'] ?>pcs
                </td>
                <td style="text-align: right;">
                    <?= number_format($do['harga']) ?>
                </td>
                <td style="text-align: right;">
                    <?= number_format($do['harga'] * $do['jumlah']) ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td style="height: 20px;"></td>
        </tr>
        <tr>
            <td valign=top colspan="2" rowspan="3">Riwayat Bayar:<br><?= $showMutasi ?></td>
            <td colspan="2" style="text-align:right">Total:</td>
            <td style="text-align:right"><?= number_format($total) ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align:right">Dibayar:</td>
            <td style="text-align:right"><?= number_format($dibayar) ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align:right"><b>Sisa:</b></td>
            <td style="text-align:right"><b><?= number_format($sisa) ?></b></td>
        </tr>
    </table>
    <br>
    Extra Note:
    <div style="width: 100%; border: 1px solid; height:50px; border-radius:2mm"></div>
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