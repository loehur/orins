<?php $parse = $data['parse'];
?>
<main class="container">
    <div class="row mx-0 px-0">
        <div class="col pe-0 ps-2" style="max-width: 250px;">
            <select class="border rounded tize cek" name="customer" required>
                <option></option>
                <?php foreach ($data['dPelanggan'] as $p) { ?>
                    <option <?= $data['customer'] == $p['id_pelanggan'] ? 'selected' : '' ?> value="<?= $p['id_pelanggan'] ?>"><?= $p['id_toko'] ?> - <?= strtoupper($p['nama']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col pt-auto mt-auto pe-0">
            <button type="submit" class="cek pt-2 btn btn-sm btn-primary">Cek Order</button>
        </div>
    </div>
    <?php if ($data['customer'] <> 0) { ?>
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
                                $detail_arr = unserialize($do['produk_detail']);
                                $detail = "";
                                foreach ($detail_arr as $da) {
                                    $detail .= $da['detail_name'] . ", ";
                                }

                                $produk = ucwords($do['produk']);

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

                                    foreach ($data['karyawan'] as $dp) {
                                        if ($dp['id_karyawan'] == $do['id_penerima']) {
                                            $cs = $dp['nama'];
                                        }
                                    }
                                    $cs = "Checking"
                            ?>
                                    <tr>
                                        <td colspan="5" class="table-secondary">
                                            <table class="w-100 p-0 m-0">
                                                <tr>
                                                    <td><span><?= substr($ref, -4) ?></span> <b><?= strtoupper($pelanggan) ?></b></td>
                                                    <td valign="top" class="text-end"><small><?= $cs  ?> <?= substr($do['insertTime'], 2, -3) ?></span></small></td>
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
    <?php } ?>
    </small>

    <form action="<?= PV::BASE_URL; ?>SPK_C/done/<?= $parse ?>" method="POST">
        <div class="modal" id="done">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row mb-2">
                                <div class="col">
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
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <button type="submit" data-bs-dismiss="modal" class="btn btn-primary">Selesai</button>
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
        var parse;
        var parse_2;

        $('select.cek').change(function() {
            parse = <?= $parse ?>;
            parse_2 = $(this).val();
            if (parse_2 == "") {
                return;
            }
        });

        $('button.cek').click(function() {
            $("div#content").load('<?= PV::BASE_URL ?>SPK_Customer/content/' + parse + '/' + parse_2);
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
                        $("div#content").load('<?= PV::BASE_URL ?>SPK_Customer/content/' + parse + '/' + parse_2);
                    } else {
                        alert(res);
                    }
                }
            });
        })

        $("span.done").click(function() {
            id = $(this).attr("data-id");
            mode = $(this).attr("data-mode");
            $("input[name=id]").val(id);
            $("input[name=mode]").val(mode);
        })

        $("form").on("submit", function(e) {
            e.preventDefault();

            var parse = <?= $parse ?>;
            var parse_2 = $("select[name=customer]").val();

            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                success: function(res) {
                    if (res == 0) {
                        $('button.cek').click();
                    } else {
                        alert(res);
                    }
                }
            });
        });
    </script>