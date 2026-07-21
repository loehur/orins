<?php

return [
        [
            'access' => [9],
            'name' => 'Driver',
            'active' => ['Driver - Pickup List'],
            'icon' => 'package',
            'icon-color' => "purple",
            'sub' => [
                [
                    'name' => 'Pickup List',
                    'link' => 'Driver_JL',
                    'active' => 'Driver - Pickup List'
                ],
            ]
        ],
        [
            'access' => [3],
            'name' => 'Buka Order',
            'active' => ['Buka Order - Umum', 'Buka Order - Rekanan', 'Buka Order - Online', 'Buka Order - Stok'],
            'icon' => 'plus-square',
            'icon-color' => "success",
            'sub' => [
                [
                    'name' => 'Umum',
                    'link' => 'Buka_Order/index/1',
                    'active' => 'Buka Order - Umum'
                ],
                [
                    'name' => 'R/D',
                    'link' => 'Buka_Order/index/2',
                    'active' => 'Buka Order - Rekanan'
                ],
                [
                    'name' => 'Online',
                    'link' => 'Buka_Order/index/3',
                    'active' => 'Buka Order - Online'
                ],
                [
                    'name' => 'Stok Produksi',
                    'link' => 'Buka_Order/index/100',
                    'active' => 'Buka Order - Stok'
                ]
            ]
        ],
        [
            'access' => [3],
            'name' => 'Data Proses',
            'active' => ['Data Order - Proses (Umum)', 'Data Order - Proses (R/D)', 'Data Order - Proses (Online)', 'Data Order - Proses (Stok)', 'Data Order - AFF (IN)', 'Data Order - AFF (OUT)'],
            'icon' => 'file-text',
            'icon-color' => "success",
            'sub' => [
                [
                    'name' => 'Umum',
                    'link' => 'Data_Order/index/0/1',
                    'active' => 'Data Order - Proses (Umum)'
                ],
                [
                    'name' => 'R/D',
                    'link' => 'Data_Order/index/0/2',
                    'active' => 'Data Order - Proses (R/D)'
                ],
                [
                    'name' => 'Online',
                    'link' => 'Data_Order/index/0/3',
                    'active' => 'Data Order - Proses (Online)'
                ],
                [
                    'name' => 'Stok Produksi',
                    'link' => 'Data_Order/index/0/100',
                    'active' => 'Data Order - Proses (Stok)'
                ],
                [
                    'name' => 'AFF - &nbsp;<i class="fa-solid fa-arrow-down text-success"></i>',
                    'link' => 'Data_Order/index/0/4',
                    'active' => 'Data Order - AFF (IN)'
                ],
                [
                    'name' => 'AFF - &nbsp;<i class="fa-solid fa-arrow-up text-danger"></i>',
                    'link' => 'Data_Order/index/0/5',
                    'active' => 'Data Order - AFF (OUT)'
                ]
            ]
        ],
        [
            'access' => [3],
            'name' => 'Data Order',
            'active' => ['Data Order - Piutang', 'Data Order - Customer', 'Data Order - Tuntas', 'Data Order - Referensi', 'Data Produksi', 'Data Order - Harian'],
            'icon' => 'file-text',
            'icon-color' => "purple",
            'sub' => [
                [
                    'name' => 'Produksi',
                    'link' => 'Data_Produksi',
                    'active' => 'Data Produksi'
                ],
                [
                    'name' => 'Berjalan',
                    'link' => 'Data_Operasi/index/0/0',
                    'active' => 'Data Order - Customer'
                ],
                [
                    'name' => 'Harian',
                    'link' => 'Penjualan',
                    'active' => 'Data Order - Harian'
                ],
                [
                    'name' => 'Tuntas',
                    'link' => 'Data_Operasi/index/0/1',
                    'active' => 'Data Order - Tuntas'
                ],
                [
                    'name' => 'Referensi',
                    'link' => 'Cek_Ref',
                    'active' => 'Data Order - Referensi'
                ],
            ]
        ],
        [
            'access' => [3],
            'name' => 'Data Piutang',
            'active' => ['Piutang - Umum', 'Piutang - R/D', 'Piutang - Online'],
            'icon' => 'file-text',
            'icon-color' => "warning",
            'sub' => [
                [
                    'name' => 'Umum',
                    'link' => 'Data_Piutang/index/1',
                    'active' => 'Piutang - Umum'
                ],
                [
                    'name' => 'R/D',
                    'link' => 'Data_Piutang/index/2',
                    'active' => 'Piutang - R/D'
                ],
                [
                    'name' => 'Online',
                    'link' => 'Data_Piutang/index/3',
                    'active' => 'Piutang - Online'
                ],
            ]
        ],
        [
            'access' => [3],
            'name' => 'Fitur CS',
            'active' => ['Fitur CS - Item Detail', 'Stok Harga', 'QR Code Generator'],
            'icon' => 'columns',
            'icon-color' => "info",
            'sub' => [
                [
                    'name' => 'Item Detail (+)',
                    'link' => 'Group_Detail_CS',
                    'active' => 'Fitur CS - Item Detail'
                ],
                [
                    'name' => 'Stok Harga',
                    'link' => 'Gudang_Stok',
                    'active' => 'Stok Harga'
                ],
                [
                    'name' => 'QR Generator',
                    'link' => 'QR_Generator',
                    'active' => 'QR Code Generator'
                ],
            ]
        ],
        [
            'access' => [3],
            'name' => 'Pelanggan',
            'active' => ['Pelanggan - Umum', 'Pelanggan - Rekanan', 'Pelanggan - Online'],
            'icon' => 'user',
            'icon-color' => "info",
            'sub' => [
                [
                    'name' => 'Umum',
                    'link' => 'Pelanggan/index/1',
                    'active' => 'Pelanggan - Umum'
                ],
                [
                    'name' => 'Rekanan',
                    'link' => 'Pelanggan/index/2',
                    'active' => 'Pelanggan - Rekanan'
                ],
                [
                    'name' => 'Online',
                    'link' => 'Pelanggan/index/3',
                    'active' => 'Pelanggan - Online'
                ],
            ]
        ],
        [
            'access' => [2],
            'name' => 'Deposit',
            'active' => ['Deposit - Topup'],
            'icon' => 'credit-card',
            'icon-color' => "success",
            'sub' => [
                [
                    'name' => 'Topup',
                    'link' => 'Deposit/i/1',
                    'active' => 'Deposit - Topup'
                ],
            ]
        ],
        [
            'access' => [2],
            'name' => 'Kasir',
            'active' => ['Cashier - Setoran', 'Cashier - Setoran Riwayat', 'Cashier - Non Tunai', 'Cashier - Afiliasi', 'Barang - Masuk', 'Barang - Riwayat Bulanan', 'Rekap Penjualan', 'Cashier - Retur Barang', 'Petty Cash', 'Barang - Riwayat Jual'],
            'icon' => 'archive',
            'icon-color' => "purple",
            'sub' => [
                [
                    'name' => 'Setoran',
                    'link' => 'Setoran',
                    'active' => 'Cashier - Setoran'
                ],
                [
                    'name' => 'Barang Masuk',
                    'link' => 'Barang_Masuk',
                    'active' => 'Barang - Masuk'
                ],
                [
                    'name' => 'Petty Cash',
                    'link' => 'Petty_Cash',
                    'active' => 'Petty Cash'
                ],
                [
                    'name' => 'Non Tunai',
                    'link' => 'Non_Tunai_C',
                    'active' => 'Cashier - Non Tunai'
                ],
                [
                    'name' => 'Riwayat Barang Mutasi',
                    'link' => 'Barang_Riwayat_B',
                    'active' => 'Barang - Riwayat Bulanan'
                ],
                [
                    'name' => 'Riwayat Barang Jual',
                    'link' => 'Riwayat_Jual',
                    'active' => 'Barang - Riwayat Jual'
                ],
                [
                    'name' => 'Afiliasi',
                    'link' => 'Afiliasi_C',
                    'active' => 'Cashier - Afiliasi'
                ],
                [
                    'name' => 'Retur Barang',
                    'link' => 'Retur_Barang_C',
                    'active' => 'Cashier - Retur Barang'
                ],
            ]
        ],
        [
            'access' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            'name' => 'Tiket',
            'active' => ['Tiket - Proses', 'Tiket - Selesai'],
            'icon' => 'life-buoy',
            'icon-color' => "info",
            'sub' => [
                [
                    'name' => 'Proses',
                    'link' => 'Tiket/index/proses',
                    'active' => 'Tiket - Proses'
                ],
                [
                    'name' => 'Selesai',
                    'link' => 'Tiket/index/selesai',
                    'active' => 'Tiket - Selesai'
                ],
            ]
        ],
        [
            'access' => [1, 108],
            'name' => 'Laporan',
            'active' => ['Laporan - Penjualan', 'Audit - Data Export'],
            'icon' => 'trello',
            'icon-color' => "success",
            'sub' => [
                [
                    'name' => 'Penjualan',
                    'link' => 'Laporan_Penjualan',
                    'active' => 'Laporan - Penjualan'
                ],
                [
                    'name' => 'Data Export',
                    'link' => 'Export',
                    'active' => 'Audit - Data Export'
                ],
            ]
        ],
        [
            'access' => [4],
            'name' => 'Data Produksi',
            'active' => ['Stok Bahan Baku'],
            'icon' => 'layers',
            'icon-color' => "purple",
            'sub' => [
                [
                    'name' => 'Stok Bahan Baku',
                    'link' => 'Stok_Bahan_Baku',
                    'active' => 'Stok Bahan Baku'
                ],
            ]
        ],
        [
            'access' => [8],
            'name' => 'Tax',
            'active' => ['Audit - Afiliasi', 'Audit - Afiliasi Riwayat', 'MyOB - Code'],
            'icon' => 'anchor',
            'icon-color' => "danger",
            'sub' => [
                [
                    'name' => 'Transaksi Afiliasi',
                    'link' => 'Afiliasi',
                    'active' => 'Audit - Afiliasi'
                ],
                [
                    'name' => 'Riwayat Afiliasi',
                    'link' => 'Afiliasi_Riwayat',
                    'active' => 'Audit - Afiliasi Riwayat'
                ],
                [
                    'name' => 'MyOB Code',
                    'link' => 'MyOB_Code',
                    'active' => 'MyOB - Code'
                ],
            ]
        ],
        [
            'access' => [7],
            'name' => 'Master Gudang',
            'active' => ['Gudang - Input', 'Gudang - Barang', 'Stok - Transfer', 'Barang - Riwayat', 'Gudang Penjualan', 'Pelanggan - Gudang', 'Gudang - Barang Masuk', 'Gudang - Retur Barang', 'Gudang - Supplier', 'Stok Harga', 'Stok Pakai'],
            'icon' => 'box',
            'icon-color' => "success",
            'sub' => [
                [
                    'name' => 'Input',
                    'link' => 'Gudang_Input',
                    'active' => 'Gudang - Input'
                ],
                [
                    'name' => 'Transfer Stok',
                    'link' => 'Stok_Transfer',
                    'active' => 'Stok - Transfer'
                ],
                [
                    'name' => 'Penjualan',
                    'link' => 'Gudang_Penjualan',
                    'active' => 'Gudang Penjualan'
                ],
                [
                    'name' => 'Stok Harga',
                    'link' => 'Gudang_Stok',
                    'active' => 'Stok Harga'
                ],
                [
                    'name' => 'Stok Pakai',
                    'link' => 'Stok_Pakai',
                    'active' => 'Stok Pakai'
                ],
                [
                    'name' => 'Barang Retur',
                    'link' => 'Gudang_BMasuk',
                    'active' => 'Gudang - Barang Masuk'
                ],
                [
                    'name' => 'Retur Barang',
                    'link' => 'Retur_Barang_G',
                    'active' => 'Gudang - Retur Barang'
                ],
                [
                    'name' => 'Supplier',
                    'link' => 'Gudang_Supplier',
                    'active' => 'Gudang - Supplier'
                ],
                [
                    'name' => 'Pelanggan',
                    'link' => 'Pelanggan/index/0',
                    'active' => 'Pelanggan - Gudang'
                ],
                [
                    'name' => 'Barang',
                    'link' => 'Gudang_Barang',
                    'active' => 'Gudang - Barang'
                ],
                [
                    'name' => 'Riwayat Barang',
                    'link' => 'Barang_Riwayat',
                    'active' => 'Barang - Riwayat'
                ],
            ]
        ],
        [
            'access' => [6, 7],
            'name' => 'Audit',
            'active' => ['Audit - Barang Masuk', 'Audit - Kas Kecil', 'Barang - Riwayat Audit', 'Audit - Barang Keluar'],
            'icon' => 'check-square',
            'icon-color' => "info",
            'sub' => [
                [
                    'name' => 'Barang Masuk',
                    'link' => 'Audit_BMasuk',
                    'active' => 'Audit - Barang Masuk'
                ],
                [
                    'name' => 'Pengeluaran Kasir',
                    'link' => 'Audit_KasKecil',
                    'active' => 'Audit - Kas Kecil'
                ],
                [
                    'name' => 'Retur Barang',
                    'link' => 'Audit_BKeluar',
                    'active' => 'Audit - Barang Keluar'
                ],
                [
                    'name' => 'Riwayat Barang (A)',
                    'link' => 'Barang_Riwayat_A',
                    'active' => 'Barang - Riwayat Audit'
                ],
            ]
        ],
        [
            'access' => [5],
            'name' => 'Finance',
            'active' => ['Finance - Non Tunai', 'Finance - Non Tunai Riwayat', 'Finance - Setoran', 'Finance - Akun Pembayaran', 'CodGen', 'Audit - Gudang Jual', 'Office - Kas', 'Petty Cash Finance', 'Penjualan SDS'],
            'icon' => 'dollar-sign',
            'icon-color' => "success",
            'sub' => [
                [
                    'name' => 'Transaksi Non Tunai',
                    'link' => 'Non_Tunai',
                    'active' => 'Finance - Non Tunai'
                ],
                [
                    'name' => 'Setoran Kasir',
                    'link' => 'Setoran_F',
                    'active' => 'Finance - Setoran'
                ],
                [
                    'name' => 'Akun Pembayaran',
                    'link' => 'Akun_Pembayaran',
                    'active' => 'Finance - Akun Pembayaran'
                ],
                [
                    'name' => 'Kas Kantor',
                    'link' => 'Office_Kas',
                    'active' => 'Office - Kas'
                ],
                [
                    'name' => 'Petty Cash',
                    'link' => 'Petty_Cash_F',
                    'active' => 'Petty Cash Finance'
                ],
                [
                    'name' => 'Penjualan Gudang',
                    'link' => 'Audit_GudangJual',
                    'active' => 'Audit - Gudang Jual'
                ],
                [
                    'name' => 'Penjualan SDS',
                    'link' => 'Penjualan_SDS',
                    'active' => 'Penjualan SDS'
                ],
                [
                    'name' => 'Stok Harga',
                    'link' => 'Gudang_Stok',
                    'active' => 'Stok Harga'
                ],
                [
                    'name' => 'Riwayat Non Tunai',
                    'link' => 'Non_Tunai_Riwayat',
                    'active' => 'Finance - Non Tunai Riwayat'
                ],
                [
                    'name' => 'CodGen',
                    'link' => 'CodGen',
                    'active' => 'CodGen'
                ],
            ]
        ],
        [
            'access' => [1],
            'name' => 'Pengaturan Produk',
            'active' => ['Produk - Detail Produksi', 'Produk - Detail Jasa', 'Produk - Produksi', 'Produk - Jasa', 'Produk - Paket'],
            'icon' => 'tool',
            'icon-color' => "dark",
            'sub' => [
                [
                    'name' => 'Detail - Produksi',
                    'link' => 'Group_Detail/index/0',
                    'active' => 'Produk - Detail Produksi'
                ],
                [
                    'name' => 'Detail - Jasa',
                    'link' => 'Group_Detail/index/1',
                    'active' => 'Produk - Detail Jasa'
                ],
                [
                    'name' => 'Produk - Produksi',
                    'link' => 'Produk/index/0',
                    'active' => 'Produk - Produksi'
                ],
                [
                    'name' => 'Produk - Jasa',
                    'link' => 'Produk/index/1',
                    'active' => 'Produk - Jasa'
                ],
            ]
        ],
        [
            'access' => [1, 2],
            'name' => 'List Paket',
            'active' => ['Paket - Umum', 'Paket - Rekanan', 'Paket - Online'],
            'icon' => 'plus-square',
            'icon-color' => "purple",
            'sub' => [
                [
                    'name' => 'Umum',
                    'link' => 'Paket/index/1',
                    'active' => 'Paket - Umum'
                ],
                [
                    'name' => 'R/D',
                    'link' => 'Paket/index/2',
                    'active' => 'Paket - Rekanan'
                ],
                [
                    'name' => 'Online',
                    'link' => 'Paket/index/3',
                    'active' => 'Paket - Online'
                ]
            ]
        ],
        [
            'access' => [0],
            'name' => 'Managment',
            'active' => ['Managment - Divisi Produksi', 'Managment - Data Toko', 'Managment - SPV Toko', 'Managment - Office User'],
            'icon' => 'server',
            'icon-color' => "danger",
            'sub' => [
                [
                    'name' => 'Divisi Produksi',
                    'link' => 'Divisi',
                    'active' => 'Managment - Divisi Produksi'
                ],
                [
                    'name' => 'Data Toko',
                    'link' => 'Toko_Daftar',
                    'active' => 'Managment - Data Toko'
                ],
                [
                    'name' => 'SPV Toko',
                    'link' => 'SPV_Toko',
                    'active' => 'Managment - SPV Toko'
                ],
                [
                    'name' => 'Office User',
                    'link' => 'Admin_Officer',
                    'active' => 'Managment - Office User'
                ],
            ]
        ]
];

