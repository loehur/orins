<main>
    <?php $wdth = 135; ?>
    <div class="ms-3 me-3 bg-white">
        <div class="row">
            <div class="col">
                <form target="_blank" action="<?php PV::BASE_URL ?>Cek/order" method="POST">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary" style="width: <?= $wdth ?>px;">No. Referensi</span>
                        <input name="ref" type="text" class="form-control" required>
                        <button type="submit" class="btn bg-gradient btn-primary">Cek</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="data"></div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                $("div#data").html(res);
            },
        });
    });
</script>