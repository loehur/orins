<?php $parse = $data['parse']; ?>
<main>
    <small>
        <div class="row ms-2 me-2 mt-3">
            <div class="col pe-0 ps-0" style="min-width: 150px; max-width: 160px">
                <input class="form-control" name="tgl" value="<?= $data['date'] ?>" type="date" />
            </div>

            <div class="col pt-auto mt-auto pe-0">
                <button type="submit" class="cek pt-2 btn btn-sm btn-primary">Cek Order</button>
            </div>
        </div>

        <div class="row ms-2 mt-2 me-1">
            <?php
            for ($x = 1; $x <= 2; $x++) { ?>
                <div class="col ps-0 pe-1">
                    <?php foreach ($data['order'][$x] as $ref => $data['order_']) { ?>
                        <table class="table table-sm shadow-sm mb-2 border" style="min-width: 350px;">
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

                                foreach ($this->dProduk as $dp) {
                                    if ($dp['id_produk'] == $id_produk) {
                                        $produk = $dp['produk'];
                                    }
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

                                    foreach ($data['karyawan'] as $dp) {
                                        if ($dp['id_karyawan'] == $do['id_penerima']) {
                                            $cs = $dp['nama'];
                                        }
                                    } ?>
                                    <tr>
                                        <td colspan="5" class="table-secondary">
                                            <table class="w-100 p-0 m-0">
                                                <tr>
                                                    <td><span><?= substr($ref, -4) ?></span> <b><?= strtoupper($pelanggan) ?></b></td>
                                                    <td style="width: 180px;" class="text-end"><small><?= $cs  ?> [<?= substr($do['insertTime'], 2, -3) ?>]</span></small></td>
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
                                                <td class="pe-1 text-success">
                                                    <?php
                                                    foreach ($detail_arr as $da) { ?>
                                                        <b><?= strtoupper($da['detail_name']) ?> </b>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-end text-purple pe-2" style="width:40px"><b><?= number_format($do['jumlah']) ?></b>pcs</td>
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
                                                        echo "<td class='pe-2'>";
                                                        if ($divisi_arr[$key]['status'] == 1) {
                                                            $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_produksi']);
                                                            echo '<i class="fa-solid fa-check text-success"></i> ' . $karyawan;
                                                        } else {
                                                            echo '<span class="done" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#done" data-id="' . $id_order_data . '" data-mode="1"> <i class="fa-regular fa-circle"></i> Tahap1 ';
                                                        }
                                                        echo "</td>";
                                                        if ($divisi_arr[$key]['cm'] == 1) {
                                                            echo "<td class='pe-2'>";
                                                            if ($divisi_arr[$key]['cm_status'] == 1) {
                                                                $karyawan = $this->model('Arr')->get($data['karyawan'], "id_karyawan", "nama", $divisi_arr[$key]['user_cm']);
                                                                echo '<i class="fa-solid text-success fa-check-double"></i> ' . $karyawan;
                                                            } else {
                                                                echo '<span class="done" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#done" data-id="' . $id_order_data . '" data-mode="2"> <i class="fa-regular fa-circle"></i> Tahap2 ';
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
</main>

<form action="<?= $this->BASE_URL; ?>SPK_C/done/<?= $parse ?>" method="POST">
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
                                <select class="form-select tize" name="id_karyawan" required>
                                    <option></option>
                                    <?php foreach ($this->dKaryawan as $k) {
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

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });
    $('button.cek').click(function() {
        var parse = <?= $parse ?>;
        var parse_2 = $("input[name=tgl]").val();
        $("div#content").load('<?= $this->BASE_URL ?>SPK_C/content/' + parse + '/' + parse_2);
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
                    $("div#content").load('<?= $this->BASE_URL ?>SPK_C/content/' + parse + '/' + parse_2);
                } else {
                    alert(res);
                }
            }
        });
    });
</script>