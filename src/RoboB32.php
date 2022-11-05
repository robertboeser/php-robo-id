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

        $rand = $this->rand;
        if(!$this->long) $rand = substr($rand, -4); // use last four bytes in short version
        $rand = Base32::encodeByteStrToCrockford(hex2bin($this->rand));
        $rand = substr($rand, 0, $this->long ? 16 : 6);

        return "$time-$rand";
    }

    /*
    * parse id from the specific format
    */
    function parse($id) {
        list($time, $rand) = explode('-', $id, 2);
        $time = str_pad($time, 16, '0', STR_PAD_LEFT);          // pad to 80 bit b32
        $time = bin2hex(Base32::decodeCrockfordToByteStr($time));
        $time = substr($time, -16);                             // use last 16 chars

        $rand = bin2hex(Base32::decodeCrockfordToByteStr($rand));
        $long = (strlen($rand) < 16);
        $this->setRand($rand);
        $this->setTime($time);
        $this->long = $long;
    }

}
