<div style="margin:auto; width: 78mm; margin-top:10mm; font-family: sans-serif;">
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
            <td colspan="2" style="text-align: center;">
                <h2 style="margin:0"><?= strtoupper($this->userData['nama_toko']) ?></h2>
                <?= $this->userData['sub_nama'] ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom: 1px solid;"></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:6px;padding-bottom:4px"><b><?= strtoupper($pelanggan) ?></b><br><?= strtoupper($cs) ?>#<?= $do['ref'] ?><br><?= $do['insertTime'] ?></td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom: 1px solid;"></td>
        </tr>

        <?php
        $total = 0;
        foreach ($data['order'] as $do) {
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
            <tr>
                <td colspan="2">
                    <?php
                    foreach ($detail_arr as $da) { ?>
                        <?= strtoupper($da['detail_name']) . " " ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; border-bottom: 1px solid;border-bottom-style: dotted;">
                    <?= $do['jumlah'] ?>pcs @<?= number_format($do['harga']) ?>
                </td>
                <td style="text-align: right; border-bottom: 1px solid;border-bottom-style: dotted;">
                    <?= number_format($do['harga'] * $do['jumlah']) ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2" style="text-align:right">
                <table style="float: right;">
                    <tr>
                        <td style="text-align: right;">Total:</td>
                        <td style="text-align: right;"><?= number_format($total) ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Dibayar:</td>
                        <td style="text-align: right;"><?= number_format($dibayar) ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><b>Sisa:</b></td>
                        <td style="text-align: right;"><b><?= number_format($sisa) ?></b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom: 1px solid;"></td>
        </tr>
        <tr>
            <td valign=top colspan="2">Riwayat Bayar:<br><?= $showMutasi ?></td>
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