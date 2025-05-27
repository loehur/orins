<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<main>
    <!-- Main page content-->
    <div class="container">

        <div class="row mb-2 mx-0">
            <div class="col-auto px-1 mb-2">
                <div class="input-group">
                    <span class="input-group-text text-primary">Dari</span>
                    <input name="date_from" id="date_from" type="date" min="2025-01-01" max="<?= date("Y-m-d") ?>" value="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                </div>
            </div>
            <div class="col-auto px-1 mb-2">
                <div class="input-group">
                    <span class="input-group-text text-primary">Ke</span>
                    <input name="date_to" id="date_to" type="date" min="2025-01-01" max="<?= date("Y-m-d") ?>" value="<?= date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                    <button onclick="cek()" class="btn btn-primary">Cek</button>
                </div>
            </div>
        </div>
        <div id="data" class="bg-light mx-1 px-2">
        </div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    function cek() {
        var date_from = $("#date_from").val();
        var date_to = $("#date_to").val();
        if (date_from != "" && date_to != "") {
            $('#data').load('<?= PV::BASE_URL ?>Riwayat_Jual/riwayat_data/' + date_from + '/' + date_to);
        }
    }
</script>