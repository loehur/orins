<?php $parse = $data['parse'] ?>
<div class="row p-1">
    <?php foreach ($data['order'] as $ref => $data['order_']) { ?>
        <div class="col-md-12">
            <small>
                <table class="table table-sm mb-0 mt-1 border text-sm">
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
                                                <td class="text-sm"><span class="text-danger"><?= substr($ref, -4) ?></span> <b><?= strtoupper($pelanggan) ?></b></td>
                                                <td style="width: 180px;" class="text-end"><small><?= $cs  ?> <?= substr($do['insertTime'], 2, -3) ?></span></small></td>
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
                                </td>
                            </tr>
                            <?php
                            $spkR = [];
                            if (strlen($do['pending_spk']) > 1) {
                                $spkR = unserialize($do['pending_spk']);
                            }

                            if (strlen($do['note']) > 0 || strlen($do['note_spk']) > 0) { ?>
                                <tr>
                                    <td colspan="10">
                                        <?php
                                        if (strlen($do['note']) > 0) { ?>
                                            <span class='text-danger text-sm'><i class='fa-solid fa-circle-exclamation'></i> <?= $do['note'] ?></span>
                                        <?php } ?>
                                        <span class="text-sm">
                                            <?php foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                if ($ks == $parse && strlen($ns) > 0) {
                                                    echo "<br><span class='text-primary text-sm'><i class='fa-solid fa-circle-exclamation'></i> " . $ns . "</span>";
                                                }
                                                if ($ks == $parse) {
                                                    if (isset($spkR[$ks])) {
                                                        $pendReady = explode("-", $spkR[$ks]); ?>
                                                        <small><span class="badge bg-<?= $pendReady[1] == 'r' ? 'success' : 'danger' ?>"><?= $data['spk_pending'][$pendReady[0]][$pendReady[1]] ?></span></small>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
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
                                <td colspan="10" class="bg-secondary"></td>
                            </tr>
                        <?php }
                        ?>
                    </tbody>
                </table>
            </small>
        </div>
    <?php } ?>
</div>