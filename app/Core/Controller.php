<?php

require 'app/Config/PV.php';

class Controller extends PV
{
    public $userData, $dToko, $dDvs, $dDvs_all, $dProduk, $dProdukAll, $dDetailGroup, $dDetailGroupAll, $dDetailItem, $dDetailItem_1, $dDetailItemVarian_1, $dDetailItemAll, $dSPK, $dUser, $dPelanggan, $dPelangganAll;
    public $v_viewer, $v_content, $v_load;
    public $dKaryawan_cs, $dKaryawan_pro, $dKaryawan_driver, $dKaryawanAll, $dKaryawanAll_cs, $dKaryawanAll_driver;

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
        $this->cek_cookie();
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
                        $this->userData['aff_id'] = $dt['aff_id'];
                        $this->userData['sub_nama'] = $dt['sub_nama'];
                        $this->userData['alamat'] = $dt['alamat'];
                        $this->userData['color'] = $dt['color'];
                    }
                }

                $this->dDvs = $_SESSION['data_divisi'];
                $this->dDvs_all = $_SESSION['data_divisi_all'];
                $this->dDetailGroup = $_SESSION['detail_group'];
                $this->dDetailItem = $_SESSION['detail_item'];
                $this->dDetailItem_1 = $_SESSION['detail_item_1'];
                $this->dDetailItemVarian_1 = $_SESSION['detail_item_varian_1'];
                $this->dSPK = $_SESSION['spk_divisi'];
                $this->dUser = $_SESSION['data_user'];
                $this->dPelanggan = $_SESSION['data_pelanggan'];
                $this->dPelangganAll = $_SESSION['data_pelanggan_all'];
                $this->dKaryawan_cs = $_SESSION['karyawan_cs'];
                $this->dKaryawan_pro = $_SESSION['karyawan_pro'];
                $this->dKaryawan_driver = $_SESSION['karyawan_driver'];

                $this->dKaryawanAll = $_SESSION['karyawan_all'];
                $this->dKaryawanAll_cs = $_SESSION['karyawan_all_cs'];
                $this->dKaryawanAll_driver = $_SESSION['karyawan_all_driver'];
            }
        }
    }

    public function dataSynchrone()
    {
        $where = "id_user = '" . $this->userData["id_user"] . "'";

        unset($_SESSION['user_data']);
        $_SESSION['user_data'] = $this->db(0)->get_where_row('user', $where);

        $whereToko = "(id_toko = " . $this->userData['id_toko'] . " OR id_toko = 0)";
        $_SESSION['data_toko'] = $this->db(0)->get('toko', 'id_toko');
        $_SESSION['data_divisi'] = $this->db(0)->get_where('divisi', "id_toko LIKE '%|" . $this->userData['id_toko'] . "|%'", "id_divisi");
        $_SESSION['data_divisi_all'] = $this->db(0)->get('divisi', 'id_divisi');
        $_SESSION['spk_divisi'] = $this->db(0)->get('spk_dvs', 'id_spk_dvs');
        $_SESSION['detail_group'] = $this->db(0)->get('detail_group', 'id_index');
        $_SESSION['detail_item'] = $this->db(0)->get('detail_item', "id_detail_item");
        $_SESSION['detail_item_1'] = $this->db(0)->get_where('detail_item', "freq > -1 ORDER BY freq DESC", "id_detail_group", 1);
        $_SESSION['detail_item_varian_1'] = $this->db(0)->get('detail_item_varian', "id_detail_item", "1");
        $_SESSION['data_user'] = $this->db(0)->get_where('user', $whereToko);

        $wherePel = $whereToko . " AND en = 1 ORDER BY freq DESC";
        $_SESSION['data_pelanggan'] = $this->db(0)->get_where('pelanggan', $wherePel, 'id_pelanggan');

        $wherePelAll = "en = 1 ORDER BY freq DESC";
        $_SESSION['data_pelanggan_all'] = $this->db(0)->get_where('pelanggan', $wherePelAll, 'id_pelanggan');

        $_SESSION['karyawan_cs'] = $this->db(0)->get_where('karyawan', $whereToko . " AND en = 1 ORDER BY freq_cs DESC", "id_karyawan");
        $_SESSION['karyawan_pro'] = $this->db(0)->get_where('karyawan', $whereToko . " AND en = 1 ORDER BY freq_pro DESC", "id_karyawan");
        $_SESSION['karyawan_driver'] = $this->db(0)->get_where('karyawan', $whereToko . " AND en = 1 ORDER BY freq_driver DESC", "id_karyawan");

        $_SESSION['karyawan_all'] = $this->db(0)->get_where('karyawan', "en >= 0 ORDER BY freq_cs DESC", "id_karyawan");
        $_SESSION['karyawan_all_cs'] = $this->db(0)->get_where('karyawan', "en = 1 ORDER BY freq_cs DESC", "id_karyawan");
        $_SESSION['karyawan_all_driver'] = $this->db(0)->get_where('karyawan', "en = 1 ORDER BY freq_driver DESC", "id_karyawan");
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
        return DB::getInstance($db);
    }

    public function data($file)
    {
        require_once "app/Data/" . $file . ".php";
        return new $file();
    }

    function valid_number($number)
    {
        if (!is_numeric($number)) {
            $number = preg_replace('/[^0-9]/', '', $number);
        }

        if (substr($number, 0, 1) == '8') {
            if (strlen($number) >= 7 && strlen($number) <= 14) {
                $fix_number = "0" . $number;
                return $fix_number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 2) == '08') {
            if (strlen($number) >= 8 && strlen($number) <= 15) {
                return $number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 3) == '628') {
            if (strlen($number) >= 9 && strlen($number) <= 16) {
                $fix_number = "0" . substr($number, 2);
                return $fix_number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 4) == '+628') {
            if (strlen($number) >= 10 && strlen($number) <= 17) {
                $fix_number = "0" . substr($number, 3);
                return $fix_number;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getStringBetween($string, $startChar, $endChar)
    {
        $startIndex = strpos($string, $startChar);
        if ($startIndex === false) {
            return ''; // Start character not found
        }

        $startIndex += strlen($startChar); // Move past the start character

        $endIndex = strpos($string, $endChar, $startIndex);
        if ($endIndex === false) {
            return ''; // End character not found after the start
        }

        $length = $endIndex - $startIndex;
        return substr($string, $startIndex, $length);
    }
}
