<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=490, user-scalable=no">
	<meta name="description" content="" />
	<meta name="author" content="" />
	<title>Orins | <?= $data['title'] ?></title>
	<link href="<?= PV::ASSETS_URL ?>css/styles.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css" rel="stylesheet" />
	<link rel="icon" type="image/x-icon" href="<?= PV::ASSETS_URL ?>assets/img/favicon.png" />
	<script src="<?= PV::ASSETS_URL ?>js/feather.min.js" crossorigin="anonymous"></script>

	<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/fontawesome-free-6.4.0-web/css/all.css" rel="stylesheet">
	<link href="<?= PV::ASSETS_URL ?>plugins/toggle/css/bootstrap-toggle.min.css" rel="stylesheet">

	<?php $fontStyle = "system-ui, -apple-system, \"Segoe UI\", Roboto, Arial, sans-serif;" ?>

	<style>
		html {
			height: 100%;
		}

		html .table {
			font-family: <?= $fontStyle ?>;
		}

		html .content {
			font-family: <?= $fontStyle ?>;
		}

		html body {
			font-family: <?= $fontStyle ?>;
		}

		.selectize-control {
			padding: 0px;
		}

		.selectize-input {
			border: none;
		}

		.selectize-input::after {
			visibility: hidden;
		}

		main {
			margin-bottom: 20px;
		}

		.col-t {
			line-height: 100%;
		}

		.selectize-dropdown .option,
		.selectize-dropdown [data-selectable],
		.selectize-dropdown .optgroup-header {
			padding: 6px 15px !important;
		}

		input:focus,
		.form-select:focus,
		.btn:focus,
		select:focus,
		textarea,
		input.form-control:focus {
			outline: none !important;
			outline-width: 0 !important;
			box-shadow: none;
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
		}

		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
			display: none;
		}
	</style>
</head>

<?php
$t = $data['title'];
$openPrioritasMenu = str_contains($t, "Afiliasi Order") || str_contains($t, "SPK - Lanjutan");
?>

<body class="nav-fixed">
	<?php include_once('topnav.php'); ?>
	<?php include_once('menu.php'); ?>
	<?php include_once('notifikasi_offcanvas.php'); ?>
	<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
	<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
	<script src="<?= PV::ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
	<script src="<?= PV::ASSETS_URL ?>js/scripts.js"></script>
	<script>
	$("a#sync").click(function(e) {
		e.preventDefault();
		sync();
	});

	function sync() {
		$.ajax({
			url: $("a#sync").attr('href'),
			type: "GET",
			success: function() {
				location.reload(true);
			},
		});
	}

	$("a.sync").click(function(e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('href'),
			type: "GET",
			success: function() {
				sync();
			},
		});
	});

	(function() {
		var NOTIF_INTERVAL_MS = 300000; // 5 menit
		var pollUrl = '<?= PV::BASE_URL ?>Notifikasi/poll?t=' + encodeURIComponent(<?= json_encode($t) ?>);
		var $body = $('#notifOffcanvasBody');
		var $badge = $('#notifBadge');
		var $toggle = $('#prioritasToggle');
		var $panel = $('#collapsePrioritas');
		var $submenu = $('#prioritasSubmenu');
		var polling = false;

		function setPrioritasOpen(open) {
			if ($toggle.length === 0 || $panel.length === 0) {
				return;
			}
			$toggle.toggleClass('collapsed', !open);
			$toggle.toggleClass('active', open);
			$toggle.attr('aria-expanded', open ? 'true' : 'false');
			$panel.toggleClass('show', open);
			if (!open) {
				$panel[0].style.height = '';
			}
		}

		function applyPrioritasBadge(count) {
			var $prioBadge = $('#menuPrioritasBadge');
			if ($prioBadge.length === 0) {
				return;
			}
			if (count > 0) {
				$prioBadge.text(count).removeClass('d-none');
			} else {
				$prioBadge.addClass('d-none');
			}
		}

		function applyNotifBadge(count) {
			if (count > 0) {
				$badge.text(count > 99 ? '99+' : count).removeClass('d-none');
			} else {
				$badge.addClass('d-none');
			}
		}

		function applyPollResponse(response) {
			if (!response || String(response).indexOf('pusatNotifikasiPayload') === -1) {
				return false;
			}
			$body.html(response);

			var $payload = $('#pusatNotifikasiPayload');
			var notifCount = parseInt($payload.attr('data-notif-count') || 0, 10);
			applyNotifBadge(notifCount);

			var $prioItems = $payload.find('#menuPrioritasItems');
			if ($prioItems.length && $submenu.length) {
				$submenu.html($prioItems.prop('outerHTML'));
				applyPrioritasBadge(parseInt($prioItems.attr('data-count') || 0, 10));
			}

			if (typeof feather !== 'undefined') {
				feather.replace();
			}
			return true;
		}

		function loadPusatNotifikasi(showLoading) {
			if (polling) {
				return;
			}
			polling = true;
			if (showLoading) {
				$body.html('<div class="text-muted small py-2">Memuat...</div>');
			}
			$.ajax({
				url: pollUrl,
				type: 'GET',
				cache: false,
				success: function(response) {
					if (!applyPollResponse(response)) {
						if (showLoading) {
							$body.html('<div class="text-danger small py-2">Gagal memuat notifikasi</div>');
						}
					}
				},
				error: function() {
					if (showLoading) {
						$body.html('<div class="text-danger small py-2">Gagal memuat notifikasi</div>');
					}
				},
				complete: function() {
					polling = false;
				}
			});
		}

		if ($toggle.length && $panel.length) {
			$toggle.on('click', function(e) {
				e.preventDefault();
				var opening = !$panel.hasClass('show');
				setPrioritasOpen(opening);
				if (opening && $submenu.children().length === 0) {
					$submenu.html('<span class="nav-link py-1 text-muted ps-3">Memuat...</span>');
					loadPusatNotifikasi(false);
				}
			});
		}

		$('#offcanvasNotifikasi').on('show.bs.offcanvas', function() {
			loadPusatNotifikasi(true);
		});

		$(function() {
			loadPusatNotifikasi(<?= !empty($openPrioritasMenu) ? 'true' : 'false' ?>);
			setInterval(function() {
				loadPusatNotifikasi(false);
			}, NOTIF_INTERVAL_MS);
		});
	})();

	(function() {
		var base = '<?= PV::BASE_URL ?>';

		function closeMobileSidenav() {
			if (window.innerWidth < 992 && document.body.classList.contains('sidenav-toggled')) {
				document.body.classList.remove('sidenav-toggled');
				localStorage.setItem('sb|sidebar-toggle', 'false');
			}
		}

		function closeNotifOffcanvas() {
			var oc = document.getElementById('offcanvasNotifikasi');
			if (oc && typeof bootstrap !== 'undefined') {
				var inst = bootstrap.Offcanvas.getInstance(oc);
				if (inst) {
					inst.hide();
				}
			}
		}

		$(document).on('click', '#layoutSidenav a.nav-link[href^="' + base + '"], #offcanvasNotifikasi a[href^="' + base + '"]', function(e) {
			var href = $(this).attr('href');
			if (!href || href.indexOf('javascript') === 0) {
				return;
			}

			// Tutup menu overlay (mobile) saat pilih halaman — dulu otomatis karena full reload
			closeMobileSidenav();
			closeNotifOffcanvas();

			if (typeof appNavigateFromHref === 'function' && appNavigateFromHref(href)) {
				e.preventDefault();
				$('#layoutSidenav a.nav-link.active').removeClass('active');
				$(this).addClass('active');
			}
		});
		window.addEventListener('popstate', function(ev) {
			if (ev.state && ev.state.href && typeof appNavigateFromHref === 'function') {
				appNavigateFromHref(ev.state.href);
			}
		});
	})();
	</script>
</body>

</html>