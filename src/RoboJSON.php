<?php
namespace Robo\RoboID;

class RoboJSON {
    protected $time;        // hex string // 16 hex characters
    protected $rand;        // hex string // 20 hex characters
    protected $long = false; // short => last 32 bits of $rand

    /*
    * create new id by setting $timed and $random
    */
    function init() {
        $time = floor(microtime(true) * 1000);
        $this->time = bin2hex(pack('J', $time));
        $rand = random_bytes(10); // 80 bits entropy
        $rand = bin2hex($rand);
        $this->rand = $this->setUuidBits($rand);
    }

    /*
    * export id as json
    */
    function export($encoding='hex') {
        $time = $this->time;
        $rand = $this->rand;

        // apply encoding
        // TODO: implement B32 and UUID support
        $encoding = 'hex';

        $json = [
            'e' => $encoding,
            'v' => $this->long ? 'L' : 'S',
            't' => $time,
            'r' => $rand,
        ];
        return json_encode($json);
    }

    /*
    * import id from json
    */
    function import($json) {
        $json = json_decode($json);
        // TODO: implement B32 and UUID support
        $rand = $json->r;
        $time = $json->t;
        $long = ($json->v !== 'S');

        $this->setTime($time);
        $this->setRand($rand);
        $this->long = $long;
    }

    /*
    * render id in the specific format
    */
    function format() {
        return $this->export();
    }

    /*
    * parse id from the specific format
    */
    function parse($id) {
        $this->import($id);
    }

    /*
    * generate new id and render it in the specific format
    */
    function genID() {
        $this->init();
        return $this->format();
    }

    /*
    * enable long version
    */
    function setLong($bool) {
        $this->long = (bool) $bool;
    }

    /* ********** helper functions ********** */

    protected function setUuidBits($hex) {
        // $bin holds the 80 least significant bits of the UUID
        $bin = hex2bin($hex);
        $bin[0] = chr((ord($bin[0]) & 0x0F) | 0xB0);  // uuid version
        $bin[2] = chr((ord($bin[2]) & 0x3F) | 0x80);  // uuid variant
        return bin2hex($bin);
    }

    protected function setTime($hex) {
        $time = str_pad($hex, 16, '0', STR_PAD_LEFT);
        $this->time - $time;
        return $time;
    }

    protected function setRand($hex) {
        $rand = str_pad($hex, 20, '0', STR_PAD_LEFT); // pad to 80 bit hex
        $rand = $this->setUuidBits($rand);
        $this->rand = $rand;
        return $rand;
    }
}
