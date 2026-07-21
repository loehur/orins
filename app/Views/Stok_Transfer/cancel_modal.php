<div class="modal fade" id="modalCancelTransfer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" id="modalCancelTransferHeader">
                <div>
                    <h5 class="modal-title mb-1" id="modalCancelTransferTitle">Batalkan Surat Transfer</h5>
                    <small class="text-muted" id="modalCancelTransferRef"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div id="cancelTransferStep1">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-25 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;">
                            <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                        </div>
                        <div>
                            <p class="mb-2">Pembatalan akan <strong>menghapus surat beserta semua item</strong> di dalamnya secara permanen.</p>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Pastikan surat masih berstatus <span class="badge bg-warning text-dark">Checking</span></li>
                                <li>Pastikan seluruh item masih berstatus <span class="badge bg-warning text-dark">Checking</span></li>
                                <li>Tindakan ini <strong>tidak dapat dibatalkan</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="cancelTransferStep2" class="d-none">
                    <div class="text-center py-2">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width:68px;height:68px;">
                            <i class="fa-regular fa-circle-xmark fa-2x"></i>
                        </div>
                        <h6 class="mb-2">Konfirmasi Akhir</h6>
                        <p class="text-muted small mb-0">Anda yakin ingin membatalkan surat transfer<br><strong class="text-dark" id="cancelTransferRefFinal"></strong>?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning px-3" id="btnCancelTransferNext">Lanjutkan</button>
                <button type="button" class="btn btn-danger px-3 d-none" id="btnCancelTransferConfirm">
                    <i class="fa-regular fa-circle-xmark me-1"></i> Ya, Batalkan Surat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        if (window.__stokTransferCancelBound) {
            return;
        }
        window.__stokTransferCancelBound = true;

        var cancelTransferId = '';
        var cancelTransferOnSuccess = null;

        function resetCancelTransferModal() {
            $('#cancelTransferStep1').removeClass('d-none');
            $('#cancelTransferStep2').addClass('d-none');
            $('#btnCancelTransferNext').removeClass('d-none').prop('disabled', false);
            $('#btnCancelTransferConfirm').addClass('d-none').prop('disabled', false).html('<i class="fa-regular fa-circle-xmark me-1"></i> Ya, Batalkan Surat');
            $('#modalCancelTransferHeader').removeClass('bg-danger bg-gradient text-white');
            $('#modalCancelTransferHeader .btn-close').removeClass('btn-close-white');
        }

        window.showStokTransferCancelModal = function(id, onSuccess) {
            cancelTransferId = id;
            cancelTransferOnSuccess = onSuccess;
            resetCancelTransferModal();
            $('#modalCancelTransferRef').text('#' + id);
            $('#cancelTransferRefFinal').text('#' + id);

            var modalEl = document.getElementById('modalCancelTransfer');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        };

        $(document).on('click', '#btnCancelTransferNext', function() {
            $('#cancelTransferStep1').addClass('d-none');
            $('#cancelTransferStep2').removeClass('d-none');
            $(this).addClass('d-none');
            $('#btnCancelTransferConfirm').removeClass('d-none');
            $('#modalCancelTransferHeader').addClass('bg-danger bg-gradient text-white');
            $('#modalCancelTransferHeader .btn-close').addClass('btn-close-white');
        });

        $(document).on('click', '#btnCancelTransferConfirm', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

            $.ajax({
                url: '<?= PV::BASE_URL ?>Stok_Transfer/cancel',
                data: {
                    id: cancelTransferId
                },
                type: 'POST',
                dataType: 'html',
                success: function(res) {
                    if (res == 0) {
                        var modalEl = document.getElementById('modalCancelTransfer');
                        bootstrap.Modal.getInstance(modalEl).hide();
                        if (typeof cancelTransferOnSuccess === 'function') {
                            cancelTransferOnSuccess(cancelTransferId);
                        }
                    } else {
                        alert(res);
                        btn.prop('disabled', false).html('<i class="fa-regular fa-circle-xmark me-1"></i> Ya, Batalkan Surat');
                    }
                },
                error: function() {
                    alert('Gagal membatalkan surat transfer.');
                    btn.prop('disabled', false).html('<i class="fa-regular fa-circle-xmark me-1"></i> Ya, Batalkan Surat');
                }
            });
        });

        $(document).on('hidden.bs.modal', '#modalCancelTransfer', function() {
            resetCancelTransferModal();
        });
    })();
</script>
