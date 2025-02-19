<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Retur_Barang_G/add" method="POST">
            <div class="row mb-2 mx-0">
                <div class="col-auto px-1 mb-2">
                    <div class="autocomplete">
                        <label>Tujuan</label><br>
                        <select name="supplier" required class="border-0 tize" id="tujuan" style="text-transform: uppercase; width:200px">
                            <option></option>
                            <?php foreach ($data['supplier'] as $tj) { ?>
                                <option value="<?= $tj['id'] ?>"><?= strtoupper($tj['nama']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-auto px-1 mb-2 text-center">
                    <label>Tanggal</label><br>
                    <input type="date" name="tanggal" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
                </div>
                <div class="col px-1 mb-2 text-end">
                    <label>Note</label><br>
                    <input class="text-end border-bottom border-0 w-100" required name="note">
                </div>
                <div class="col-auto px-1 mb-2">
                    <div class="pt-4">
                        <input name="sds" class="form-check-input" type="checkbox" value="1">
                        <label class="form-check-label" for="flexCheckDefault">
                            SDS
                        </label>
                    </div>
                </div>
                <div class="col mt-auto mb-2">
                    <button type="submit" class="btn btn-outline-success">Create</button>
                </div>
            </div>
        </form>

        <table class="table table-sm">
            <?php foreach ($data['input'] as $a) { ?>
                <tr>
                    <td><a href="<?= PV::BASE_URL ?>Retur_Barang_G/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a></td>
                    <td class="align-middle">
                        #<?= $a['id'] ?><br>
                        <?= $a['tanggal'] ?>
                    </td>
                    <td class="">
                        <?= $a['sds'] == 0 ? "SDS-NO" : "SDS-YES" ?><br>
                        <small><?= $a['note'] ?></small>
                    </td>
                    <td class="align-top">
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-warning"><i class="fa-regular fa-circle"></i> Checking</span>
                        <?php } else { ?>
                            <span class="text-success"><i class="fa-solid fa-check"></i></span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>
<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
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