<?php foreach ($data['stok'] as $d) { ?>
    <?php if ($d['qty'] > 0) { ?>
        <form class="form-add-mutasi" action="<?= PV::BASE_URL ?>Retur_Barang_G/add_mutasi/<?= $data['ref'] ?>" method="POST">
            <div class="row mb-2 mx-1 text-sm border-bottom">
                <div class="col-auto px-1 mb-2 text-center">
                    <input type="hidden" name="sds" value="<?= $d['sds'] ?>">
                    <input type="hidden" name="kode" value="<?= $d['id_barang'] ?>">
                    <span><?= $d['sds'] == 1 ? "SDS" : "ABF" ?></span>
                </div>
                <div class="col px-1 mb-2">
                    <input type="hidden" name="sn" value="<?= $d['sn'] ?>">
                    <span><?= $d['sn'] == "" ? "-" : $d['sn'] ?></span>
                </div>
                <div class="col-auto text-center px-1 mb-2 text-end">
                    <input type="hidden" value="<?= $d['qty'] ?>" name="qty_stok">
                    <span><?= $d['qty'] ?></span>
                </div>
                <div class="col text-end px-1 mb-2 text-end">
                    <input required type="number" min="1" value="<?= strlen($d['sn']) > 0 ? 1 : "" ?>" max="<?= $d['qty'] ?>" class="px-2 text-center border-bottom border-0" name="qty">
                </div>
                <div class="col-auto pe-0 text-end mb-2">
                    <button type="submit" class="btn btn-sm btn-outline-success">Add</button>
                </div>
            </div>
        </form>
    <?php } ?>
<?php } ?>
