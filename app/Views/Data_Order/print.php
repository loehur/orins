<div style="margin:auto; margin-top:5mm; width: 190mm; font-family: system-ui;">
    <div class="header">
        <h2 style="margin:0;color:<?= $this->userData['color'] ?>"><b><?= $this->userData['nama_toko'] ?></b></h2>
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
    if (count($data['order']) > 0) {
        foreach ($data['order'] as $do) {
            $get = $do;
            $pelanggan = $data['pelanggan'][$do['id_pelanggan']]['nama'];
            $id_pelanggan = $do['id_pelanggan'];
            $cs_name = $data['karyawan'][$do['id_penerima']]['nama'];
            $cs = substr($cs_name, 0, 2) . "-" . $do['id_penerima'];
            break;
        }
    } else {
        if (count($data['paket']) > 0) {
            foreach ($data['paket'] as $pref => $do) {
                if (isset($do['order'])) {
                    foreach ($do['order'] as $pdo) {
                        $get = $pdo;
                        $id_pelanggan = $pdo['id_pelanggan'];
                        $cs_name = $data['karyawan'][$pdo['id_penerima']]['nama'];
                        $cs = substr($cs_name, 0, 2) . "-" . $pdo['id_penerima'];
                        break;
                    }
                    break;
                }
                if (isset($do['barang'])) {
                    foreach ($do['barang'] as $pdo) {
                        $get = $pdo;
                        $id_pelanggan = $pdo['id_target'];
                        $cs_name = $data['karyawan'][$pdo['cs_id']]['nama'];
                        $cs = substr($cs_name, 0, 2) . "-" . $pdo['cs_id'];
                        break;
                    }
                    break;
                }
            }
        } else {
            foreach ($data['mutasi'] as $do) {
                $get = $do;
                $id_pelanggan = $do['id_target'];
                $cs_name = $data['karyawan'][$do['cs_id']]['nama'];
                $cs = substr($cs_name, 0, 2) . "-" . $do['cs_id'];
                break;
            }
        }
    }

    $do = $get;

    $pelanggan = $data['pelanggan'][$id_pelanggan]['nama'];
    $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];
    $in_toko = "";
    if ($id_toko_pelanggan <> $this->userData['id_toko']) {
        $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
    }
    ?>

    <table style="width: 100%;">
        <tr>
            <td><small>Pelanggan</small><br><b><span style="color:green;"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></b> | <span style="color:blue;"><?= $data['mark'] ?></span> #<?= substr($id_pelanggan, -2) ?></td>
            <td><small>CS</small><br><b><?= strtoupper($cs) ?></b></td>
            <td style="text-align: right;"><small>Tanggal</small><br><b><?= date('d/m/y H:i', strtotime($do['insertTime'])) ?></b></td>
            <td style="text-align: right;"><small>No. Referensi</small><br><b><?= substr($do['ref'], 0, -5) ?>-<span style="color: green;"><?= substr($do['ref'], -4) ?></span></b></td>
        </tr>
    </table>
    <br>

    <table style="width:100%; border-collapse:collapse">
        <tr style="border-bottom: 1px solid silver;">
            <th style="text-align: right;">No.</th>
            <th style="text-align:left">Keterangan</th>
            <th style="text-align: right;">Qty</th>
            <th style="text-align: right;">Harga</th>
            <th style="text-align: right;">Total</th>
        </tr>
        <?php
        $no = 0;

        $total = 0;
        $dibayar = 0;

        $total_disc = 0;
        $xtraDiskon = 0;
        $showMutasi = "";

        foreach ($data['kas'] as $dk) {
            if ($dk['ref_transaksi'] == $do['ref'] && ($dk['status_mutasi'] == 1 || $dk['status_mutasi'] == 0)) {
                $dibayar += $dk['jumlah'];
                switch ($dk['metode_mutasi']) {
                    case 1;
                        $note = 'Cash';
                        break;
                    case 2:
                    case 3:
                        $note = $dk['note'];
                        break;
                    case 4:
                        $note = 'Saldo';
                        break;
                }


                if ($dk['status_mutasi'] == 0) {
                    $showMutasi .= "<tr><td><small>* " . $note . "</small></td><td><small>" . date('d/m/y H:i', strtotime($dk['insertTime'])) . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . "</small></td><td><small><b>*Checking</b></small></td></tr>";
                } else {
                    if ($dk['metode_mutasi'] <> 1) {
                        $showMutasi .= "<tr><td><small>* " . $note . "</small></td><td><small>" . date('d/m/y H:i', strtotime($dk['insertTime'])) . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . "</small></td></tr>";
                    } else {
                        if ($dk['jumlah'] <> $dk['bayar']) {
                            $pembayaran_cash = "(" . number_format($dk['bayar']) . "-" . number_format($dk['kembali']) . ")";
                        } else {
                            $pembayaran_cash = "";
                        }
                        $showMutasi .= "<tr><td><small>* " . $note . "</small></td><td><small>" . date('d/m/y H:i', strtotime($dk['insertTime'])) . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . " " . $pembayaran_cash . "</small></td></tr>";
                    }
                }
            }
        }

        foreach ($data['diskon'] as $ds) {
            if ($ds['ref_transaksi'] == $data['parse']) {
                if ($ds['cancel'] == 0) {
                    $xtraDiskon += $ds['jumlah'];
                    $showMutasi .= "<tr><td><small>* Extra Diskon " . $ds['insertTime'] . "</small></td><td align='right'><small>Rp" . number_format($ds['jumlah']) . "</small></tr>";
                }
            }
        }

        if (count($data['paket']) > 0) {
            foreach ($data['paket'] as $pref => $do) {
                $akum_diskon = 0;
                $no += 1;
                $jumlah = 1;
                $total += ($jumlah * $do['harga']); ?>

                <tr style="border-bottom: 1px solid silver;">
                    <td style="text-align: right; vertical-align:text-top; padding-right:5px" valign="top">
                        <?= $no ?>.
                    </td>
                    <td style="padding-right: 5px;" valign='top'>
                        Paket <?= $data['list_paket'][$pref]['nama'] ?><br>
                        <table style="font-size: 13;">
                            <?php
                            if (isset($do['order'])) {
                                foreach ($do['order'] as $pdo) { ?>
                                    <tr>
                                        <td style="padding-left:10px;border-top: 1px solid silver;">
                                            <?php
                                            echo $pdo['jumlah'] . "x - " . $pdo['produk'] . "<br>";
                                            $detail_arr = unserialize($pdo['produk_detail']);
                                            foreach ($detail_arr as $da) { ?>
                                                <table class="border-bottom" style="float: left;margin:0;padding:0;font-size: 13;">
                                                    <tr>
                                                        <td class="pe-1" nowrap style="padding:0;">
                                                            <?= "<small>" . ucwords($da['group_name']) . "</small> <br>" . strtoupper($da['detail_name']) ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            <?php } ?>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>

                            <?php
                            if (isset($do['barang'])) {
                                foreach ($do['barang'] as $pdo) {
                                    $dp = $data['barang'][$pdo['id_barang']] ?>
                                    <tr>
                                        <td style="padding-left:10px;border-top: 1px solid silver;">
                                            <?= $pdo['qty'] . "x - " . trim($dp['brand'] . " " . $dp['model']) . "<br>" ?>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </table>
                    </td>
                    <td style="text-align: right;vertical-align:text-top; padding-left:7px">
                        <?= $jumlah ?>
                    </td>
                    <td style="text-align: right;vertical-align:text-top; padding-left:7px;">
                        <?php
                        if ($akum_diskon > 0) {
                            echo "<del>" . number_format($do['harga']) . "</del><br><small>Disc. " . number_format($akum_diskon) . "</small><br>" . number_format($do['harga']  - $akum_diskon);
                        } else {
                            echo number_format($do['harga']);
                        } ?>
                    </td>
                    <td style="text-align: right;vertical-align:text-top; padding-left:7px">
                        <?php
                        if ($akum_diskon > 0) {
                            echo "<del>" . number_format($do['harga']) . "</del><br><small>Disc. " . number_format($akum_diskon) . "</small><br>" . number_format(($do['harga'] * $do['qty'])) - ($akum_diskon);
                        } else {
                            echo number_format($do['harga']);
                        } ?>
                    </td>
                </tr>

            <?php }
        }

        if (count($data['order']) > 0) {
            foreach ($data['order'] as $do) {
                if ($do['paket_ref'] <> "") {
                    continue;
                }
                $no += 1;
                $akum_diskon = 0;
                $total += (($do['harga'] * $do['jumlah']));
                $id_produk = $do['id_produk'];
                $detail_arr = unserialize($do['produk_detail']);
                $listDetail = unserialize($do['detail_harga']);
                $produk = ucwords($do['produk']);

                foreach ($listDetail as $kl => $ld_o) {
                    $disk = $ld_o['d'];
                    $akum_diskon += $disk;
                    $total_disc += $disk * $do['jumlah'];
                }
            ?>

                <tr style="border-bottom: 1px solid silver;">
                    <td style="text-align: right; vertical-align:text-top; padding-right:5px" valign="top">
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
                            echo "<del>" . number_format(($do['harga'] + $do['margin_paket'])) . "</del><br><small>Disc. " . number_format($akum_diskon) . "</small><br>" . number_format(($do['harga'] + $do['margin_paket']) - $akum_diskon);
                        } else {
                            echo number_format(($do['harga'] + $do['margin_paket']));
                        } ?>
                    </td>
                    <td style="text-align: right;vertical-align:text-top; padding-left:7px">
                        <?php
                        if ($akum_diskon > 0) {
                            echo "<del>" . number_format(($do['harga'] + $do['margin_paket']) * $do['jumlah']) . "</del><br><small>Disc. " . number_format($akum_diskon * $do['jumlah']) . "</small><br>" . number_format((($do['harga'] + $do['margin_paket']) * $do['jumlah']) - ($akum_diskon * $do['jumlah']));
                        } else {
                            echo number_format(($do['harga'] + $do['margin_paket']) * $do['jumlah']);
                        } ?>
                    </td>
                </tr>
            <?php }
        }
        if (count($data['mutasi']) > 0) {
            foreach ($data['mutasi'] as $do) {
                $akum_diskon = $do['diskon'];
                $no += 1;
                $jumlah = $do['qty'];
                $dp = $data['barang'][$do['id_barang']];
                $total += (($jumlah * $do['harga_jual'])); ?>
                <tr style="border-bottom: 1px solid silver;">
                    <td style="text-align: right; vertical-align:text-top; padding-right:5px" valign="top">
                        <?= $no ?>.
                    </td>
                    <td style="padding-right: 5px;" valign='top'>
                        <?= trim($dp['brand'] . " " . $dp['model']) ?><?= trim($dp['product_name']) ?><br>
                        <span style="font-size: 13;"><?= $do['sn'] <> "" ? "SN: " . $do['sn'] : "" ?></span>
                    </td>
                    <td style="text-align: right;vertical-align:text-top; padding-left:7px">
                        <?= $do['qty'] ?>
                    </td>
                    <td style="text-align: right;vertical-align:text-top; padding-left:7px;">
                        <?php
                        if ($akum_diskon > 0) {
                            echo "<del>" . number_format(($do['harga_jual'] + $do['margin_paket'])) . "</del><br><small>Disc. " . number_format($akum_diskon) . "</small><br>" . number_format(($do['harga_jual'] + $do['margin_paket']) - $akum_diskon);
                        } else {
                            echo number_format(($do['harga_jual'] + $do['margin_paket']));
                        } ?>
                    </td>
                    <td style="text-align: right;vertical-align:text-top; padding-left:7px">
                        <?php
                        if ($akum_diskon > 0) {
                            $total_disc += $akum_diskon * $do['qty'];
                            echo "<del>" . number_format(($do['harga_jual'] * $do['qty']) + $do['margin_paket']) . "</del><br><small>Disc. " . number_format($akum_diskon * $do['qty']) . "</small><br>" . number_format((($do['harga_jual'] * $do['qty']) + $do['margin_paket']) - ($akum_diskon * $do['qty']));
                        } else {
                            echo number_format(($do['harga_jual'] * $do['qty']) + $do['margin_paket']);
                        } ?>
                    </td>
                </tr>
        <?php }
        }

        $sisa = $total - $dibayar - $xtraDiskon; ?>
    </table>
    <table style="width: 100%;border-collapse:collapse">
        <tr>
            <td colspan="3" style="height: 20px;"></td>
        </tr>
        <tr>
            <td valign=top><small>Riwayat Pembayaran:</small>
                <table><?= $showMutasi ?></table>
            </td>
            <td align="right" style="padding-right: 0;">
                <table style="padding-right: 0;border-collapse:collapse">
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

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        window.print();

        setTimeout(function() {
            self.close();
        }, 20000);
    });
</script>