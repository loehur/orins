<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<main>
    <!-- Main page content-->
    <div class="container">

        <div class="row mb-2 mx-0">
            <div class="col">
                <select name="barang" class="tize border-0" id="barang">
                    <option value="">Pilih Barang</option>
                    <?php foreach ($data['barang'] as $key => $br) { ?>
                        <option value="<?= $key ?>"><?= $br['code'] ?> <?= trim($br['brand'] . " " . $br['model']) ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div id="data" class="bg-light mx-1 px-2">
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    function cek() {
        var get = $("#month").val();
        if (get != "") {
            $('#data').load('<?= PV::BASE_URL ?>Barang_Riwayat_A/riwayat_data/' + get);
        }
    }

    $("#barang").change(function() {
        var get = $(this).val();
        if (get != "") {
            $('#data').load('<?= PV::BASE_URL ?>Barang_Riwayat_A/riwayat_data/' + get);
        }
    })
</script>