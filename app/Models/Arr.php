<?php

class Arr
{
    function get($arr, $id_name, $col_name, $id_target)
    {
        foreach ($arr as $a) {
            if ($a[$id_name] == $id_target) {
                return $a[$col_name];
            }
        }
    }
}
