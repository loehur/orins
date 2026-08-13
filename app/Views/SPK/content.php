<main>
    <!-- Main page content-->
    <div class="row mx-0">
        <div class="col-md-6 px-1 mb-2">
            <small>
                <table class="table table-sm mb-0 bg-white shadow-sm border text-sm">
                    <tr>
                        <td colspan="5" class="table-danger"><b>Tahap I</b></td>
                    </tr>
                    <?php foreach ($data['recap'] as $r) { ?>
                        <tr>
                            <td class=""><?= strtoupper($r['spk']) ?></td>
                            <td align="right"><b><?= $r['jumlah'] ?></b>pcs</td>
                            <td align="center">
                                <span class="btn-outline-primary rounded px-1 updateSPK" style="cursor: pointer;" data-order="<?= $r['order'] ?>" data-bs-toggle="modal" data-bs-target="#updateSPK">
                                    <small>Update</small>
                                </span>
                            </td>
                            <td align="center">
                                <span class="btn-outline-info rounded px-1 cekSPK" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalOrder" data-parse="<?= $data['id_divisi'] ?>" data-order="<?= $r['order'] ?>">
                                    <small>Cek</small>
                                </span>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </table>
            </small>
        </div>
        <div class="col-md-6 ps-0 pe-1">
            <small>
                <table class="table table-sm mb-0 bg-white shadow-sm border text-sm">
                    <tr>
                        <td colspan="5" class="table-warning"><b>Tahap II</b></td>
                    </tr>
                    <?php foreach ($data['recap_2'] as $r) { ?>
                        <tr>
                            <td><?= strtoupper($r['spk']) ?></td>
                            <td align="right"><b><?= $r['jumlah'] ?></b>pcs</td>
                            <td align="center">
                                <span class="btn-outline-primary rounded px-1 updateSPK" style="cursor: pointer;" data-order="<?= $r['order'] ?>" data-bs-toggle="modal" data-bs-target="#updateSPK2">
                                    <small>Update</small>
                                </span>
                            </td>
                            <td>
                                <span data-bs-toggle="modal" data-bs-target="#modalOrder" style="cursor: pointer;" class="btn-outline-info rounded px-1 cekSPK" data-parse="<?= $data['id_divisi'] ?>" data-order="<?= $r['order'] ?>">
                                    <small>Cek</small>
                                </span>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </table>
            </small>
        </div>
    </div>
</main>

<div class="modal" id="updateSPK" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update SPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>SPK/updateSPK/<?= $data['id_divisi'] ?>/1" method="POST">
                <div class="modal-body">
                    <div class="col mb-2">
                        <label class="form-label">User Produksi</label>
                        <select class="border tize" name="id_karyawan" required>
                            <option></option>
                            <?php foreach ($data['karyawan'] as $k) { ?>
                                <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col" id="cekUpdate"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="updateSPK2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update SPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>SPK/updateSPK/<?= $data['id_divisi'] ?>/2" method="POST">
                <div class="modal-body">
                    <div class="col mb-2">
                        <label class="form-label">User Produksi</label>
                        <select class="form-select tize" name="id_karyawan" required>
                            <option></option>
                            <?php foreach ($data['karyawan'] as $k) {
                                if ($k['id_toko'] == $this->userData['id_toko']) { ?>
                                    <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama'] ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col" id="cekUpdate"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="cekSPK" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">SPK Selesai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= PV::BASE_URL ?>SPK/updateSPK/<?= $data['id_divisi'] ?>" method="POST">
                <div class="modal-body">
                    <div class="col" id="cekSelesai"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="modalOrder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="cekOrder">

        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
<script>
    function showToast(message, type) {
        type = type || 'danger';
        var container = document.querySelector('.toast-container');
        if (!container) return;
        var bgClass = type === 'danger' ? 'bg-danger text-white' : type === 'success' ? 'bg-success text-white' : type === 'warning' ? 'bg-warning text-dark' : 'bg-info text-white';
        var icon = type === 'danger' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center border-0 shadow ' + bgClass;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = '<div class="d-flex"><div class="toast-body d-flex align-items-start"><i class="fas ' + icon + ' me-2 fs-5 flex-shrink-0 mt-1"></i><span class="text-sm">' + String(message).replace(/\n/g, '<br>') + '</span></div><button type="button" class="btn-close ' + (type === 'warning' ? '' : 'btn-close-white') + ' me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(toastEl);
        var toast = new bootstrap.Toast(toastEl, { delay: 4500 });
        toastEl.addEventListener('hidden.bs.toast', function() { toastEl.remove(); });
        toast.show();
    }

    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $('span.updateSPK').click(function() {
        var order = $(this).attr("data-order");
        $("div#cekUpdate").load('<?= PV::BASE_URL ?>SPK/load_update/' + order);
    });


    $('span.cekSPK').click(function() {
        var order = $(this).attr("data-order");
        var parse = $(this).attr("data-parse");
        $("div#cekOrder").load('<?= PV::BASE_URL ?>SPK/cekSPK/' + order + "/" + parse);
    });

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    showToast(res, 'danger');
                }
            },
            error: function() {
                showToast('Gagal menyimpan SPK', 'danger');
            }
        });
    });
</script>