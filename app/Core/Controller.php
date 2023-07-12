<?php

require 'app/Config/Public_Variables.php';

class Controller extends Public_Variables
{

    public $userData, $dToko, $dDvs, $dDvsAll, $dProduk, $dProdukAll, $dDetailGroup, $dDetailGroupAll, $dDetailItem, $dDetailItemAll, $dSPK, $dSPK_all, $dUser, $dPelanggan, $dPelangganAll, $dKaryawan, $dKaryawanAll;
    public $v_viewer, $v_content, $v_load;

    public function view($file, $data = [])
    {
        require_once "app/Views/" . $file . ".php";
    }

    public function model($file)
    {
        require_once "app/Models/" . $file . ".php";
        return new $file();
    }

    public function session_cek()
    {
        if (isset($_SESSION['login_orins'])) {
            if ($_SESSION['login_orins'] == False) {
                $this->logout();
            }
        } else {
            header("location: " . $this->BASE_URL . "Login");
        }
    }

    public function data()
    {
        if (isset($_SESSION['login_orins'])) {
            if ($_SESSION['login_orins'] == true) {
                $this->userData = $_SESSION['user_data'];
                $this->dToko = $_SESSION['data_toko'];

                foreach ($this->dToko as $dt) {
                    if ($dt['id_toko'] == $this->userData['id_toko']) {
                        $this->userData['nama_toko'] = $dt['nama_toko'];
                        $this->userData['sub_nama'] = $dt['sub_nama'];
                        $this->userData['alamat'] = $dt['alamat'];
                    }
                }

                $this->dDvs = $_SESSION['data_divisi'];
                $this->dDvsAll = $_SESSION['data_divisi_all'];
                $this->dProduk = $_SESSION['produk'];
                $this->dProdukAll = $_SESSION['produk_all'];
                $this->dDetailGroup = $_SESSION['detail_group'];
                $this->dDetailItem = $_SESSION['detail_item'];
                $this->dSPK = $_SESSION['spk_divisi'];
                $this->dDetailGroupAll = $_SESSION['detail_group_all'];
                $this->dDetailItemAll = $_SESSION['detail_item_all'];
                $this->dSPK_all = $_SESSION['spk_divisi_all'];
                $this->dUser = $_SESSION['data_user'];
                $this->dPelanggan = $_SESSION['data_pelanggan'];
                $this->dPelangganAll = $_SESSION['data_pelanggan_all'];
                $this->dKaryawan = $_SESSION['karyawan'];
                $this->dKaryawanAll = $_SESSION['karyawan_all'];
            }
        }
    }

    public function dataSynchrone()
    {
        $where = "id_user = '" . $this->userData["id_user"] . "'";

        unset($_SESSION['user_data']);
        $_SESSION['user_data'] = $this->model('M_DB_1')->get_where_row('user', $where);

        $whereToko = "id_toko = " . $this->userData['id_toko'];
        $_SESSION['data_toko'] = $this->model('M_DB_1')->get('toko');
        $_SESSION['data_divisi'] = $this->model('M_DB_1')->get_where('divisi', $whereToko . " ORDER BY sort ASC");
        $_SESSION['data_divisi_all'] = $this->model('M_DB_1')->get_order('divisi', "sort ASC");
        $_SESSION['spk_divisi'] = $this->model('M_DB_1')->get_where('spk_dvs', $whereToko);
        $_SESSION['spk_divisi_all'] = $this->model('M_DB_1')->get('spk_dvs');
        $_SESSION['produk'] = $this->model('M_DB_1')->get_where('produk', $whereToko . " ORDER BY produk ASC");
        $_SESSION['produk_all'] = $this->model('M_DB_1')->get_order('produk', 'produk ASC');
        $_SESSION['detail_group'] = $this->model('M_DB_1')->get_where('detail_group', $whereToko . " ORDER BY sort ASC");
        $_SESSION['detail_group_all'] = $this->model('M_DB_1')->get_order('detail_group', "sort ASC");
        $_SESSION['detail_item'] = $this->model('M_DB_1')->get_where('detail_item', $whereToko . " ORDER BY detail_item ASC");
        $_SESSION['detail_item_all'] = $this->model('M_DB_1')->get_order('detail_item', "detail_item ASC");
        $_SESSION['data_user'] = $this->model('M_DB_1')->get('user', $whereToko);
        $_SESSION['data_pelanggan'] = $this->model('M_DB_1')->get_where('pelanggan', $whereToko);
        $_SESSION['data_pelanggan_all'] = $this->model('M_DB_1')->get('pelanggan');
        $_SESSION['karyawan'] = $this->model('M_DB_1')->get_where('karyawan', $whereToko);
        $_SESSION['karyawan_all'] = $this->model('M_DB_1')->get('karyawan');
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ' . $this->BASE_URL . "Login");
    }
}
