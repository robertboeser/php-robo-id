<?php
namespace Robo\RoboID;

class RoboHEX extends RoboJSON{

    /*
    * render id in the specific format
    */
    function format() {
        $time = substr($this->time, -12);
        $rand = $this->rand;
        if(!$this->long) {
            $rand = hex2bin($rand);
            // use last four bytes in short version
            // remove last two bits to get 30 bits entropy
            $rand = substr($rand, -4);
            $rand[3] = chr(ord($rand[3]) & 0xFC);
            $rand = bin2hex($rand);
        }
        return "$time-$rand";
    }

    /*
    * parse id from the specific format
    */
    function parse($id) {
        list($time, $rand) = explode('-', $id, 2);
        $this->setTime($time);
        $this->setRand($rand);
        $this->long = $long;
    }
}
