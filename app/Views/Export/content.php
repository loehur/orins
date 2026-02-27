<main>
    <div class="ms-3 me-3 bg-white">
        <div class="rounded border border-primary px-3 pt-2 pb-1">
            <div class="fw-bold mb-2"><span class="text-primary">Period</span> <small class="text-muted">(max 3 bulan / 92 hari)</small></div>
            <div class="row">
                <div class="col" style="min-width:270px;min-width:300px">
                    <form class="export-form" action="<?php PV::BASE_URL ?>Export/export" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Production Sales</span>
                            <input name="date_from" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <input name="date_to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form class="export-form" action="<?php PV::BASE_URL ?>Export/export_pbarang" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Item Sales</span>
                            <input name="date_from" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <input name="date_to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form class="export-form" action="<?php PV::BASE_URL ?>Export/export_paket" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-primary" style="width: 120px;">Package Sales</span>
                            <input name="date_from" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <input name="date_to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-primary">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form class="export-form" action="<?php PV::BASE_URL ?>Export/export_p" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-success" style="width: 120px;">Payment</span>
                            <input name="date_from" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <input name="date_to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-success">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form class="export-form" action="<?php PV::BASE_URL ?>Export/export_ed" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-danger" style="width: 120px;">Extra Discount</span>
                            <input name="date_from" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <input name="date_to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-danger">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form class="export-form" action="<?php PV::BASE_URL ?>Export/export_sc" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-success" style="width: 120px;">Surcharge</span>
                            <input name="date_from" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <input name="date_to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-success">Export</button>
                        </div>
                    </form>
                </div>
                <div class="col" style="min-width:270px;min-width:300px">
                    <form class="export-form" action="<?php PV::BASE_URL ?>Export/export_pc" method="POST">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-dark" style="width: 120px;">Petty Cash</span>
                            <input name="date_from" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <input name="date_to" type="date" min="2023-07-01" max="<?= date("Y-m-d") ?>" class="form-control" required>
                            <button type="submit" class="btn bg-gradient btn-dark">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<style>
.export-toast { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
</style>

<script>
function showToast(message, type) {
    type = type || 'warning';
    var container = document.querySelector('.toast-container');
    var bgClass = type === 'danger' ? 'bg-danger' : 'bg-warning text-dark';
    var icon = type === 'danger' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle';

    var toastEl = document.createElement('div');
    toastEl.className = 'toast export-toast align-items-center border-0 ' + bgClass;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = '<div class="d-flex">' +
        '<div class="toast-body d-flex align-items-center">' +
        '<i class="fas ' + icon + ' me-2 fs-5 flex-shrink-0"></i>' +
        '<span>' + message + '</span>' +
        '</div>' +
        '<button type="button" class="btn-close ' + (type === 'danger' ? 'btn-close-white' : '') + ' me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
        '</div>';

    container.appendChild(toastEl);

    var toast = new bootstrap.Toast(toastEl, { delay: 4500 });
    toastEl.addEventListener('hidden.bs.toast', function() { toastEl.remove(); });
    toast.show();
}

document.querySelectorAll('.export-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        var from = form.querySelector('input[name="date_from"]');
        var to = form.querySelector('input[name="date_to"]');
        if (!from.value || !to.value) return;
        var fromDate = new Date(from.value);
        var toDate = new Date(to.value);
        if (fromDate > toDate) {
            e.preventDefault();
            showToast('Tanggal From tidak boleh melewati Date To', 'danger');
            return;
        }
        var days = Math.round((toDate - fromDate) / 86400000);
        if (days > 92) {
            e.preventDefault();
            showToast('Maksimal periode 3 bulan (92 hari)', 'warning');
            return;
        }
    });
});
</script>
