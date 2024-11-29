<?php

$yearNow = date('Y');
$mdata = [
	[
		'access' => [$this->pCS],
		'name' => 'Buka Order',
		'icon' => 'plus-square',
		'sub' => [
			[
				'name' => 'Umum',
				'link' => 'Buka_Order/index/1'
			],
			[
				'name' => 'Rekanan',
				'link' => 'Buka_Order/index/2'
			]
		]
	],
	[
		'access' => [$this->pCS],
		'name' => 'Data Order',
		'icon' => 'file-text',
		'sub' => [
			[
				'name' => 'Proses',
				'link' => 'Data_Order/index/0'
			],
			[
				'name' => 'Piutang',
				'link' => 'Data_Piutang'
			],
			[
				'name' => 'Customer',
				'link' => 'Data_Operasi/index/0/0'
			],
			[
				'name' => 'Tuntas',
				'link' => 'Data_Operasi/index/0/' . $yearNow
			],
		]
	],
	[
		'access' => [$this->pCS],
		'name' => 'Pelanggan',
		'icon' => 'user',
		'sub' => [
			[
				'name' => 'Umum',
				'link' => 'Pelanggan/index/1'
			],
			[
				'name' => 'Rekanan',
				'link' => 'Pelanggan/index/2'
			],
		]
	],
	[
		'access' => [$this->pKasir],
		'name' => 'Deposit',
		'icon' => 'credit-card',
		'sub' => [
			[
				'name' => 'Topup',
				'link' => 'Deposit/i/1'
			],
			[
				'name' => 'List',
				'link' => 'Deposit/i/2'
			],
		]
	],
	[
		'access' => [$this->pGudang],
		'name' => 'Master Gudang',
		'icon' => 'box',
		'sub' => [
			[
				'name' => 'Input',
				'link' => 'Gudang_Input'
			],
			[
				'name' => 'Barang',
				'link' => 'Gudang_Barang'
			],
			[
				'name' => 'Stok',
				'link' => 'Stok'
			],
			[
				'name' => 'Riwayat',
				'link' => 'Input'
			],
		]
	],
	[
		'access' => [$this->pMaster],
		'name' => 'Managment',
		'icon' => 'server',
		'sub' => [
			[
				'name' => 'Divisi Produksi',
				'link' => 'Divisi'
			],
			[
				'name' => 'Data Toko',
				'link' => 'Toko_Daftar'
			],
			[
				'name' => 'Admin Toko',
				'link' => 'Toko_Admin'
			],
			[
				'name' => 'Admin Officer',
				'link' => 'Admin_Officer'
			],
		]
	]
];
