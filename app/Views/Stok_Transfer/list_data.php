<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<?php $d = $data['input']; ?>

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="row mb-2">
            <div class="col-auto mt-auto px-1 mb-2">
                <a href="<?= PV::BASE_URL ?>Stok_Transfer"><button class="btn btn-outline pb-0 border-0"><i class="fa-solid fa-chevron-left"></i> <small>Back</small></button></a>
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>No. Ref</label><br>
                <input name="id" value="<?= $d['id'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase;">
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>Tujuan</label><br>
                <input name="supplier_c" value="<?= $data['tujuan'][$d['id_target']]['nama_toko'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto px-1 mb-2 text-center">
                <label>Tanggal</label><br>
                <input type="date" name="tanggal" readonly class="text-center border-bottom border-0" value="<?= $d['tanggal'] ?>">
            </div>
        </div>
        <hr>
        <?php if ($d['cek'] == 0) { ?>
            <div class="row mb-2 mx-0">
                <div class="col px-1 mb-2">
                    <label>Barang</label><br>
                    <select name="barang" class="tize border-0 w-100" required id="barang">
                        <option></option>
                        <?php foreach ($data['barang'] as $br) { ?>
                            <?php if (strlen($br['nama']) > 1) {
                                $code_split = str_split($br['code'], 2); ?>
                                <option value="<?= $br['id'] ?>"><?= $code_split[0] ?> <?= $br['nama'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <div id="stok_data"></div>
        <div id="list_transfer"></div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
        $('#list_transfer').load('<?= PV::BASE_URL ?>Stok_Transfer/list_transfer/<?= $d['id'] ?>');
    });

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result)
                }
            },
        });
    });

    $("#barang").change(function() {
        var get = $(this).val();
        if (get != "") {
            $('#stok_data').load('<?= PV::BASE_URL ?>Stok_Transfer/stok_data/' + get + '/' + '<?= $d['id'] ?>');
        }
    })
</script>