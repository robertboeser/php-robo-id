<?php
namespace Robo\RoboID;

class RoboHEX extends RoboJSON{

    /*
    * render id in the specific format
    */
    function format() {
        $time = substr($this->time, -12);

        $rand = $this->rand;

        switch($this->type) {
            case 'L':
                // there is nothing to do here :-)
            break;
            case 'X':
                $rand .= '-'.$this->xrnd;
            break;
            case 'S':
            default:
                $rand = hex2bin($rand);
                // use last four bytes in short version
                // remove last two bits to get 30 bits entropy
                $rand = substr($rand, -4);
                $rand[3] = chr(ord($rand[3]) & 0xFC);
                $rand = bin2hex($rand);
            break;
        }

        return "$time-$rand";
    }

    /*
    * parse id from the specific format
    */
    function parse($id) {
        list($time, $rand, $xrnd) = explode('-', "$id--", 3);
        $this->setTime($time);
        $this->setRand($rand);
        $this->setXRnd(trim($xrnd, '-'));

        $type = 'S';
        if(strlen($rand) > 8) $type = 'L';
        if($xrnd) $type = 'X';
        $this->type = $type;
    }
}
