<?php $parse = $data['parse']; ?>
<main class="container">
    <div class="row mx-2">
        <div class="col pe-0 ps-0" style="min-width: 150px; max-width: 160px">
            <input class="form-control" name="tgl" value="<?= $data['date'] ?>" type="date" />
        </div>

        <div class="col pt-auto mt-auto pe-0">
            <button type="submit" class="cek pt-2 btn btn-sm btn-primary">Cek Order</button>
        </div>
    </div>
    <div class="row mx-2 mt-3">
        <?php
        for ($x = 1; $x <= 2; $x++) { ?>
            <div class="col ps-0 pe-1">
                <?php foreach ($data['order'][$x] as $ref => $data['order_']) { ?>
                    <table class="table table-sm shadow-sm mb-2 border" style="min-width: 340px;">
                        <?php
                        $no = 0;
                        $total = 0;
                        foreach ($data['order_'] as $key => $do) {
                            $no++;
                            $spkDone = 0;
                            $spkCount = 0;
                            $id_order_data = $do['id_order_data'];
                            $id_produk = $do['id_produk'];
                            $produk = ucwords($do['produk']);
                            $detail_arr = unserialize($do['produk_detail']);
                            $detail = "";
                            foreach ($detail_arr as $da) {
                                $detail .= $da['detail_name'] . ", ";
                            }

                            $divisi_arr = unserialize($do['spk_dvs']);
                            $divisi = [];
                            foreach ($divisi_arr as $key => $dv) {
                                foreach ($this->dDvs as $dv_) {
                                    if ($dv_['id_divisi'] == $key) {
                                        $divisi[$key] = $dv_['divisi'];
                                    }
                                }
                            }

                            foreach ($divisi as $key => $dvs) {
                                if ($key == $parse) {
                                    $spkCount += 1;
                                    if ($divisi_arr[$key]['status'] == 1) {
                                        $spkDone += 1;
                                    }
                                    if ($divisi_arr[$key]['cm'] == 1) {
                                        $spkCount += 1;
                                        if ($divisi_arr[$key]['cm_status'] == 1) {
                                            $spkDone += 1;
                                        }
                                    }
                                }
                            }

                            $classTR = "";
                            if ($spkDone == $spkCount) {
                                $classTR = "table-info";
                            } else {
                                if ($spkDone == 1) {
                                    $classTR = "table-warning";
                                }
                            }

                            if ($no == 1) {
                                foreach ($data['pelanggan'] as $dp) {
                                    if ($dp['id_pelanggan'] == $do['id_pelanggan']) {
                                        $pelanggan = $dp['nama'];
                                    }
                                }

                                $cs = $data['karyawan_all'][$do['id_penerima']]['nama'];

                                $cs_af = "";
                                if ($do['id_user_afiliasi'] <> 0) {
                                    $cs_af = "/" . $data['karyawan_all'][$do['id_user_afiliasi']]['nama'];
                                }
                        ?>
                                <tr>
                                    <td colspan="5" class="table-secondary">
                                        <table class="w-100 p-0 m-0">
                                            <tr>
                                                <td><span><?= substr($ref, -4) ?></span> <b><?= strtoupper($pelanggan) ?></b></td>
                                                <td valign="top" class="text-end"><small><?= $cs . $cs_af  ?> <?= substr($do['insertTime'], 2, -3) ?></span></small></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php }
                            ?>
                            <tr class="<?= $classTR ?>">
                                <td>
                                    <table class="float-start">
                                        <tr>
                                            <td class="pe-1">
                                                <span class="text-success"><?= $produk ?></span><br>
                                                <?php
                                                foreach ($detail_arr as $da) { ?>
                                                    <?= strtoupper($da['detail_name']) ?>
                                                <?php } ?>
                                            </td>
                                            <td valign="bottom" class="text-end text-purple pe-2" style="width:40px"><b><?= number_format($do['jumlah']) ?></b>pcs</td>
                                        </tr>
                                        <?php
                                        if (strlen($do['note']) > 0 || strlen($do['note_spk']) > 0) { ?>
                                            <tr>
                                                <td colspan="10">
                                                    <?php if (strlen($do['note']) > 0) { ?>
                                                        <span class='text-danger'><i class='fa-solid fa-circle-exclamation'></i> <?= $do['note'] ?></span>
                                                    <?php } ?>
                                                    <?php foreach (unserialize($do['note_spk']) as $ks => $ns) {
                                                        if ($ks == $parse && strlen($ns) > 0) {
                                                            echo "<br><span class='text-primary'><i class='fa-solid fa-circle-exclamation'></i> " . $ns . "</span>";
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                    <table class="float-end">
                                        <tr>
                                            <?php
                                            foreach ($divisi as $key => $dvs) {
                                                if ($key == $parse) {

                                                    if ($divisi_arr[$key]['status'] == 0) {
                                                        if ($do['id_afiliasi'] == 0) { ?>
                                                            <?php if (!str_contains($do['spk_lanjutan'], "D-" . $parse . "#")) { ?>
                                                                <td style="cursor: pointer;" class="pe-2 push" data-id="<?= $id_order_data ?>" data-val="<?= $parse ?>"><i class="fa-regular fa-circle-right"></i> Push</td>
                                                            <?php } else { ?>
                                                                <td class="pe-2 text-danger">Pushed</td>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>

                                            <?php echo "<td class='pe-2'>";
                                                    if ($divisi_arr[$key]['status'] == 1) {
                                                        $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_produksi']);
                                                        echo '<i class="fa-solid fa-check text-success"></i> ' . $karyawan;
                                                    } else {
                                                        if ($do['id_afiliasi'] == 0 || $do['id_afiliasi'] == $this->userData['id_toko']) {
                                                            echo '<span class="done" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#done" data-id="' . $id_order_data . '" data-mode="1"> <i class="fa-regular fa-circle"></i> Tahap 1';
                                                        }
                                                    }
                                                    echo "</td>";
                                                    if ($divisi_arr[$key]['cm'] == 1) {
                                                        echo "<td class='pe-2'>";
                                                        if ($divisi_arr[$key]['cm_status'] == 1) {
                                                            $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_cm']);
                                                            echo '<i class="fa-solid text-success fa-check-double"></i> ' . $karyawan;
                                                        } else {
                                                            if ($do['id_afiliasi'] == 0 || $do['id_afiliasi'] == $this->userData['id_toko']) {
                                                                echo '<span class="done" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#done" data-id="' . $id_order_data . '" data-mode="2"> <i class="fa-regular fa-circle"></i> Tahap 2';
                                                            }
                                                        }
                                                        echo "</td>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    </small>

    <form action="<?= PV::BASE_URL; ?>SPK_C/done/<?= $parse ?>" method="POST">
        <div class="modal" id="done">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row mb-2">
                                <div class="col px-1">
                                    <label class=" form-label">Karyawan</label>
                                    <input type="hidden" name="id">
                                    <input type="hidden" name="mode">
                                    <select class="border tize" name="id_karyawan" required>
                                        <option></option>
                                        <?php foreach ($data['karyawan'] as $k) {
                                            if ($k['id_toko'] == $this->userData['id_toko']) { ?>
                                                <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2 mt-4">
                                <div class="col px-1">
                                    <button type="submit" data-bs-dismiss="modal" class="btn btn-primary w-100">Selesai</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
    <script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
    <script>
        $(document).ready(function() {
            $('select.tize').selectize();
        });

        $(".push").click(function() {
            var id = $(this).attr('data-id');
            var val = $(this).attr('data-val');

            $.ajax({
                url: "<?= PV::BASE_URL ?>SPK_C/push",
                data: {
                    id: id,
                    push: val
                },
                type: "POST",
                success: function(res) {
                    if (res == 0) {
                        $('button.cek').click();
                    } else {
                        alert(res);
                    }
                }
            });
        })

        $('button.cek').click(function() {
            var parse = <?= $parse ?>;
            var parse_2 = $("input[name=tgl]").val();
            $("div#content").load('<?= PV::BASE_URL ?>SPK_C/content/' + parse + '/' + parse_2);
        });

        $("span.done").click(function() {
            id = $(this).attr("data-id");
            mode = $(this).attr("data-mode");
            $("input[name=id]").val(id);
            $("input[name=mode]").val(mode);
        })

        $("form").on("submit", function(e) {
            e.preventDefault();

            var parse = <?= $parse ?>;
            var parse_2 = $("input[name=tgl]").val();

            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                success: function(res) {
                    if (res == 0) {
                        $("div#content").load('<?= PV::BASE_URL ?>SPK_C/content/' + parse + '/' + parse_2);
                    } else {
                        alert(res);
                    }
                }
            });
        });
    </script>