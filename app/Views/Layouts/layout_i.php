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
			padding-top: 12px;
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
?>

<body class="nav-fixed disabled_all">
	<div id="content"></div>
	<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
	<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
	<script src="<?= PV::ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
	<script>
	(function() {
		function removeOrphanBackdrops() {
			document.querySelectorAll('.modal-backdrop').forEach(function(b) {
				b.remove();
			});
			if (!document.querySelector('.modal.show')) {
				document.body.classList.remove('modal-open');
				document.body.style.removeProperty('overflow');
				document.body.style.removeProperty('padding-right');
			}
		}
		window.cleanupBootstrapModals = function() {
			if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
				document.querySelectorAll('.modal').forEach(function(el) {
					var inst = bootstrap.Modal.getInstance(el);
					if (inst) {
						try { inst.hide(); } catch (e) {}
						try { inst.dispose(); } catch (e) {}
					}
					el.classList.remove('show');
					el.style.display = 'none';
					el.setAttribute('aria-hidden', 'true');
					el.removeAttribute('aria-modal');
					el.removeAttribute('role');
				});
			}
			removeOrphanBackdrops();
		};
		document.addEventListener('hidden.bs.modal', function() {
			setTimeout(removeOrphanBackdrops, 10);
		});
		document.addEventListener('keydown', function(e) {
			if (e.key !== 'Escape') return;
			if (!document.querySelector('.modal.show') && (
				document.querySelector('.modal-backdrop') || document.body.classList.contains('modal-open')
			)) {
				removeOrphanBackdrops();
			}
		});
		document.addEventListener('click', function(e) {
			if (!e.target || !e.target.classList || !e.target.classList.contains('modal-backdrop')) return;
			if (!document.querySelector('.modal.show')) removeOrphanBackdrops();
		}, true);
	})();
	</script>
	<script src="<?= PV::ASSETS_URL ?>js/scripts.js"></script>
</body>

</html>