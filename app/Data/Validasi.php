<?php

class Validasi extends Controller
{
    function valid_wa($number)
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

    function valid_wa_direct($number)
    {
        if (!is_numeric($number)) {
            $number = preg_replace('/[^0-9]/', '', $number);
        }

        if (substr($number, 0, 1) == '8') {
            if (strlen($number) >= 9 && strlen($number) <= 12) {
                $fix_number = "62" . $number;
                return $fix_number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 2) == '08') {
            if (strlen($number) >= 10 && strlen($number) <= 13) {
                $fix_number = "62" . substr($number, 1);
                return $fix_number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 3) == '628') {
            if (strlen($number) >= 11 && strlen($number) <= 14) {
                return $number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 4) == '+628') {
            if (strlen($number) >= 12 && strlen($number) <= 15) {
                $fix_number = substr($number, 1);
                return $fix_number;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
