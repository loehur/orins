<main>
    <?php $date_choose = $data['date'] == "" ? date('Y-m-d', strtotime("-1 days")) : $data['date'] ?>
    <div class="row mx-0 mb-2 px-2">
        <div class="col pe-0">
            <div class="input-group">
                <span class="input-group-text text-primary">Tanggal</span>
                <input id="inDate" name="month" type="date" min="2023-07-01" max="<?= date('Y-m-d') ?>" value="<?= $date_choose ?>" placeholder="YYYY-MM-DD" class="form-control" required>
                <button id="cek" class="btn btn-primary">Cek</button>
            </div>

        </div>
    </div>

    <div class="pe-2 pb-0 mt-2 ms-3 me-3 bg-white">
        <div class="row">
            <div class="col">
                <table class="table table-sm text-sm table-borderless mb-2">
                    <tr>
                        <td class="text-end">Tunai</td>
                        <td class="text-end" style="width:100px">Rp<?= number_format($data['tunai']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-end">Non Tunai</td>
                        <td class="text-end" style="width:100px">Rp<?= number_format($data['nTunai']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-end">Extra Diskon</td>
                        <td class="text-end" style="width:100px">-Rp<?= number_format($data['xtra_diskon']) ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td class="text-end">Total</td>
                        <td class="text-end" style="width:100px">Rp<?= number_format($data['nTunai'] + $data['tunai'] - $data['xtra_diskon']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal" id="modalCek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="cek_load"></div>
    </div>
</div>
<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    $("button#cek").click(function() {
        var date = $('#inDate').val();
        location.href = "<?= PV::BASE_URL ?>Penjualan_SDS/index/" + date;
    });

    $("button#cekTotal").click(function() {
        location.href = "<?= PV::BASE_URL ?>Penjualan_SDS";
    });

    $('span.cekTrx').click(function() {
        var ref = $(this).attr("data-ref");
        $("div#cek_load").load('<?= PV::BASE_URL ?>Setoran/cek/' + ref);
    });
</script>