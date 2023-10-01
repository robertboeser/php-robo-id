<?php
namespace Robo\RoboID;

class RoboJSON {
    protected $time;        // hex string // 16 hex characters
    protected $rand;        // hex string // 20 hex characters
    protected $xrnd;
    protected $type = 'S';  // S => last 32 bits of $rand
                            // L => last 80 bits of $rand
                            // X => append $xrnd to long

    /*
    * create new id by setting $timed and $random
    */
    function init() {
        $time = floor(microtime(true) * 1000);
        $this->time = bin2hex(pack('J', $time));
        $this->rand = bin2hex(random_bytes(10)); // 80 bits entropy
        $this->xrnd = bin2hex(random_bytes(10)); // 80 bits entropy
    }

    /*
    * create an associative array
    */
    function jsonSerialize() {
        return [
            'v' => $this->type,
            't' => $this->time,
            'r' => $this->rand,
            'x' => $this->xrnd,
        ];
    }

    /*
    * set properties from an array or object
    */
    function jsonUnserialize($json) {
        if(is_array($json)) {
            $this->setType($json['v']);
            $this->setTime($json['t']);
            $this->setRand($json['r']);
            $this->setXRnd($json['x'] ?? '');
        }
        if(is_object($json)) {
            $this->setType($json->v);
            $this->setTime($json->t);
            $this->setRand($json->r);
            $this->setXRnd($json->x ?? '');
        }
    }

    /*
    * export id as json
    */
    function export() {
        return json_encode($this->jsonSerialize());
    }

    /*
    * import id from json
    */
    function import($str) {
        $this->jsonUnserialize(json_decode($str));
    }

    /*
    * parse is an alias of import
    */
    function parse($id) {
        $this->import($id);
    }

    /*
    * render id in the specific format
    */
    function format() {
        return $this->export();
    }

    /*
    * generate new id and render it in the specific format
    */
    function genID() {
        $this->init();
        return $this->format();
    }

    /*
    * set version type
    */
    function setType($val) {
        $val = strtoupper($val);
        if(in_array($val, ['S', 'L', 'X'])) {
            $this->type = $val;
        } else {
            $this->type = 'S';
        }
    }

    /* ********** helper functions ********** */

    protected function setTime($hex) {
        $time = str_pad($hex, 16, '0', STR_PAD_LEFT);
        $this->time = $time;
        return $time;
    }

    protected function setRand($hex) {
        $rand = str_pad($hex, 20, '0', STR_PAD_LEFT); // pad to 80 bit hex
        $this->rand = $rand;
        return $rand;
    }

    protected function setXRnd($hex) {
        if(!$hex) $hex = '';
        $rand = str_pad($hex, 20, '0', STR_PAD_LEFT); // pad to 80 bit hex
        $this->xrnd = $rand;
        return $rand;
    }
}
