<?php
namespace Robo\RoboID;
// https://github.com/ulid/spec
//
use SKleeschulte\Base32;

class RoboULID extends RoboJSON
{
    /*
     * render id in the specific format
     */
    function format()
    {
        $time = str_pad($this->time, 20, "0", STR_PAD_LEFT); // pad to 80 bit b32
        $time = Base32::encodeByteStrToCrockford(hex2bin($time));
        $time = substr($time, -10); // use last 10 chars

        $rand = Base32::encodeByteStrToCrockford(hex2bin($this->rand));
        $rand = substr($rand, 0, 16);

        return $time . $rand;
    }

    /*
     * parse id from the specific format
     */
    function parse($id)
    {
        $time = substr($id, 0, 10);
        $rand = substr($id, 10);

        $time = str_pad($time, 16, "0", STR_PAD_LEFT); // pad to 80 bit b32
        $time = bin2hex(Base32::decodeCrockfordToByteStr($time));
        $time = substr($time, -16); // use last 16 chars

        $rand = bin2hex(Base32::decodeCrockfordToByteStr($rand));
        $xrnd = "";

        $type = "L";

        $this->setRand($rand);
        $this->setXRnd($xrnd);
        $this->setTime($time);
        $this->setType($type);
    }
}
