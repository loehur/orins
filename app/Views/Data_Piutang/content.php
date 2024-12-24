<style>
    tr:hover {
        background-color: ghostwhite;
    }
</style>
<main>
    <div class="position-fixed bg-white w-100" style="top:0; padding-top:70px;">
        <div class="p-2 rounded bg-light ms-2 mb-2 me-1 border" style="max-width: 500px;">
            <div class="row mb-1">
                <div class="col-auto pe-0">
                    <input type="text" placeholder="Cari Pelanggan..." id="myInput" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    </div>
    <!-- Main page content-->
    <small>
        <div class="ms-2 rounded pb-2 me-1 border" style="max-width: 500px; margin-top:40px">
            <div class="row row-cols-1 mx-2 overflow-auto" style="height: 700px;">
                <?php
                $today = date("Y-m-d");
                $list = [];
                foreach ($data['order'] as $do) {
                    $id_pelanggan = $do['id_pelanggan'];
                    $dateTime = substr($do['insertTime'], 0, 10);
                    if (isset($list[$id_pelanggan])) {
                        if ($list[$id_pelanggan] > $dateTime) {
                            $list[$id_pelanggan] = $dateTime;
                        }
                    } else {
                        $list[$id_pelanggan] = $dateTime;
                    }
                }

                asort($list);
                ?>
                <div class="col px-1">
                    <table class="table table-sm w-100 mb-1 bg-white">
                        <?php
                        foreach ($list as $k => $v) {
                            $pelanggan = $data['pelanggan'][$k]['nama'];

                            $tgl1 = new DateTime($today);
                            $tgl2 = new DateTime($list[$k]);
                            $jarak = $tgl2->diff($tgl1);
                            $hari =  $jarak->days;

                            if ($hari <= 2) {
                                continue;
                            }

                        ?>
                            <tr data-id="<?= $k ?>" class="cekPLG target" style="cursor: pointer;">
                                <td class="p-1">
                                    <span class="text-primary"><b><?= strtoupper($pelanggan) ?></b></span>
                                </td>
                                <td class="text-end"> <small><?= $hari ?> Hari</small></td>
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