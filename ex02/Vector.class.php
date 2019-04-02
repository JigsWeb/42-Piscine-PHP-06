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
            $orig = new Vertex(['x' => 0, 'y' => 0, 'z' => 0, 'w' => 1]);
        $this->_x = $dest->x - $orig->x;
        $this->_y = $dest->y - $orig->y;
        $this->_z = $dest->z - $orig->z;
        $this->_w = isset($w) ? $w : 0.0;

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

Vertex::$verbose = False;

print( Vector::doc() );
Vector::$verbose = True;


$vtxO = new Vertex( array( 'x' => 0.0, 'y' => 0.0, 'z' => 0.0 ) );
$vtxX = new Vertex( array( 'x' => 1.0, 'y' => 0.0, 'z' => 0.0 ) );
$vtxY = new Vertex( array( 'x' => 0.0, 'y' => 1.0, 'z' => 0.0 ) );
$vtxZ = new Vertex( array( 'x' => 0.0, 'y' => 0.0, 'z' => 1.0 ) );

$vtcXunit = new Vector( array( 'orig' => $vtxO, 'dest' => $vtxX ) );
$vtcYunit = new Vector( array( 'orig' => $vtxO, 'dest' => $vtxY ) );
$vtcZunit = new Vector( array( 'orig' => $vtxO, 'dest' => $vtxZ ) );

print( $vtcXunit . PHP_EOL );
print( $vtcYunit . PHP_EOL );
print( $vtcZunit . PHP_EOL );

$dest1 = new Vertex( array( 'x' => -12.34, 'y' => 23.45, 'z' => -34.56 ) );
Vertex::$verbose = True;
$vtc1  = new Vector( array( 'dest' => $dest1 ) );
Vertex::$verbose = False;

$orig2 = new Vertex( array( 'x' => 23.87, 'y' => -37.95, 'z' => 78.34 ) );
$dest2 = new Vertex( array( 'x' => -12.34, 'y' => 23.45, 'z' => -34.56 ) );
$vtc2  = new Vector( array( 'orig' => $orig2, 'dest' => $dest2 ) );

print( 'Magnitude is ' . $vtc2->magnitude() . PHP_EOL );

$nVtc2 = $vtc2->normalize();
print( 'Normalized $vtc2 is ' . $nVtc2 . PHP_EOL );
print( 'Normalized $vtc2 magnitude is ' . $nVtc2->magnitude() . PHP_EOL );

print( '$vtc1 + $vtc2 is ' . $vtc1->add( $vtc2 ) . PHP_EOL );
print( '$vtc1 - $vtc2 is ' . $vtc1->sub( $vtc2 ) . PHP_EOL );
print( 'opposite of $vtc1 is ' . $vtc1->opposite() . PHP_EOL );
print( 'scalar product of $vtc1 and 42 is ' . $vtc1->scalarProduct( 42 ) . PHP_EOL );
print( 'dot product of $vtc1 and $vtc2 is ' . $vtc1->dotProduct( $vtc2 ) . PHP_EOL );
print( 'cross product of $vtc1 and $vtc2 is ' . $vtc1->crossProduct( $vtc2 ) . PHP_EOL );
print( 'cross product of $vtcXunit and $vtcYunit is ' . $vtcXunit->crossProduct( $vtcYunit ) . 'aka $vtcZunit' . PHP_EOL );
print( 'cosinus of angle between $vtc1 and $vtc2 is ' . $vtc1->cos( $vtc2 ) . PHP_EOL );
print( 'cosinus of angle between $vtcXunit and $vtcYunit is ' . $vtcXunit->cos( $vtcYunit ) . PHP_EOL );
