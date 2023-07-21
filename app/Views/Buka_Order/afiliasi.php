<form class="aff_form" action="<?= $this->BASE_URL ?>Buka_Order/add/<?= $data['id_toko'] ?>" method="POST">
    <div class="modal-body px-2 bg-info-soft">
        <label class="label-form">Produk <span class="text-success"><b><?= strtoupper($data['toko']) ?></b></span></label>
        <div class="mb-3">
            <select class="tize loadDetail" name="id_produk" required>
                <option></option>
                <?php foreach ($this->dProdukAll as $dp) {
                    if ($data['id_toko'] == $dp['id_toko']) { ?>
                        <option value="<?= $dp['id_produk'] ?>"><?= $dp['produk'] ?></option>
                <?php }
                } ?>
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
        $("div#detail_aff").load('<?= $this->BASE_URL ?>Buka_Order/load_detail/' + produk);
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
                    location.href = "<?= $this->BASE_URL ?>Data_Operasi/index/" + parse;
                } else {
                    alert(res);
                }
            }
        });
    });
</script>