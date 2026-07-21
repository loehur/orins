<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>
<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Gudang_Penjualan/add" method="POST">
            <div class="row mb-2 mx-0">
                <div class="col-auto px-1 mb-2">
                    <label>Tujuan</label><br>
                    <select name="tujuan" required class="border-0 tize" id="tujuan" style="text-transform: uppercase; width:200px">
                        <option></option>
                        <?php foreach ($data['tujuan'] as $tj) { ?>
                            <option value="<?= $tj['id_pelanggan'] ?>"><?= strtoupper($tj['nama']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-auto px-1 mb-2 text-center">
                    <label>Tanggal</label><br>
                    <input type="date" name="tanggal" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
                </div>
                <div class="col mt-auto mb-2">
                    <button type="submit" class="btn btn-outline-success">Create</button>
                </div>
            </div>
        </form>

        <table class="text-sm" id="dt_tb">
            <thead>
                <tr>
                    <th class="text-center"></th>
                    <th>Ref</th>
                    <th>Tanggal</th>
                    <th>Tujuan</th>
                    <th>ST</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data['input'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <a href="<?= PV::BASE_URL ?>Gudang_Penjualan/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                    </td>
                    <td>
                        <?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= $a['tanggal'] ?>
                    </td>
                    <td class="">
                        <?= strtoupper($data['tujuan'][$a['id_target']]['nama']) ?>
                    </td>
                    <td class="align-middle">
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-warning"><i class="fa-regular fa-circle"></i> Checking</span>
                        <?php } else { ?>
                            <span class="text-success"><i class="fa-solid fa-check"></i></span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
        $('#dt_tb').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "bAutoWidth": false,
            "pageLength": 50,
            "scrollY": 530,
            "dom": "lfrti"
        });
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
</script>