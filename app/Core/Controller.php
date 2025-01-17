<?php

require 'app/Config/PV.php';

class Controller extends PV
{
    public $userData, $dToko, $dDvs, $dProduk, $dProdukAll, $dDetailGroup, $dDetailGroupAll, $dDetailItem, $dDetailItemAll, $dSPK, $dUser, $dPelanggan, $dPelangganAll, $dKaryawan, $dKaryawanAll;
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
            header("location: " . PV::BASE_URL . "Login");
        }
    }

    public function data_order()
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
                        $this->userData['color'] = $dt['color'];
                    }
                }

                $this->dDvs = $_SESSION['data_divisi'];
                $this->dDetailGroup = $_SESSION['detail_group'];
                $this->dDetailItem = $_SESSION['detail_item'];
                $this->dSPK = $_SESSION['spk_divisi'];
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
        $_SESSION['user_data'] = $this->db(0)->get_where_row('user', $where);

        $whereToko = "id_toko = " . $this->userData['id_toko'];
        $_SESSION['data_toko'] = $this->db(0)->get('toko', 'id_toko');
        $_SESSION['data_divisi'] = $this->db(0)->get_where('divisi', "id_toko LIKE '%|" . $this->userData['id_toko'] . "|%'", "id_divisi");
        $_SESSION['spk_divisi'] = $this->db(0)->get('spk_dvs');
        $_SESSION['detail_group'] = $this->db(0)->get('detail_group');
        $_SESSION['detail_item'] = $this->db(0)->get_order('detail_item', "detail_item ASC");
        $_SESSION['data_user'] = $this->db(0)->get_where('user', $whereToko);

        $wherePel = $whereToko . " AND en = 1 ORDER BY freq DESC";
        $_SESSION['data_pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePel);

        $wherePelAll = "en = 1 ORDER BY freq DESC";
        $_SESSION['data_pelanggan_all'] = $this->db(0)->get_where('pelanggan', $wherePelAll);

        $_SESSION['karyawan'] = $this->db(0)->get_where('karyawan', $whereToko . " AND en = 1");
        $_SESSION['karyawan_all'] = $this->db(0)->get_where('karyawan', "en = 1", "id_karyawan");
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ' . PV::BASE_URL . "Login");
    }

    function cek_cookie()
    {
        if (isset($_COOKIE["ORINSESSID"])) {
            $cookie_value = $this->model("Enc")->dec_2($_COOKIE["ORINSESSID"]);
            $user_data = unserialize($cookie_value);

            if (isset($user_data['user']) && isset($user_data['device'])) {
                $device = $_SERVER['HTTP_USER_AGENT'];
                if ($user_data['device'] == $device) {
                    $this->set_login($user_data);
                }
            }
        }
    }

    function set_login($userData = [])
    {
        //LOGIN
        $where = "id_user = " . $userData['id_user'];
        $userData = $this->db(0)->get_where_row('user', $where);

        $_SESSION['login_orins'] = TRUE;
        $_SESSION['user_data'] = $userData;
        $this->userData = $_SESSION['user_data'];
        $this->dataSynchrone();
    }

    public function db($db = 0)
    {
        $file = "M_DB";
        require_once "app/Models/" . $file . ".php";
        return new $file($db);
    }

    public function data($file)
    {
        require_once "app/Data/" . $file . ".php";
        return new $file();
    }
}
