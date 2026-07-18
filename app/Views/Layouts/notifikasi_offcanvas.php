<style>
	#btnPusatNotifikasi {
		position: relative;
		line-height: 1;
		padding: .35rem .5rem;
	}

	#btnPusatNotifikasi #notifBadge {
		position: absolute;
		top: 0;
		right: 0;
		transform: translate(35%, -25%);
		font-size: 10px;
		min-width: 1.1rem;
		padding: 0 .3rem;
	}

	#offcanvasNotifikasi .offcanvas-body {
		padding-top: .75rem;
	}
</style>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNotifikasi" aria-labelledby="offcanvasNotifikasiLabel">
	<div class="offcanvas-header border-bottom py-2">
		<h5 class="offcanvas-title mb-0" id="offcanvasNotifikasiLabel">Pusat Notifikasi</h5>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
	</div>
	<div class="offcanvas-body" id="notifOffcanvasBody">
		<div class="text-muted small py-2">Memuat...</div>
	</div>
</div>
