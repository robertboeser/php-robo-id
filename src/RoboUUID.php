<?php
namespace Robo\RoboID;

class RoboUUID extends RoboJSON{

    /*
    * render id in the specific format
    */
    function format() {
        $time = substr($this->time, -12);
        $time = hex2bin($time);
        $time[0] = chr(ord($time[0]) | 0x80);
        $time = bin2hex($time);

        $rand = $this->rand;
        if($this->type === 'S') {
            $rand = hex2bin($rand);
            // use last four bytes in short version
            // remove last two bits to get 30 bits entropy
            $rand = substr($rand, -4);
            $rand[3] = chr(ord($rand[3]) & 0xFC);
            $rand = bin2hex($rand);
            $rand = str_pad($rand, 20, '0', STR_PAD_LEFT);
        }
        $rand = $this->setUuidBits($rand);

        $time = substr($time, 0, 8).'-'.substr($time, 8, 4);
        $rand = substr($rand, 0, 4).'-'.substr($rand, 4, 4).'-'.substr($rand, 8, 12);
        return "$time-$rand";
    }

    /*
    * parse id from the specific format
    */
    function parse($id) {
        $parts = explode('-', $id, 5);
        $time = $parts[0].$parts[1];
        $rand = $parts[2].$parts[3].$parts[4];

        $time = hex2bin($time);
        $time[0] = chr(ord($time[0]) & 0x1F);
        $time = bin2hex($time);

        $this->setTime($time);
        $this->setXRnd('');
        $rand = $this->setRand($rand);
        $this->type = (substr($rand, 1, 3) !== '000' || substr($rand, 5, 3) !== '000' || substr($rand, 8, 4) !== '0000') ? 'L' : 'S';
    }

    /* ********** helper functions ********** */

    protected function setUuidBits($hex) {
        // $bin holds the 80 least significant bits of the UUID
        $bin = hex2bin($hex);
        $bin[0] = chr((ord($bin[0]) & 0x0F) | 0xB0);  // uuid version
        $bin[2] = chr((ord($bin[2]) & 0x3F) | 0x80);  // uuid variant
        return bin2hex($bin);
    }
}
