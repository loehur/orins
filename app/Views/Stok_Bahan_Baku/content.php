<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container text-sm">
        <table id="tb_barang" class="hover text-sm">
            <thead>
                <th>Head</th>
                <th>Nama</th>
                <th>Toko</th>
                <th>Gudang</th>
            </thead>
            <?php foreach ($data['barang'] as $a) {
                if (isset($data['stok_gudang'][$a['id']])) { ?>
                    <?php if (substr($a['code'], 0, 2) == "B0") { ?>
                        <tr>
                            <td class="">
                                <?= $a['tipe'] ?>
                            </td>
                            <td>
                                <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?>
                            </td>
                            <td style="width: 70px;" class="text-end">
                                <?php if (isset($data['stok'][$a['id']])) { ?>
                                    <span class="btn btn-sm btn-success bg-gradient pakai" data-bs-toggle="modal" data-qty data-bs-target="#exampleModal4" id="a<?= $a['id'] ?>" data-id_barang="<?= $a['id'] ?>" data-id_sumber="<?= $this->userData['id_toko'] ?>" style=" min-width: 50px;"><?= $data['stok'][$a['id']]['qty'] ?></span>
                                <?php } else { ?>
                                    <span class="btn btn-sm btn-success bg-gradient pakai" style=" min-width: 50px;">0</span>
                                <?php } ?>
                            </td>
                            <td style="width: 70px;" class="text-end">
                                <span class="btn btn-sm btn-danger bg-gradient pakai" data-bs-toggle="modal" data-bs-target="#exampleModal4" id="b<?= $a['id'] ?>" data-id_barang="<?= $a['id'] ?>" data-id_sumber="0" style="min-width: 50px;"><?= $data['stok_gudang'][$a['id']]['qty'] ?></span>
                            </td>
                        </tr>
                    <?php } ?>
            <?php }
            } ?>
        </table>
    </div>
</main>


<form action="<?= PV::BASE_URL; ?>Stok_Bahan_Baku/pakai" method="POST">
    <div class="modal" id="exampleModal4">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" style="height: 350px;">
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Karyawan</label>
                                <input type="hidden" id="id_sumber" name="id_sumber">
                                <input type="hidden" id="id_barang" name="id_barang">
                                <select class="form-select tize" name="staf_id" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan_toko'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= ucwords($k['nama']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Jumlah</label>
                                <input class="form form-control" type="number" value="1" min="1" name="qty">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <button type="submit" data-bs-dismiss="modal" class="btn btn-sm btn-dark">Pakai</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    var qty = 0;
    var id = 0;
    $(document).ready(function() {
        $('select.tize').selectize();

        $('#tb_barang').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": 50,
            "scrollY": 615,
            "dom": "lfrti"
        });
    })

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    qty_in = $("input[name=qty]").val();
                    var new_qty = (qty - qty_in);
                    $("span#" + id).html(new_qty);
                } else {
                    alert(res);
                }
            }
        });
    });

    $("span.pakai").click(function() {
        var id_barang = $(this).attr("data-id_barang");
        var id_sumber = $(this).attr("data-id_sumber");
        qty = $(this).text();
        id = $(this).attr("id");
        $("input#id_barang").val(id_barang);
        $("input#id_sumber").val(id_sumber);
    })

    var click = 0;
</script>