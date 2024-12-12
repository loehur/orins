<div class="row p-1">
    <?php foreach ($data['order'] as $ref => $data['order_']) { ?>
        <div class="col-md-12">
            <small>
                <table class="table table-sm mb-0 mt-1 border">
                    <tbody>
                        <?php
                        $no = 0;
                        $total = 0;
                        foreach ($data['order_'] as $key => $do) {
                            $no++;
                            $jumlah = $do['harga'] * $do['jumlah'];
                            $total += $jumlah;
                            $id_order_data = $do['id_order_data'];
                            $id_produk = $do['id_produk'];
                            $detail_arr = unserialize($do['produk_detail']);
                            $detail = "";
                            foreach ($detail_arr as $da) {
                                $detail .= $da['detail_name'] . ", ";
                            }


                            $produk = $data['produk'][$id_produk]['produk'];

                            $divisi_arr = unserialize($do['spk_dvs']);
                            $divisi = [];
                            foreach ($divisi_arr as $key => $dv) {
                                foreach ($this->dDvs as $dv_) {
                                    if ($dv_['id_divisi'] == $key) {
                                        $divisi[$key] = $dv_['divisi'];
                                    }
                                }
                            }

                            if ($no == 1) {
                                foreach ($data['pelanggan'] as $dp) {
                                    if ($dp['id_pelanggan'] == $do['id_pelanggan']) {
                                        $pelanggan = $dp['nama'];
                                    }
                                }

                                $cs = $data['karyawan'][$do['id_penerima']]['nama'];
                        ?>
                                <tr>
                                    <td colspan="5" class="table-light">
                                        <table class="w-100 p-0 m-0">
                                            <tr>
                                                <td><span class="text-danger"><?= substr($ref, -4) ?></span> <b><?= strtoupper($pelanggan) ?></b></td>
                                                <td style="width: 180px;" class="text-end"><small><?= $cs  ?> [<?= substr($do['insertTime'], 2, -3) ?>]</span></small></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php }
                            ?>
                            <tr>
                                <td class="text-success">
                                    <b><small><?= ucwords($produk) ?></small></b>
                                </td>
                                <td class="text-end text-purple"><b><?= number_format($do['jumlah']) ?></b>pcs</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <div class="row">
                                        <?php
                                        foreach ($detail_arr as $da) { ?>
                                            <div class="col-auto pe-1" style="line-height: 100%;">
                                                <?= "<small><u>" . $da['group_name'] . "</u></small><br>" . strtoupper($da['detail_name']) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-auto" style="line-height: 100%;">
                                            <span>
                                                <small>Catatan Utama<br><span class="text-danger"><?= $do['note'] ?></span></small>
                                            </span>
                                        </div>
                                        <div class="col-auto" style="line-height: 100%;">
                                            <span>
                                                <small>Catatan Produksi<br>
                                                    <span class="text-primary">
                                                        <?php
                                                        foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                            if (strlen($ns) > 0) {
                                                                echo "<b>" . $this->model('Arr')->get($this->dDvs, "id_divisi", "divisi", $ks) . ":</b> " . $ns . ", ";
                                                            }
                                                        }
                                                        ?>
                                                    </span>
                                                </small>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-nowrap"><small>
                                        <div class="row">
                                            <?php
                                            foreach ($divisi as $key => $dvs) {
                                                if ($divisi_arr[$key]['status'] == 1) {
                                                    $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_produksi']);
                                                    echo '<div class="col text-center"><i class="fa-solid fa-check text-success"></i> ' . $dvs . " (" . $karyawan . ")</div>";
                                                } else {
                                                    echo '<div class="col text-center"><i class="fa-regular fa-circle"></i> ' . $dvs . "</div>";
                                                }

                                                if ($divisi_arr[$key]['cm'] == 1) {
                                                    if ($divisi_arr[$key]['cm_status'] == 1) {
                                                        $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_cm']);
                                                        echo '<div class="col text-center"><i class="fa-solid text-success fa-check-double"></i> ' . $dvs . " (" . $karyawan . ")</div>";
                                                    } else {
                                                        echo '<div class="col text-center"><i class="fa-regular fa-circle"></i> ' . $dvs . '</div>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="10" class="bg-light"></td>
                            </tr>
                        <?php }
                        ?>
                    </tbody>
                </table>
            </small>
        </div>
    <?php } ?>
</div>