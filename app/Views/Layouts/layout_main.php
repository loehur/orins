<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	<title>Orins | <?= $data['title'] ?></title>
	<link href="<?= $this->ASSETS_URL ?>css/styles.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css" rel="stylesheet" />
	<link rel="icon" type="image/x-icon" href="<?= $this->ASSETS_URL ?>assets/img/favicon.png" />
	<script src="<?= $this->ASSETS_URL ?>js/feather.min.js" crossorigin="anonymous"></script>

	<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/fontawesome-free-6.4.0-web/css/all.css" rel="stylesheet">
	<link href="<?= $this->ASSETS_URL ?>plugins/toggle/css/bootstrap-toggle.min.css" rel="stylesheet">

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
			padding-top: 12px;
		}

		.selectize-input::after {
			visibility: hidden;
		}

		main {
			margin-bottom: 20px;
		}
	</style>
</head>

<?php $t = $data['title']; ?>

<?php
$cols = "id_toko, id_pelanggan, ref";
$where = "id_afiliasi = " . $this->userData['id_toko'] . " AND status_order = 1 AND cancel = 0 AND tuntas = 0 GROUP BY id_toko, id_pelanggan, ref";
$aff_ = $this->model('M_DB_1')->get_cols_where('order_data', $cols, $where, 1);
$aff_c = count($aff_);
?>

<body class="nav-fixed">
	<nav class="topnav navbar navbar-expand shadow-sm border-bottom-1 justify-content-between justify-content-sm-start bg-light" id="sidenavAccordion">
		<button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0 pt-3" id="sidebarToggle"><i data-feather="menu"></i></button>
		<a class="navbar-brand pe-3 ps-4 ps-lg-2" id="sync" href="<?= $this->BASE_URL ?>Log/sync"><?= strtoupper($this->userData['nama_toko']) ?></a>
		<ul class="navbar-nav align-items-center ms-auto ms">
			<!-- User Dropdown-->
			<?php if (in_array($this->userData['user_tipe'], $this->pMaster)) { ?>
				<li class="nav-item dropdown no-caret dropdown-user me-1">
					<a class="btn bg-white rounded btn-transparent-dark dropdown-toggle border" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<b><i class="fa-solid fa-repeat"></i></b>
					</a>
					<div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
						<?php
						foreach ($this->dToko as $dt) { ?>
							<a class="dropdown-item sync" href="<?= $this->BASE_URL ?>Log/change_toko/<?= $dt['id_toko'] ?>"><?= $dt['nama_toko'] ?></a>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
			<li class="nav-item dropdown no-caret dropdown-user me-3">
				<a class="btn rounded btn-transparent-dark dropdown-toggle border bg-white" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<b><?= strtoupper($this->userData['nama']) ?></b>
				</a>
				<div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
					<h6 class="dropdown-header d-flex align-items-center">
						<div class="dropdown-user-details">
							<div class="dropdown-user-details-name"><?= $this->userData['nama'] ?></div>
						</div>
					</h6>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="<?= $this->BASE_URL ?>Akun">
						<div class="dropdown-item-icon"><i data-feather="settings"></i></div>
						Account
					</a>
					<a class="dropdown-item" href="<?= $this->BASE_URL ?>Login_99/logout">
						<div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
						Logout
					</a>
				</div>
			</li>
		</ul>
	</nav>
	<div id="layoutSidenav">
		<div id="layoutSidenav_nav">
			<nav class="sidenav sidenav-light bg-light border">
				<div class="sidenav-menu">
					<div class="nav accordion" id="accordionSidenav">
						<!-- Sidenav Menu Heading (Account)-->
						<?php if (in_array($this->userData['user_tipe'], $this->pCS)) { ?>
							<!-- Sidenav Accordion (Dashboard)-->
							<a class="nav-link <?= (str_contains($t, "Buka Order")) ? 'active' : 'collapsed' ?> mt-2" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseNewOrder" aria-expanded="true" aria-controls="collapseNewOrder">
								<div class="nav-link-icon"><i data-feather="plus-square"></i></div>
								Buka Order
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Buka Order")) ? 'show' : '' ?>" id="collapseNewOrder" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<a class="nav-link <?= ($t == "Buka Order - Umum") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Buka_Order/index/1">Umum</a>
									<a class="nav-link <?= ($t == "Buka Order - Rekanan") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Buka_Order/index/2">Rekanan</a>
								</nav>
							</div>
							<!-- Sidenav Accordion (Dashboard)-->
							<a class="nav-link <?= (str_contains($t, "Afiliasi Order")) ? 'active' : 'collapsed' ?> mt-2" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAff" aria-expanded="true" aria-controls="collapseAff">
								<div class="nav-link-icon"><i data-feather="plus-square"></i></div>
								Afiliasi Order <span class="badge bg-danger-soft text-danger ms-auto"><?= $aff_c ?></span>
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Afiliasi Order")) ? 'show' : '' ?>" id="collapseAff" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<?php foreach ($aff_ as $af) {
										$toko = $this->model('Arr')->get($this->dToko, "id_toko", "nama_toko", $af['id_toko']); ?>
										<a class="nav-link <?= ($t == "Afiliasi Order - " . $af['ref']) ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Buka_Order_Aff/index/<?= $af['ref'] ?>"><?= $toko ?> - C#<?= $af['id_pelanggan'] ?></a>
									<?php } ?>
								</nav>
							</div>
							<!-- Sidenav Accordion (Dashboard)-->
							<a class="nav-link <?= (str_contains($t, "Data Order")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#dataOrder" aria-expanded="true" aria-controls="dataOrder">
								<div class="nav-link-icon"><i data-feather="file-text"></i></div>
								Data Order
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Data Order")) ? 'show' : '' ?>" id="dataOrder" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<a class="nav-link <?= ($t == "Data Order Proses") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Data_Order/index/0">Proses</a>
									<a class="nav-link <?= ($t == "Data Order Customer") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Data_Operasi/index/0/0">Customer</a>
									<a class="nav-link <?= ($t == "Data Order Tuntas") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Data_Operasi/index/0/1">Tuntas</a>
								</nav>
							</div>
							<a class="nav-link <?= (str_contains($t, "Pelanggan")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlows" aria-expanded="false" aria-controls="collapseFlows">
								<div class="nav-link-icon"><i data-feather="user"></i></div>
								Pelanggan
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Pelanggan")) ? 'show' : '' ?>" id="collapseFlows" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "Pelanggan Umum") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Pelanggan/index/1">Umum</a>
									<a class="nav-link <?= ($t == "Pelanggan Rekanan") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Pelanggan/index/2">Rekanan</a>
								</nav>
							</div>

							<a class="nav-link <?= (str_contains($t, "CS Fitur")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlowCSF" aria-expanded="false" aria-controls="collapseFlowCSF">
								<div class="nav-link-icon"><i data-feather="columns"></i></div>
								CS Fitur
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "CS Fitur")) ? 'show' : '' ?>" id="collapseFlowCSF" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "CS Fitur - Item Detail") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Group_Detail_CS">Item Detail (+)</a>
								</nav>
							</div>
						<?php } ?>

						<?php if (in_array($this->userData['user_tipe'], $this->pProduksi)) { ?>
							<a class="nav-link <?= (str_contains($t, "SPK_C")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#SPK_C" aria-expanded="true" aria-controls="SPK">
								<div class="nav-link-icon"><i data-feather="file-text"></i></div>
								SPK - Harian
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "SPK_C")) ? 'show' : '' ?>" id="SPK_C" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<?php foreach ($this->dDvs as $dv) {
										if ($dv['viewer'] <> 0) { ?>
											<a class="nav-link <?= ($t == "SPK_C - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>SPK_C/index/<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></a>
									<?php }
									} ?>
								</nav>
							</div>
							<a class="nav-link <?= (str_contains($t, "SPK_R")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#SPK" aria-expanded="true" aria-controls="SPK">
								<div class="nav-link-icon"><i data-feather="file-text"></i></div>
								SPK - Rekap
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "SPK_R")) ? 'show' : '' ?>" id="SPK" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
									<?php foreach ($this->dDvs as $dv) {
										if ($dv['viewer'] <> 0) { ?>
											<a class="nav-link <?= ($t == "SPK_R - " . $dv['divisi']) ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>SPK/index/<?= $dv['id_divisi'] ?>"><?= $dv['divisi'] ?></a>
									<?php }
									} ?>
								</nav>
							</div>
						<?php } ?>

						<?php if (in_array($this->userData['user_tipe'], $this->pKasir)) { ?>
							<hr class="p-0 m-0">
							<!-- CASHIER PANEL -->
							<a class="nav-link <?= (str_contains($t, "Cashier")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseCashier" aria-expanded="false" aria-controls="collapseFlows">
								<div class="nav-link-icon"><i data-feather="credit-card"></i></div>
								Cashier
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Cashier")) ? 'show' : '' ?>" id="collapseCashier" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "Cashier - Setoran") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Setoran">Setoran</a>
									<a class="nav-link <?= ($t == "Cashier - Non Tunai") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Non_Tunai_C">Transaksi Non Tunai</a>
								</nav>
							</div>
						<?php } ?>

						<?php if (in_array($this->userData['user_tipe'], $this->pAdmin)) { ?>
							<hr class="p-0 m-0">
							<a class="nav-link <?= (str_contains($t, "User")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlowsUser" aria-expanded="false" aria-controls="collapseFlows">
								<div class="nav-link-icon"><i data-feather="user"></i></div>
								Orins User
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "User")) ? 'show' : '' ?>" id="collapseFlowsUser" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "User Kasir") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>User/index/2">Kasir</a>
									<a class="nav-link <?= ($t == "User CS") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>User/index/3">CS</a>
									<a class="nav-link <?= ($t == "User Produksi") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>User/index/4">Produksi</a>
								</nav>
							</div>
							<a class="nav-link <?= (str_contains($t, "Karyawan")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlows" aria-expanded="false" aria-controls="collapseFlows">
								<div class="nav-link-icon"><i data-feather="user"></i></div>
								Karyawan
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Karyawan")) ? 'show' : '' ?>" id="collapseFlows" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "Karyawan Aktif") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Karyawan">Aktif</a>
									<a class="nav-link" href="#">Tidak Aktif</a>
								</nav>
							</div>
							<a class="nav-link <?= (str_contains($t, "Set Produksi")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFlows2" aria-expanded="false" aria-controls="collapseFlows">
								<div class="nav-link-icon"><i data-feather="tool"></i></div>
								Pengaturan Produksi
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Set Produksi")) ? 'show' : '' ?>" id="collapseFlows2" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "Set Produksi - Divisi") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Divisi">Divisi</a>
									<a class="nav-link <?= ($t == "Set Produksi - Group Detail") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Group_Detail">Kelompok Detail</a>
									<a class="nav-link <?= ($t == "Set Produksi - Produk") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Produk">Produk Produksi</a>
								</nav>
							</div>
						<?php } ?>

						<?php if (in_array($this->userData['user_tipe'], $this->pFinance)) { ?>
							<hr class="p-0 m-0">
							<!-- FINANCE PANEL -->
							<a class="nav-link <?= (str_contains($t, "Finance")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFlows">
								<div class="nav-link-icon"><i data-feather="server"></i></div>
								Finance
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Finance")) ? 'show' : '' ?>" id="collapseFinance" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "Finance - Non Tunai") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Non_Tunai">Transaksi Non Tunai</a>
									<a class="nav-link <?= ($t == "Finance - Setoran") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Setoran_F">Setoran Kasir</a>
								</nav>
							</div>
						<?php } ?>

						<?php if (in_array($this->userData['user_tipe'], $this->pMaster)) { ?>
							<hr class="p-0 m-0">
							<!-- MASTER PANEL -->
							<a class="nav-link <?= (str_contains($t, "Managment")) ? 'active' : 'collapsed' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseToko" aria-expanded="false" aria-controls="collapseFlows">
								<div class="nav-link-icon"><i data-feather="server"></i></div>
								Managment
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</a>
							<div class="collapse <?= (str_contains($t, "Managment")) ? 'show' : '' ?>" id="collapseToko" data-bs-parent="#accordionSidenav">
								<nav class="sidenav-menu-nested nav">
									<a class="nav-link <?= ($t == "Managment - Toko") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Toko_Daftar">Data Toko</a>
									<a class="nav-link <?= ($t == "Managment - Admin Toko") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Toko_Admin">Admin Toko</a>
									<a class="nav-link <?= ($t == "Managment - Admin Officer") ? 'active' : '' ?>" href="<?= $this->BASE_URL ?>Admin_Officer">Admin Officer</a>
								</nav>
							</div>
						<?php } ?>
					</div>
				</div>
				<!-- Sidenav Footer-->
				<div class="sidenav-footer">
					<div class="sidenav-footer-content">
						<div class="sidenav-footer-subtitle">Logged in as:</div>
						<div class="sidenav-footer-title"><?= $this->userData['nama'] ?></div>
					</div>
				</div>
			</nav>
		</div>
		<div id="layoutSidenav_content">
			<main>
				<div id="content"></div>
			</main>
		</div>
	</div>
	<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
	<script src="<?= $this->ASSETS_URL ?>js/scripts.js"></script>
</body>

</html>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

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

	var time = new Date().getTime();
	$(document.body).bind("mousemove keypress", function(e) {
		time = new Date().getTime();
	});

	function refresh() {
		if (new Date().getTime() - time >= 420000)
			window.location.reload(true);
		else
			setTimeout(refresh, 10000);
	}
	setTimeout(refresh, 10000);
</script>