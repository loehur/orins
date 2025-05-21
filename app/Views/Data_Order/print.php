<div style="margin:auto; width: 190mm; font-family: system-ui;">
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
    $jP = "U";
    $countProduksi = count($data['order']) + count($data['paket']);
    $countBarang = count($data['mutasi']);

    if (count($data['order']) > 0) {
        foreach ($data['order'] as $do) {
            $get = $do;
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
    $jenis_pelanggan = $data['pelanggan'][$id_pelanggan]['id_pelanggan_jenis'];
    $id_toko_pelanggan = $data['pelanggan'][$id_pelanggan]['id_toko'];
    $no_pelanggan = $data['pelanggan'][$id_pelanggan]['no_hp'];
    $in_toko = "";
    if ($id_toko_pelanggan <> $this->userData['id_toko']) {
        $in_toko = $this->dToko[$id_toko_pelanggan]['inisial'] . " ";
    }

    switch ($jenis_pelanggan) {
        case 1:
            $jP = "U";
            break;
        case 2:
            $jP = "R";
            break;
        case 3:
            $jP = "O";
            break;
    } ?>

    <table style="width: 100%;">
        <tr>
            <td>
                <small>Pelanggan #<?= $jP ?></small>
                <br><b><span style="color:green;"><?= $in_toko ?></span><?= strtoupper($pelanggan) ?></b> | <span style="color:blue;"><?= $data['mark'] ?></span> #<?= substr($id_pelanggan, -2) ?>
                <br><small><?= $no_pelanggan ?></small>
            </td>
            <td><small>CS</small><br><b><?= strtoupper($cs) ?></b></td>
            <td style="text-align: right;"><small>Tanggal</small><br><b><?= date('d/m/y H:i', strtotime($do['insertTime'])) ?></b></td>
            <td style="text-align: right;">
                <small>No. </small>
                <b><span style="color: green;"><?= substr($do['ref'], -4) ?></span></b>
                <br><small>Ref. <?= $do['ref'] ?></small>
            </td>
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
        $total_charge = 0;

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

                if (isset($data['payment_account'][$dk['pa']]['payment_account'])) {
                    $payment_account = strtoupper($data['payment_account'][$dk['pa']]['payment_account']) . " ";
                } else {
                    if ($dk['metode_mutasi'] == 1) {
                        $payment_account = "Tunai";
                    } else {
                        $payment_account = $note;
                    }
                }

                $show_charge = "";
                if ($dk['charge'] > 0) {
                    $show_charge = "<small>(+" . $dk['charge'] * ($dk['jumlah'] / 100) . ")</small>";
                    $total_charge += ($dk['charge'] * ($dk['jumlah'] / 100));
                }

                if ($dk['status_mutasi'] == 0) {
                    $showMutasi .= "<tr><td><small>* " . $payment_account . "</small></td><td><small>" . $dk['note'] . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . $show_charge . "</small></td><td><small><b>*Checking</b></small></td></tr>";
                } else {
                    if ($dk['metode_mutasi'] <> 1) {
                        $showMutasi .= "<tr><td><small>* " . $payment_account . "</small></td><td><small>" . $dk['note'] . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . $show_charge . "</small></td></tr>";
                    } else {
                        if ($dk['jumlah'] <> $dk['bayar']) {
                            $pembayaran_cash = "(" . number_format($dk['bayar']) . "-" . number_format($dk['kembali']) . ")";
                        } else {
                            $pembayaran_cash = "";
                        }
                        $showMutasi .= "<tr><td><small>* " . $payment_account . "</small></td><td><small>" . date('d/m/y H:i', strtotime($dk['insertTime'])) . "</small></td><td align='right'><small>Rp" . number_format($dk['jumlah']) . " " . $pembayaran_cash . "</small></td></tr>";
                    }
                }
            }
        }

        foreach ($data['diskon'] as $ds) {
            if ($ds['ref_transaksi'] == $data['parse']) {
                if ($ds['cancel'] == 0) {
                    $xtraDiskon += $ds['jumlah'];
                    $showMutasi .= "<tr><td><small>* Extra Diskon </small></td><td><small>" . date('d/m/y H:i', strtotime($ds['insertTime'])) . "</small></td><td align='right'><small>Rp" . number_format($ds['jumlah']) . "</small></tr>";
                }
            }
        }

        if (count($data['paket']) > 0) {
            foreach ($data['paket'] as $pref => $do) {
                $akum_diskon = 0;
                $no += 1;
                $jumlah = $do['qty'];
                $total += $do['harga']; ?>

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
                                    <?php if ($pdo['id_toko'] == $this->userData['id_toko'] || $pdo['id_afiliasi'] == $this->userData['id_toko']) { ?>
                                        <tr>
                                            <td style="padding-left:10px;border-top: 1px solid silver;">
                                                <?php
                                                echo $pdo['jumlah'] . "x - " . $pdo['produk'] . "<br>";
                                                $detail_arr = unserialize($pdo['produk_detail']);
                                                foreach ($detail_arr as $da) { ?>
                                                    <div style="float: left;padding-right: 4px;line-height: 100%;">
                                                        <small><?= ucwords($da['group_name']) ?><br><span style="white-space: nowrap;"><?= strtoupper($da['detail_name']) ?></span></small>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($pdo['note'] <> "") { ?>
                                                    <div style="float: left;padding-right: 4px;line-height: 100%;" class="hilang">
                                                        <small>Note<br><span style="color: red;white-space: nowrap;"><?= $pdo['note'] ?></span></small>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>

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
                            echo "<del>" . number_format($do['harga'] / $jumlah) . "</del><br><small>Disc. " . number_format($akum_diskon) . "</small><br>" . number_format(($do['harga'] / $jumlah)  - $akum_diskon);
                        } else {
                            echo number_format($do['harga'] / $jumlah);
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
                if ($do['id_toko'] == $this->userData['id_toko'] || $do['id_afiliasi'] == $this->userData['id_toko']) {
                    if ($do['paket_ref'] <> "" || $do['cancel'] <> 0) {
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
                    } ?>

                    <tr style="border-bottom: 1px solid silver;">
                        <td style="text-align: right; vertical-align:text-top; padding-right:5px" valign="top">
                            <?= $no ?>.
                        </td>
                        <td style="padding-right: 5px;" valign='top'>
                            <?= $produk ?><br>
                            <?php foreach ($detail_arr as $da) { ?>
                                <div style="float: left;padding-right: 4px;line-height: 100%;">
                                    <small><?= ucwords($da['group_name']) ?></small><br><span style="white-space: nowrap;"><?= strtoupper($da['detail_name']) ?></span>
                                </div>
                            <?php } ?>
                            <?php if ($do['note'] <> "") { ?>
                                <div style="float: left;padding-right: 4px;line-height: 100%;" class="hilang">
                                    <small>Note</small><br>
                                    <span style="color: red;white-space: nowrap;"><?= $do['note'] ?></span>
                                </div>
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
        }
        if (count($data['mutasi']) > 0) {
            foreach ($data['mutasi'] as $do) {
                if ($do['stat'] == 2) {
                    continue;
                }

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

        $sisa = $total - $dibayar - $xtraDiskon;
        if (isset($data['charge']['jumlah'])) {
            $sisa += $data['charge']['jumlah'];
        }

        $showR = "";
        if ($this->userData['id_toko'] == 1) {
            if ($countBarang > 0) {
                $showR = "display:none";
            }
        }
        ?>
    </table>
    <table style="width: 100%;border-collapse:collapse">
        <tr>
            <td colspan="3" style="height: 20px;"></td>
        </tr>
        <tr>
            <td valign=top style="vertical-align:top">
                <?php if ((($dibayar + $xtraDiskon) > 0)) { ?>
                    <small style="<?= $showR ?>">Riwayat Pembayaran:</small>
                    <table style="<?= $showR ?>"><?= $showMutasi ?></table><br style="<?= $showR ?>">
                <?php } ?>
                <small><i>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan</i></small>
            </td>
            <td align="right" style="padding-right: 0; vertical-align:top">
                <table style="padding-right: 0;border-collapse:collapse">
                    <tr>
                        <td style="text-align:right">Total : </td>
                        <td style="text-align:right">
                            <?php if ($countBarang > 0) { ?>
                                Rp<?= number_format($total - $total_disc) ?>
                            <?php } else { ?>
                                Rp<?= number_format($total) ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if ($total_disc > 0) { ?>
                        <tr style="<?= $showR ?>">
                            <td style="text-align:right">Diskon : </td>
                            <td style="text-align:right">-Rp<?= number_format($total_disc) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($total_charge > 0) { ?>
                        <tr style="<?= $showR ?>">
                            <td style="text-align:right">Trx. Charge : </td>
                            <td style="text-align:right">Rp<?= number_format($total_charge) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (isset($data['charge']['jumlah'])) { ?>
                        <tr style="<?= $showR ?>">
                            <td style="text-align:right">Admin Fee : </td>
                            <td style="text-align:right">Rp<?= number_format($data['charge']['jumlah']) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($xtraDiskon > 0) { ?>
                        <tr style="<?= $showR ?>">
                            <td style="text-align:right">Extra Diskon : </td>
                            <td style="text-align:right">-Rp<?= number_format($xtraDiskon) ?></td>
                        </tr>
                    <?php } ?>
                    <tr style="<?= $showR ?>">
                        <td style="text-align:right">Dibayar : </td>
                        <td style="text-align:right">-Rp<?= number_format($dibayar + $total_charge) ?></td>
                    </tr>
                    <tr style="<?= $showR ?>">
                        <td style="text-align:right"><b>Sisa : </b></td>
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

        $(".hilang").dblclick(function() {
            $(this).css("display", "none");
        })

        setTimeout(function() {
            self.close();
        }, 20000);
    });
</script>