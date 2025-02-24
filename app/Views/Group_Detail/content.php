<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<main class="container">
    <!-- Main page content-->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col">
                    <select name="item_group" class="tize border-0" required id="item_group">
                        <option value=""></option>
                        <?php foreach ($data['main'] as $k => $a) { ?>
                            <option value="<?= $a['id_index'] ?>" <?= $a['id_index'] == $data['id_index'] ? "selected" : "" ?>><?= $a['detail_group'] ?> <?= $a['note'] <> "" ? "(" . $a['note'] . ")" : "" ?> </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col mt-auto">
                    <button type="button" class="float-end btn btn-sm btn-outline-primary mx-2" data-bs-toggle="modal" data-bs-target="#exampleModalLink">Tambah Link</button>
                    <button type="button" class="float-end btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="load"></div>
        </div>
    </div>
</main>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah Kelompok Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="<?= PV::BASE_URL ?>Group_Detail/add/0/<?= $data['pj'] ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Kelompok Detail</label>
                            <input type="text" name="group" required class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="note" required class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalLink" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah LINK Kelompok Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="<?= PV::BASE_URL ?>Group_Detail/add/1/<?= $data['pj'] ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Kelompok Detail</label>
                                <input type="text" name="group" required class="form-control">
                            </div>
                            <div class="col">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="note" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Kelompok Detail</label>
                        <select class="border tize" name="id_index" required>
                            <option></option>
                            <?php foreach ($data['main'] as $d) { ?>
                                <option value="<?= $d['id_index'] ?>"><?= $d['detail_group'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
        var id_index = $data['id_index'];
        if (typeof id_index != "undefined") {
            load(<?= $data['id_index'] ?>);
        }
    });

    $("form.ajax").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(result) {
                if (result == 0) {
                    content(<?= $data['pj'] ?>);
                } else {
                    alert(result);
                }
            },
        });
    });

    $("#item_group").change(function() {
        var get = $(this).val();
        if (get != "") {
            load(get);
        } else {
            content(<?= $data['pj'] ?>);
        }
    })

    function load(get) {
        $("#load").load('<?= PV::BASE_URL ?>Group_Detail/load/' + get)
    }
</script>