<nav class="topnav navbar navbar-expand shadow-sm border-bottom-1 bg-white px-2" id="sidenavAccordion">
	<button class="border-0 bg-transparent text-secondary pt-2" id="sidebarToggle"><i class="fa-solid fa-bars"></i> Menu</button>
	<?php if (in_array($this->userData['user_tipe'], PV::OFFICE)) { ?>
		<a class="ps-lg-2 ms-auto text-end me-2 border text-success p-2 rounded text-decoration-none" id="sync" href="<?= PV::BASE_URL ?>Log/sync">SYNC</a>
	<?php } else { ?>
		<a class="ps-lg-2 ms-auto text-end me-2 border text-success p-2 rounded text-decoration-none" id="sync" href="<?= PV::BASE_URL ?>Log/sync"><?= strtoupper($this->userData['nama_toko']) ?></a>
	<?php } ?>
	<ul class="navbar-nav align-items-center">
		<!-- User Dropdown-->
		<?php if (in_array($this->userData['user_tipe'], PV::PRIV[0])) { ?>
			<li class="nav-item dropdown no-caret dropdown-user me-2">
				<a class="rounded bg-transparent text-decoration-none py-2 px-3 border bg-white" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<b><i class="fa-solid fa-repeat"></i></b>
				</a>
				<div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
					<?php
					foreach ($this->dToko as $dt) { ?>
						<a class="dropdown-item sync" href="<?= PV::BASE_URL ?>Log/change_toko/<?= $dt['id_toko'] ?>"><?= $dt['nama_toko'] ?></a>
					<?php } ?>
				</div>
			</li>
		<?php } ?>
		<li class="nav-item dropdown no-caret dropdown-user me-3">
			<a class="rounded bg-transparent text-decoration-none text-indigo p-2 dropdown-toggle border bg-white" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<b><?= strtoupper($this->userData['nama']) ?></b>
			</a>
			<div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
				<h6 class="dropdown-header d-flex align-items-center">
					<div class="dropdown-user-details">
						<div class="dropdown-user-details-name"><?= $this->userData['nama'] ?></div>
					</div>
				</h6>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="<?= PV::BASE_URL ?>Akun">
					<div class="dropdown-item-icon"><i data-feather="settings"></i></div>
					Account
				</a>
				<a class="dropdown-item" href="<?= PV::BASE_URL ?>Login_99/logout">
					<div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
					Logout
				</a>
			</div>
		</li>
	</ul>
</nav>