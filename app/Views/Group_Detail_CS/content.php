<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mt-n10">
            <div class="card-body">
                <table class="table table-hover">
                    <tbody>
                        <?php
                        foreach ($data as $k => $a) {
                            $c_item = count($a['item']);
                        ?>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            <span class="text-success"><?= $a['detail_group'] ?></span>
                                        </div>
                                        <div class="col">
                                            <div class="float-end">
                                                <button onclick="chgActionMulti(<?= $a['id_detail_group'] ?>,'<?= $a['detail_group'] ?>')" type="button" class="border rounded bg-white" data-bs-toggle="modal" data-bs-target="#itemMulti">Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 mt-1">
                                        <?php
                                        foreach ($data[$k]['item'] as $di) { ?>
                                            <div class="col">
                                                <small>
                                                    <span class="border edit px-1 text-nowrap rounded" data-id='<?= $di['id_detail_item'] ?>'><?= strtoupper($di['detail_item']) ?></span>
                                                </small>
                                            </div>
                                        <?php }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="itemMulti" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menambah MULTI <span class="text-success groupDetail"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addItemMulti" action="<?= $this->BASE_URL ?>Group_Detail_CS/add_item_multi" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item Detail - <small>Pisahkan dengan Koma ( , )</small></label>
                        <input type="text" name="item" class="form-control" required>
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

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    var addItemMultiAction = $("form#addItemMulti").attr('action');

    function chgActionMulti(id_detail_group, group) {
        var newAction = addItemMultiAction + "/" + id_detail_group;
        $('form#addItemMulti').attr('action', newAction);
        $('span.groupDetail').html(group);
    }

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
                    alert(result);
                }
            },
        });
    });
</script>