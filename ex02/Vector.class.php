<?php

require_once('Vertex.class.php');

Class Vector {
    static $verbose = false;
    private $_x;
    private $_y;
    private $_z;
    private $_w;

    function __construct($params) {
        extract($params);
        if (!isset($orig))
            $orig = new Vertex(['x' => 0, 'y' => 0, 'z' => 0]);
        $this->_x = $dest->x - $orig->x;
        $this->_y = $dest->y - $orig->y;
        $this->_z = $dest->z - $orig->z;
        $this->_w = $dest->w - $orig->w;

        if (self::$verbose)
            echo $this." constructed\n";
    }

    static function doc() {
        return file_get_contents('Vector.doc.txt');
    }

    function __destruct() {
        if (self::$verbose)
            echo $this." destructed.\n";
    }

    function __toString() {
        return "Vector( x: ".sprintf("%.2f", $this->_x).", y: ".sprintf("%.2f", $this->_y).", z:".sprintf("%.2f", $this->_z).", w:".sprintf("%.2f", $this->_w)." )";
    }

    function __get($name) {
        if (in_array($name, ['x','y','z','w']))
            return $this->{"_$name"};
    }

    function magnitude() {
        return (sqrt(($this->_x * $this->_x) + ($this->_y * $this->_y) + ($this->_z * $this->_z)));
    }

    function normalize() {
        $m = $this->magnitude();
        return new Vector(['dest' => new Vertex(['x' => $this->_x / $m, 'y' => $this->_y / $m, 'z' => $this->_z / $m])]);
    }

    function add(Vector $rhs) {
        return new Vector(['dest' => new Vertex(['x' => $this->_x + $rhs->x, 'y' => $this->_y + $rhs->y, 'z' => $this->_z + $rhs->z])]);
    }

    function sub(Vector $rhs) {
        return new Vector(['dest' => new Vertex(['x' => $this->_x - $rhs->x, 'y' => $this->_y - $rhs->y, 'z' => $this->_z - $rhs->z])]);
    }

    function opposite() {
        return new Vector(['dest' => new Vertex(['x' => -$this->_x, 'y' => -$this->_y, 'z' => -$this->_z])]);
    }

    function scalarProduct($k) {
        return new Vector(['dest' => new Vertex(['x' => $this->_x * $k, 'y' => $this->_y * $k, 'z' => $this->_z * $k])]);
    }

    function dotProduct(Vector $rhs) {
        return ($this->_x * $rhs->x) + ($this->_y * $rhs->y) + ($this->_z * $rhs->z);
    }

    function crossProduct(Vector $rhs) {
        return new Vector(['dest' => new Vertex([
            'x' => $this->_y * $rhs->z - $this->_z * $rhs->y,
            'y' => $this->_z * $rhs->x - $this->_x * $rhs->z,
            'z' => $this->_x * $rhs->y - $this->_y * $rhs->x
        ])]);
    }

    function cos(Vector $rhs) {
        return ($this->dotProduct($rhs) / ($this->magnitude() * $rhs->magnitude()));
    }
}