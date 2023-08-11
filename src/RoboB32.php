<?php
namespace Robo\RoboID;

use SKleeschulte\Base32;

class RoboB32 extends RoboJSON {

    /*
    * render id in the specific format
    */
    function format() {
        $time = str_pad($this->time, 20, '0', STR_PAD_LEFT);    // pad to 80 bit b32
        $time = Base32::encodeByteStrToCrockford(hex2bin($time));
        $time = substr($time, -9);                              // use last 9 chars

        $rand = Base32::encodeByteStrToCrockford(hex2bin($this->rand));

        switch($this->type) {
            case 'L':
                $rand = substr($rand, 0, 16);
            break;
            case 'X':
                $rand = substr($rand, 0, 16);
                $xrnd = Base32::encodeByteStrToCrockford(hex2bin($this->xrnd));
                $xrnd = substr($xrnd, 0, 16);
                $rand .= '-'.$xrnd;
            break;
            case 'S':
            default:
                $rand = substr($rand, 0, 6);
            break;
        }

        return "$time-$rand";
    }

    /*
    * parse id from the specific format
    */
    function parse($id) {
        list($time, $rand, $xrnd) = explode('-', "$id--", 3);
        $time = str_pad($time, 16, '0', STR_PAD_LEFT);          // pad to 80 bit b32
        $time = bin2hex(Base32::decodeCrockfordToByteStr($time));
        $time = substr($time, -16);                             // use last 16 chars

        $rand = bin2hex(Base32::decodeCrockfordToByteStr($rand));
        $xrnd = bin2hex(Base32::decodeCrockfordToByteStr(trim($xrnd, '-')));

        $type = 'S';
        if(strlen($rand) > 6) $type = 'L';
        if($xrnd) $type = 'X';

        $this->setRand($rand);
        $this->setXRnd($xrnd);
        $this->setTime($time);
        $this->setType($type);
    }

}
