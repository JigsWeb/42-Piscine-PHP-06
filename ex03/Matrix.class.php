<?php

require_once('Vector.class.php');

class Matrix {
    static $verbose = false;
    const   IDENTITY    = '_identity',
            SCALE       = '_scale',
            RX          = '_rotation_x',
            RY          = '_rotation_y',
            RZ          = '_rotation_z',
            TRANSLATION = '_translation',
            PROJECTION  = '_projection';
    private $vtcX;
    private $vtcY;
    private $vtcZ;
    private $vtx0;

    function __construct($params = []) {
        $this->$params['preset']($params);
    }

    private function _identity($params) {
        $this->vtcX = new Vertex(['x' => 1, 'y' => 0, 'z' => 0, 'w' => 0]);
        $this->vtcY = new Vertex(['x' => 0, 'y' => 1, 'z' => 0, 'w' => 0]);
        $this->vtcZ = new Vertex(['x' => 0, 'y' => 0, 'z' => 1, 'w' => 0]);
        $this->vtx0 = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 0, 'z' => 0, 'w' => 2])]);
    }

    private function _scale($params) {

    }

    private function _rotation_x($params) {

    }

    private function _rotation_y($params) {

    }

    private function _rotation_z($params) {

    }

    private function _translation($params) {

    }

    private function _projection($params) {

    }

    function __toString() {
        $res = "M | vtcX | vtcY | vtcZ | vtxO\n";
        $res .= "-----------------------------\n";
        $res .= "x | ".sprintf("%.2f", $this->vtcX->x)." | ".sprintf("%.2f", $this->vtcY->x)." | ".sprintf("%.2f", $this->vtcZ->x)." | ".sprintf("%.2f", $this->vtx0->x)."\n";
        $res .= "y | ".sprintf("%.2f", $this->vtcX->y)." | ".sprintf("%.2f", $this->vtcY->y)." | ".sprintf("%.2f", $this->vtcZ->y)." | ".sprintf("%.2f", $this->vtx0->y)."\n";
        $res .= "z | ".sprintf("%.2f", $this->vtcX->z)." | ".sprintf("%.2f", $this->vtcY->z)." | ".sprintf("%.2f", $this->vtcZ->z)." | ".sprintf("%.2f", $this->vtx0->z)."\n";
        $res .= "w | ".sprintf("%.2f", $this->vtcX->w)." | ".sprintf("%.2f", $this->vtcY->w)." | ".sprintf("%.2f", $this->vtcZ->w)." | ".sprintf("%.2f", $this->vtx0->w)."\n";
        return $res;
    }
}

$I = new Matrix( array( 'preset' => Matrix::IDENTITY ) );
print( $I . PHP_EOL . PHP_EOL );