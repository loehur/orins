<?php

class WA extends Controller
{
    function send_wa($token, $target, $text, $group)
    {
        return $this->model("WA_Fonnte")->send($token, $target, $text, $group);
    }
}
