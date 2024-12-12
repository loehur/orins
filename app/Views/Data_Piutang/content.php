<style>
    tr:hover {
        background-color: ghostwhite;
    }
</style>
<main>
    <div class="position-fixed bg-white w-100" style="top:0; padding-top:65px;">
        <div class="p-2 rounded bg-light ms-2 mb-2 me-1 border" style="max-width: 500px;">
            <div class=" row mb-1">
                <div class="col-auto pe-0">
                    <input type="text" placeholder="Cari Pelanggan..." id="myInput" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    </div>
    <!-- Main page content-->
    <small>
        <div class="ms-2 rounded pb-2 me-1 border" style="max-width: 500px; margin-top:60px">
            <div class="row row-cols-1 mx-2 mt-2">
                <?php
                $today = date("Y-m-d");
                $bill = [];
                $dibayar = [];
                $cekBayar = [];
                $cekDiskon = [];

                foreach ($data['order'] as $do) {
                    $id_user_afiliasi = $do['id_user_afiliasi'];
                    // if ($this->userData['id_toko'] <> $do['id_toko'] && $do['id_afiliasi'] <> 0 && $id_user_afiliasi == 0) {
                    if ($this->userData['id_toko'] <> $do['id_toko']) {
                        continue;
                    }

                    $ref = $do['ref'];
                    $id_pelanggan = $do['id_pelanggan'];

                    if (!isset($dibayar[$id_pelanggan])) {
                        $dibayar[$id_pelanggan] = 0;
                    }

                    if (!isset($cekBayar[$ref])) {
                        foreach ($data['kas'] as $dk) {
                            if ($dk['ref_transaksi'] == $ref && $dk['status_mutasi'] <> 2) {
                                $dibayar[$id_pelanggan] += $dk['jumlah'];
                            }
                        }
                        $cekBayar[$ref] = true;
                    }

                    if (!isset($cekDiskon[$ref])) {
                        foreach ($data['diskon'] as $dk) {
                            if ($dk['ref_transaksi'] == $ref) {
                                $dibayar[$id_pelanggan] += $dk['jumlah'];
                            }
                        }
                        $cekDiskon[$ref] = true;
                    }

                    $dateTime = substr($do['insertTime'], 0, 10);

                    if (isset($last[$id_pelanggan])) {
                        if ($last[$id_pelanggan] > $dateTime) {
                            $last[$id_pelanggan] = $dateTime;
                        }
                    } else {
                        $last[$id_pelanggan] = $dateTime;
                    }

                    $cancel = $do['cancel'];
                    $jumlah = $do['harga'] * $do['jumlah'];

                    $cekSPK = $do['spk_dvs'];
                    $cekAmbil = $do['id_ambil'];

                    if ($cancel == 0) {
                        //if ((strlen($cekSPK) > 10 && $cekAmbil > 0) || strlen($cekSPK) <= 10) {
                        if (isset($bill[$id_pelanggan])) {
                            $bill[$id_pelanggan] += $jumlah;
                        } else {
                            $bill[$id_pelanggan] = $jumlah;
                        }
                        //}
                    }
                }
                ?>
                <div class="col px-1">
                    <table class="table table-sm w-100 mb-1 bg-white <?= ($dateTime == $today) ? 'border-bottom border-success' : 'border-bottom border-warning' ?>">
                        <?php
                        foreach ($bill as $k => $v) {
                            $sisa = $v - $dibayar[$k];
                            if ($sisa == 0) {
                                continue;
                            }
                            $pelanggan = "";
                            foreach ($data['pelanggan'] as $dp) {
                                if ($dp['id_pelanggan'] == $k) {
                                    $pelanggan = $dp['nama'];
                                }
                            }

                            $tgl1 = new DateTime($today);
                            $tgl2 = new DateTime($last[$k]);
                            $jarak = $tgl2->diff($tgl1);
                            $hari =  $jarak->days;

                            if ($hari <= 2) {
                                continue;
                            }

                        ?>
                            <tr data-id="<?= $k ?>" class="cekPLG target" style="cursor: pointer;">
                                <td class="p-1">
                                    <span class="text-primary"><b><?= strtoupper($pelanggan) ?></b></span>
                                    <small><?= $hari ?> Hari</small>
                                </td>
                                <td class="p-1 text-end">
                                    <?= number_format($v - $dibayar[$k]) ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </small>
</main>
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("#myInput").on("keyup", function() {
        var input = this.value;
        var filter = input.toLowerCase();
        var nodes = document.getElementsByClassName('target');

        if (filter.length > 0) {
            for (i = 0; i < nodes.length; i++) {
                if (nodes[i].innerText.toLowerCase().includes(filter)) {
                    nodes[i].style.display = "table-row";
                } else {
                    nodes[i].style.display = "none";
                }
            }
        } else {
            for (i = 0; i < nodes.length; i++) {
                nodes[i].style.display = "table-row";
            }
        }
    });

    $("tr.cekPLG").click(function() {
        var id = $(this).attr("data-id");
        window.location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + id;
    });
</script>