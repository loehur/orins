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

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
	<!-- FONT -->

	<?php $fontStyle = "'Titillium Web', sans-serif;" ?>

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
		var $collapse = $('#collapsePrioritas');
		if ($collapse.length === 0) {
			return;
		}

		var prioritasLoaded = false;
		var prioritasUrl = '<?= PV::BASE_URL ?>Menu/prioritas/' + encodeURIComponent(<?= json_encode($t) ?>);

		function loadPrioritasMenu() {
			if (prioritasLoaded) {
				return;
			}
			$('#menuPrioritasContent').html('<span class="nav-link py-1 text-muted">Memuat...</span>');
			$('#menuPrioritasContent').load(prioritasUrl, function(response, status) {
				if (status !== 'success') {
					$('#menuPrioritasContent').html('<span class="nav-link py-1 text-danger">Gagal memuat menu</span>');
					return;
				}
				prioritasLoaded = true;
				var count = parseInt($('#menuPrioritasItems').data('count') || 0, 10);
				var $badge = $('#menuPrioritasBadge');
				if (count > 0) {
					$badge.text(count).removeClass('d-none');
				} else {
					$badge.addClass('d-none');
				}
				if (typeof feather !== 'undefined') {
					feather.replace();
				}
			});
		}

		$collapse.on('show.bs.collapse', function() {
			loadPrioritasMenu();
		});

		<?php if (!empty($openPrioritasMenu)) { ?>
		$(function() {
			loadPrioritasMenu();
		});
		<?php } ?>
	})();
	</script>
</body>

</html>