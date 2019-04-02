<?php

require_once('Color.class.php');

Class Vertex {
    static $verbose = false;
    private $_x;
    private $_y;
    private $_z;
    private $_w;
    private $_color;

    function __construct($params) {
        extract($params);
        $this->_x = $x;
        $this->_y = $y;
        $this->_z = $z;
        $this->_w = isset($w) ? $w : 1.0;
        $this->_color = isset($color) ? $color : new Color(['rgb' => 0xffffff]);

        if (self::$verbose)
            echo $this." constructed\n";
    }

    static function doc() {
        return file_get_contents('Vertex.doc.txt');
    }

    function __destruct() {
        if (self::$verbose)
            echo $this." destructed.\n";
    }

    function __toString() {
        return "Vertex( x: ".sprintf("%.2f", $this->_x).", y: ".sprintf("%.2f", $this->_y).", z:".sprintf("%.2f", $this->_z).", w:".sprintf("%.2f", $this->_w)
            .(self::$verbose ? ', '.$this->_color : '')." )";
    }

    function __set($name, $value) {
        if (in_array($name, ['x','y','z','w','color']))
            $this->{"_$name"} = $value;
    }

    function __get($name) {
        if (in_array($name, ['x','y','z','w','color']))
            return $this->{"_$name"};
    }
}