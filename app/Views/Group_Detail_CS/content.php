<main class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table">
                <tbody>
                    <?php
                    foreach ($data as $k => $a) {
                        $c_item = count($a['item']);
                    ?>
                        <tr>
                            <td>
                                <div class="row mb-3">
                                    <div class="col-auto">
                                        <span class="text-success fw-bold"><?= $a['detail_group'] ?> <?= $a['note'] == "" ? "" : "(" . $a['note'] . ")" ?></span>
                                    </div>
                                    <div class="col">
                                        <button onclick="chgActionMulti(<?= $a['id_detail_group'] ?>,'<?= $a['detail_group'] ?>')" type="button" class="btn btn-sm btn-success bg-gradient" data-bs-toggle="modal" data-bs-target="#itemMulti">Tambah</button>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <?php
                                    foreach ($data[$k]['item'] as $di) { ?>
                                        <div class="col-auto mb-1 px-1">
                                            <small>
                                                <span class="border edit px-1 text-nowrap rounded" data-id='<?= $di['id_detail_item'] ?>'><?= strtoupper($di['detail_item']) ?></span>
                                            </small>
                                        </div>
                                    <?php }
                                    ?>
                                    <div class="col mb-1">
                                        <small>
                                            ... dst
                                        </small>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
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
            <form id="addItemMulti" action="<?= PV::BASE_URL ?>Group_Detail_CS/add_item_multi" method="POST">
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

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

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