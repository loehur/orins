<?php
$sdsLabel = [
   0 => 'TOKO',
   1 => 'SDS',
   2 => 'HYBRID',
];
?>
<main>
    <div class="row mx-0">
        <div class="col" style="max-width: 720px;">
            <div class="fw-bold mb-2">Akun Pembayaran</div>
            <div class="small text-muted mb-2">Hanya ubah nama & tipe lokasi (TOKO / SDS / HYBRID). Tidak bisa menambah atau menghapus.</div>

            <table class="table table-sm table-bordered text-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Nama Akun</th>
                        <th style="width: 140px;">Lokasi</th>
                        <th style="width: 70px;" class="text-end">Freq</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['list'])) { ?>
                        <tr>
                            <td colspan="4" class="text-muted text-center py-3">Belum ada akun pembayaran untuk toko ini.</td>
                        </tr>
                    <?php } else {
                        foreach ($data['list'] as $pa) {
                            $id = (int)$pa['id'];
                            $sds = (int)($pa['sds'] ?? 0);
                            if (!isset($sdsLabel[$sds])) {
                                $sds = 0;
                            }
                            ?>
                            <tr data-id="<?= $id ?>">
                                <td><?= $id ?></td>
                                <td>
                                    <span class="cell_edit text-success fw-bold"
                                        data-id="<?= $id ?>"
                                        data-col="payment_account"
                                        data-tipe="text"><?= htmlspecialchars(strtoupper($pa['payment_account'] ?? '')) ?></span>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm pa-sds" data-id="<?= $id ?>">
                                        <?php foreach ($sdsLabel as $val => $label) { ?>
                                            <option value="<?= $val ?>" <?= $sds === $val ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td class="text-end"><?= (int)($pa['freq'] ?? 0) ?></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    function savePaField(id, col, value, onOk, onFail) {
        $.ajax({
            url: "<?= PV::BASE_URL ?>Akun_Pembayaran/update",
            type: "POST",
            dataType: "json",
            data: { id: id, col: col, value: value },
            success: function(res) {
                if (res && res.ok == 1) {
                    if (typeof onOk === "function") onOk(res);
                } else {
                    var msg = (res && res.error) ? res.error : "Gagal menyimpan";
                    if (typeof onFail === "function") onFail(msg);
                    else alert(msg);
                }
            },
            error: function() {
                if (typeof onFail === "function") onFail("Gagal koneksi ke server");
                else alert("Gagal koneksi ke server");
            }
        });
    }

    var click = 0;
    $(document).on("click", ".cell_edit", function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var id = $(this).attr("data-id");
        var col = $(this).attr("data-col");
        var tipe = $(this).attr("data-tipe") || "text";
        var value = $(this).text().trim();
        var value_before = value;
        var el = $(this);
        var width = Math.max(el.parent().width() || 180, 120);

        el.html("<input required type='" + tipe + "' style='text-transform:uppercase;outline:none;border:none;width:" + width + "px;background:transparent' id='value_' value='" + value.replace(/'/g, "&#39;") + "'>");
        $("#value_").focus();
        $("#value_").keypress(function(e) {
            if (e.which == 13) {
                $(this).blur();
            }
        });
        $("#value_").focusout(function() {
            var value_after = ($(this).val() || "").toUpperCase().trim();
            if (value_after === value_before || value_after === "") {
                el.html(value_before);
                click = 0;
                return;
            }
            savePaField(id, col, value_after, function(res) {
                el.html(res.value || value_after);
                click = 0;
            }, function(msg) {
                alert(msg);
                el.html(value_before);
                click = 0;
            });
        });
    });

    $(document).on("change", "select.pa-sds", function() {
        var $sel = $(this);
        var id = $sel.data("id");
        var value = $sel.val();
        var prev = $sel.data("prev");
        if (prev === undefined) {
            prev = $sel.find("option").filter(function() {
                return this.defaultSelected;
            }).val();
        }
        savePaField(id, "sds", value, function() {
            $sel.data("prev", value);
        }, function(msg) {
            alert(msg);
            $sel.val(prev);
        });
    });
</script>
