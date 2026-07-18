<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }

    #tb_barang tbody td,
    #tb_barang thead th {
        padding: 0.2rem 0.45rem;
        line-height: 1.15;
        vertical-align: middle;
    }

    #tb_barang .btn-sm {
        padding: 0.1rem 0.35rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    #tb_barang_wrapper .dt-scroll-body,
    #tb_barang_wrapper .dataTables_scrollBody {
        max-height: 300px !important;
    }

    .riwayat-pakai-scroll {
        max-height: 220px;
        overflow-y: auto;
    }

    .riwayat-pakai-scroll thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
        padding: 0.25rem 0.45rem;
        line-height: 1.15;
    }

    .riwayat-pakai-scroll tbody td {
        padding: 0.2rem 0.45rem;
        line-height: 1.15;
        vertical-align: middle;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container text-sm">
        <table id="tb_barang" class="hover text-sm">
            <thead>
                <th>Head</th>
                <th>Nama</th>
                <th>Gudang</th>
            </thead>
            <?php foreach ($data['barang'] as $a) {
                if (isset($data['stok_gudang'][$a['id']])) { ?>
                    <tr>
                        <td class="">
                            <?= $a['tipe'] ?>
                        </td>
                        <td>
                            <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?>
                        </td>
                        <td style="width: 70px;" class="text-end">
                            <?php if ($data['stok_gudang'][$a['id']]['qty'] > 0) { ?>
                                <span class="btn btn-sm btn-danger bg-gradient pakai" data-bs-toggle="modal" data-bs-target="#exampleModal4" id="b<?= $a['id'] ?>" data-id_barang="<?= $a['id'] ?>" data-id_sumber="0" data-has_sn="<?= $a['sn'] ?>" style="min-width: 50px;"><?= number_format($data['stok_gudang'][$a['id']]['qty'], 0) ?></span>
                            <?php } else { ?>
                                <span class="btn btn-sm btn-dark bg-gradient pakai" style="min-width: 50px;"><?= number_format($data['stok_gudang'][$a['id']]['qty'], 0) ?></span>
                            <?php } ?>
                        </td>
                    </tr>
            <?php }
            } ?>
        </table>

        <div class="mt-3 pt-2 border-top">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <span class="fw-bold">Riwayat Pakai</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-dark riwayat-filter active" data-period="this">Bulan ini</button>
                    <button type="button" class="btn btn-outline-dark riwayat-filter" data-period="last">Bulan lalu</button>
                </div>
            </div>
            <div id="riwayat-pakai-panel">
                <div class="text-muted small py-2">Memuat riwayat...</div>
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="exampleModal4" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="min-height: 350px;">
            <form id="formPakaiStok" action="<?= PV::BASE_URL; ?>Stok_Bahan_Baku/pakai" method="POST">
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-sm" id="sn_label">SN (Optional)</label>
                                <input class="form form-control mb-2" name="sn" id="sn_input">
                                <select class="form-select mb-2" name="sn" id="sn_select" style="display:none;">
                                    <option value="">Pilih SN</option>
                                </select>

                                <label class="form-label text-sm">SDS</label>
                                <select class="form-select mb-2" name="sds">
                                    <option value="0" selected>TIDAK</option>
                                    <option value="1">YA</option>
                                </select>

                                <label class="form-label text-sm">Tujuan Pakai</label>
                                <select class="form-select mb-2" name="akun_pakai" required>
                                    <option></option>
                                    <?php foreach ($data['akun_pakai'] as $ap) { ?>
                                        <option value="<?= $ap['id'] ?>"><?= ucwords($ap['nama']) ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label text-sm">Catatan</label>
                                <input class="form form-control mb-2" name="note" required>

                                <label class="form-label text-sm">Jumlah</label>
                                <input class="form form-control mb-2" type="number" value="1" min="1" name="qty" id="qty_input">

                                <label class="form-label text-sm">Karyawan</label>
                                <input type="hidden" id="id_sumber" name="id_sumber">
                                <input type="hidden" id="id_barang" name="id_barang">
                                <select class="form-select tize mb-2" name="staf_id" required>
                                    <option></option>
                                    <?php foreach ($data['karyawan_toko'] as $k) { ?>
                                        <option value="<?= $k['id_karyawan'] ?>"><?= ucwords($k['nama']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2 mt-3">
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-dark">Pakai</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusPakai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-gradient text-dark py-2">
                <h6 class="modal-title">Hapus Riwayat Pakai</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-sm">
                <p class="mb-2">Yakin ingin menghapus riwayat pakai ini?</p>
                <div id="hapus-pakai-detail" class="border rounded p-2 bg-light small"></div>
                <p class="mb-0 mt-2 text-danger"><strong>Stok gudang akan dikembalikan.</strong></p>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnHapusPakaiConfirm">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>

<script>
    var qty = 0;
    var id = 0;
    var snList = [];
    var hasSnItem = false;
    var riwayatPeriod = 'this';
    var hapusPakaiPending = null;
    var modalHapusPakai = null;

    function setRiwayatFilterActive(period) {
        $('.riwayat-filter').removeClass('btn-dark active').addClass('btn-outline-dark');
        $('.riwayat-filter[data-period="' + period + '"]').removeClass('btn-outline-dark').addClass('btn-dark active');
    }

    function loadRiwayatPakai(period) {
        riwayatPeriod = period || 'this';
        setRiwayatFilterActive(riwayatPeriod);
        $('#riwayat-pakai-panel').html('<div class="text-muted small py-2">Memuat riwayat...</div>');
        $('#riwayat-pakai-panel').load('<?= PV::BASE_URL ?>Stok_Pakai/riwayat_pakai/' + riwayatPeriod);
    }

    function setSnMode(useSelect) {
        hasSnItem = useSelect;
        if (useSelect) {
            $("#sn_label").text("SN");
            $("#sn_input").hide().prop("disabled", true).val("");
            $("#sn_select").show().prop("disabled", false).prop("required", true);
        } else {
            $("#sn_label").text("SN (Optional)");
            $("#sn_select").hide().prop("disabled", true).prop("required", false).val("");
            $("#sn_input").show().prop("disabled", false);
            snList = [];
        }
        $("#qty_input").val(1).removeAttr("max");
    }

    function renderSnOptions() {
        var sds = $("select[name=sds]").val();
        var $sel = $("#sn_select").empty().append('<option value="">Pilih SN</option>');
        var count = 0;
        $.each(snList, function(i, item) {
            if (String(item.sds) === String(sds)) {
                $sel.append('<option value="' + item.sn + '" data-qty="' + item.qty + '">' + item.sn + '</option>');
                count++;
            }
        });
        if (count === 0) {
            $sel.append('<option value="" disabled>SN tidak tersedia</option>');
        }
        $("#qty_input").val(1).removeAttr("max");
    }

    function loadSnList(id_barang, id_sumber) {
        $.getJSON('<?= PV::BASE_URL ?>Stok_Bahan_Baku/stok_sn/' + id_barang + '/' + id_sumber, function(data) {
            snList = data || [];
            renderSnOptions();
        });
    }

    function updateStokGudang(idBarang, qtyDelta) {
        var $btn = $('span#b' + idBarang);
        if (!$btn.length) {
            return;
        }
        var cur = parseInt(String($btn.text()).replace(/,/g, ''), 10) || 0;
        $btn.text(cur + qtyDelta);
    }

    function cleanupModalBackdrops() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({
            overflow: '',
            paddingRight: ''
        });
    }

    function showHapusPakaiModal($el) {
        hapusPakaiPending = {
            id: $el.data('id'),
            id_barang: $el.data('id_barang'),
            qty: parseInt($el.data('qty'), 10) || 0,
            barang: $el.data('barang'),
            tanggal: $el.data('tanggal')
        };
        $('#hapus-pakai-detail').html(
            '<div><strong>' + hapusPakaiPending.barang + '</strong></div>' +
            '<div>Qty: ' + hapusPakaiPending.qty + '</div>' +
            '<div>Tanggal: ' + hapusPakaiPending.tanggal + '</div>'
        );
        var el = document.getElementById('modalHapusPakai');
        if (el && el.parentElement !== document.body) {
            document.body.appendChild(el);
        }
        modalHapusPakai = bootstrap.Modal.getOrCreateInstance(el);
        modalHapusPakai.show();
    }

    $(document).ready(function() {
        var modalPakai = document.getElementById('exampleModal4');
        var modalHapus = document.getElementById('modalHapusPakai');
        if (modalPakai && modalPakai.parentElement !== document.body) {
            document.body.appendChild(modalPakai);
        }
        if (modalHapus && modalHapus.parentElement !== document.body) {
            document.body.appendChild(modalHapus);
        }

        $('#modalHapusPakai').on('hidden.bs.modal', function() {
            if ($('.modal.show').length === 0) {
                cleanupModalBackdrops();
            }
        });

        $('select.tize').selectize();

        $('#tb_barang').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": 50,
            "scrollY": 300,
            "dom": "lfrti",
            "columnDefs": [
                { "searchable": false, "targets": [0, 2] }
            ]
        });

        loadRiwayatPakai('this');
    })

    $(document).on('click', '.riwayat-filter', function() {
        loadRiwayatPakai($(this).data('period'));
    });

    $(document).on('click', '.hapus-riwayat-pakai', function(e) {
        e.preventDefault();
        e.stopPropagation();
        showHapusPakaiModal($(this));
    });

    $(document).on('click', '#btnHapusPakaiConfirm', function() {
        if (!hapusPakaiPending) {
            return;
        }
        var $btn = $(this).prop('disabled', true);
        $.ajax({
            url: '<?= PV::BASE_URL ?>Stok_Pakai/hapus_pakai',
            type: 'POST',
            data: {
                id: hapusPakaiPending.id
            },
            success: function(res) {
                res = String(res).trim();
                if (res === '0') {
                    if (modalHapusPakai) {
                        modalHapusPakai.hide();
                    }
                    updateStokGudang(hapusPakaiPending.id_barang, hapusPakaiPending.qty);
                    hapusPakaiPending = null;
                    loadRiwayatPakai(riwayatPeriod);
                } else {
                    alert(res || 'Gagal menghapus');
                }
            },
            error: function() {
                alert('Gagal menghapus riwayat pakai');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    $("select[name=sds]").on("change", function() {
        if (hasSnItem) {
            renderSnOptions();
        }
    });

    $("#sn_select").on("change", function() {
        var maxQty = $(this).find(":selected").data("qty");
        if (maxQty) {
            $("#qty_input").attr("max", maxQty);
            if (parseInt($("#qty_input").val(), 10) > maxQty) {
                $("#qty_input").val(maxQty);
            }
        } else {
            $("#qty_input").val(1).removeAttr("max");
        }
    });

    $(document).on("submit", "#formPakaiStok", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (String(res).trim() === '0') {
                    qty_in = $("input[name=qty]").val();
                    var new_qty = (qty - qty_in);
                    alert("Pakai Success!");
                    $("span#" + id).html(new_qty);
                    var pakaiModal = bootstrap.Modal.getInstance(document.getElementById('exampleModal4'));
                    if (pakaiModal) {
                        pakaiModal.hide();
                    }
                    loadRiwayatPakai(riwayatPeriod);
                } else {
                    alert(res);
                }
            }
        });
    });

    $("span.pakai").click(function() {
        var id_barang = $(this).attr("data-id_barang");
        var id_sumber = $(this).attr("data-id_sumber");
        var has_sn = $(this).attr("data-has_sn");
        qty = $(this).text();
        id = $(this).attr("id");
        $("input#id_barang").val(id_barang);
        $("input#id_sumber").val(id_sumber);
        $("select[name=sds]").val("0");
        if (has_sn == "1") {
            setSnMode(true);
            loadSnList(id_barang, id_sumber);
        } else {
            setSnMode(false);
        }
    })

    var click = 0;
</script>