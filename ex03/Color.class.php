<?php

class Color {
    static $verbose = false;
    public $red;
    public $green;
    public $blue;

    static function doc() {
        return file_get_contents('Color.doc.txt');
    }

    function __construct($arr) {
        if (isset($arr['rgb']))
            $arr = $this->convertRGBValue(intval($arr['rgb']));
        $this->_init($arr);
        if (self::$verbose)
            echo $this->__toString()." constructed.\n";
    }

    function __destruct() {
        if (self::$verbose)
            echo $this." destructed.\n";
    }
    
    function __toString() {
        return "Color( red: ".sprintf("%3s", $this->red).", green: ".sprintf("%3s", $this->green).", blue: ".sprintf("%3s", $this->blue)." )";
    }

    private function convertRGBValue($rgb) {
        return array_combine(['red', 'green', 'blue'], [
            ($rgb&0xff0000)>>16,
            ($rgb&0x00ff00)>>8,
            ($rgb&0x0000ff)
        ]);
    }

    private function _init($arr) {
        $this->red = intval($arr['red']);
        $this->green = intval($arr['green']);
        $this->blue = intval($arr['blue']);
    }

    function add($c) {
        return new Color([
            'red' => $this->red + $c->red,
            'green' => $this->green + $c->green,
            'blue' => $this->blue + $c->blue
        ]);
    }

    function sub($c) {
        return new Color([
            'red' => $this->red - $c->red,
            'green' => $this->green - $c->green,
            'blue' => $this->blue - $c->blue
        ]);
    }

    function mult($f) {
        return new Color([
            'red' => $this->red * $f,
            'green' => $this->green * $f,
            'blue' => $this->blue * $f
        ]);
    }
}
