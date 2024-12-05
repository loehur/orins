<form class="aff_form" action="<?= PV::BASE_URL ?>Buka_Order/add/<?= $data['id_toko'] ?>" method="POST">
    <div class="modal-body px-2 bg-warning-soft">
        <div class="mb-3">
            <select class="tize loadDetail" name="id_produk" required>
                <option></option>
                <?php foreach ($data['produk'] as $dp) { ?>
                    <option value="<?= $dp['id_produk'] ?>"><?= $dp['produk'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div id="detail_aff"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Tambah</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $('select.loadDetail').on('change', function() {
        var produk = this.value;
        $("div#detail_aff").load('<?= PV::BASE_URL ?>Buka_Order/load_detail/' + produk);
    });

    $("form.aff_form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
                } else if (res == 1) {
                    var parse = $("select[name=id_pelanggan]").val();
                    location.href = "<?= PV::BASE_URL ?>Data_Operasi/index/" + parse;
                } else {
                    alert(res);
                }
            }
        });
    });
</script>