<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="row mb-2 mx-0">
            <div class="col px-1 mb-2">
                <label>Barang</label><br>
                <select name="barang" class="tize border-0" id="barang">
                    <option></option>
                    <?php foreach ($data['barang'] as $key => $br) {
                        $code_split = str_split($br['code'], 2); ?>
                        <option value="<?= $key ?>"><?= $br['code'] ?> <?= trim($br['brand'] . " " . $br['model']) ?></span></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto px-1 mb-2">
                <label>Serial Number</label><br>
                <input name="sn" id="sn" class="form-control form-control-sm">
            </div>
            <div class="col-auto px-1 mb-2">
                <label>&nbsp;</label><br>
                <span id="cek" class="btn btn-sm btn-success">Cek</span>
            </div>
        </div>
        <div id="data" class="bg-light mx-1 px-2"></div>
    </div>
</main>

<div class="modal fade" id="modalSnDup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header py-2 bg-warning bg-gradient text-dark">
                <h6 class="modal-title mb-0">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Peringatan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalSnDupBody"></div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function escapeHtml(str) {
        return String(str || "").replace(/[&<>"']/g, function(c) {
            return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c];
        });
    }

    function showSnWarn(html) {
        $("#modalSnDupBody").html(html);
        var el = document.getElementById("modalSnDup");
        if (el && el.parentElement !== document.body) {
            document.body.appendChild(el);
        }
        var modal = bootstrap.Modal.getOrCreateInstance(el);
        modal.show();
    }

    function loadRiwayat(idBarang, sn) {
        var sel = document.getElementById("barang");
        if (sel && sel.selectize) {
            sel.selectize.setValue(String(idBarang), true);
        }
        $("#data").load("<?= PV::BASE_URL ?>Barang_Riwayat/data/" + encodeURIComponent(idBarang) + "/" + encodeURIComponent(sn));
    }

    function cekRiwayat() {
        var get = $("#barang").val();
        var sn = $.trim($("#sn").val());
        if (get) {
            loadRiwayat(get, sn);
            return;
        }
        if (!sn) {
            return;
        }

        var $btn = $("#cek").prop("disabled", true);
        $.ajax({
            url: "<?= PV::BASE_URL ?>Barang_Riwayat/cek_sn",
            type: "POST",
            dataType: "json",
            data: { sn: sn },
            complete: function() {
                $btn.prop("disabled", false);
            },
            success: function(res) {
                if (!res || !res.ok) {
                    showSnWarn("<p class='mb-0'>" + escapeHtml((res && res.error) ? res.error : "Gagal cek Serial Number") + "</p>");
                    return;
                }
                if (res.count === 1) {
                    loadRiwayat(res.id_barang, res.sn);
                    return;
                }
                if (res.count === 0) {
                    showSnWarn("<p class='mb-0'>Serial Number: <b>" + escapeHtml(res.sn) + "</b> tidak ditemukan.</p>");
                    return;
                }
                var list = "";
                $.each(res.produk || [], function(i, nama) {
                    list += "<li>" + escapeHtml(nama) + "</li>";
                });
                var jumlah = (res.count === 2) ? "dua" : res.count;
                showSnWarn(
                    "<p class='mb-2'>Serial Number: <b>" + escapeHtml(res.sn) + "</b> terdapat di " + jumlah + " produk:</p>" +
                    "<ul class='mb-3'>" + list + "</ul>" +
                    "<p class='mb-0'>Silahkan lakukan cek riwayat dengan mengisi lengkap SN beserta Barangnya.</p>"
                );
            },
            error: function() {
                showSnWarn("<p class='mb-0'>Gagal cek Serial Number. Coba lagi.</p>");
            }
        });
    }

    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $("#cek").click(function() {
        cekRiwayat();
    });

    $("#sn").on("keydown", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            cekRiwayat();
        }
    });
</script>