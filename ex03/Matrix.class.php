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
    private $_vtcX;
    private $_vtcY;
    private $_vtcZ;
    private $_vtx0;

    function __construct($params = []) {
        $this->$params['preset']($params);
    }

    private function _identity($params) {
        $this->_vtcX = new Vector(['dest' => new Vertex(['x' => 1, 'y' => 0, 'z' => 0])]);
        $this->_vtcY = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 1, 'z' => 0])]);
        $this->_vtcZ = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 0, 'z' => 1])]);
        $this->_vtx0 = new Vertex(['x' => 0, 'y' => 0, 'z' => 0]);
        $this->_construct_message('IDENTITY');
    }

    private function _scale($params) {
        $this->_vtcX = new Vector(['dest' => new Vertex(['x' => $params['scale'], 'y' => 0, 'z' => 0])]);
        $this->_vtcY = new Vector(['dest' => new Vertex(['x' => 0, 'y' => $params['scale'], 'z' => 0])]);
        $this->_vtcZ = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 0, 'z' => $params['scale']])]);
        $this->_vtx0 = new Vertex(['x' => 0, 'y' => 0, 'z' => 0]);
        $this->_construct_message('SCALE');
    }

    private function _rotation_x($params) {
        $this->_vtcX = new Vector(['dest' => new Vertex(['x' => 1, 'y' => 0, 'z' => 0])]);
        $this->_vtcY = new Vector(['dest' => new Vertex(['x' => 0, 'y' => cos($params['angle']), 'z' => sin($params['angle'])])]);
        $this->_vtcZ = new Vector(['dest' => new Vertex(['x' => 0, 'y' => -sin($params['angle']), 'z' => cos($params['angle'])])]);
        $this->_vtx0 = new Vertex(['x' => 0, 'y' => 0, 'z' => 0]);
        $this->_construct_message('0x ROTATION');
    }

    private function _rotation_y($params) {
        $this->_vtcX = new Vector(['dest' => new Vertex(['x' => cos($params['angle']), 'y' => 0, 'z' => -sin($params['angle'])])]);
        $this->_vtcY = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 1, 'z' => 0])]);
        $this->_vtcZ = new Vector(['dest' => new Vertex(['x' => sin($params['angle']), 'y' => 0, 'z' => cos($params['angle'])])]);
        $this->_vtx0 = new Vertex(['x' => 0, 'y' => 0, 'z' => 0]);
        $this->_construct_message('0y ROTATION');
    }

    private function _rotation_z($params) {
        $this->_vtcX = new Vector(['dest' => new Vertex(['x' => cos($params['angle']), 'y' => sin($params['angle']), 'z' => 0])]);
        $this->_vtcY = new Vector(['dest' => new Vertex(['x' => -sin($params['angle']), 'y' => cos($params['angle']), 'z' => 0])]);
        $this->_vtcZ = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 0, 'z' => 1])]);
        $this->_vtx0 = new Vertex(['x' => 0, 'y' => 0, 'z' => 0]);
        $this->_construct_message('0z ROTATION');
    }

    private function _translation($params) {
        $this->_vtcX = new Vector(['dest' => new Vertex(['x' => 1, 'y' => 0, 'z' => 0])]);
        $this->_vtcY = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 1, 'z' => 0])]);
        $this->_vtcZ = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 0, 'z' => 1])]);
        $this->_vtx0 = new Vertex(['x' => $params['vtc']->x, 'y' => $params['vtc']->y, 'z' => $params['vtc']->z]);
        $this->_construct_message('TRANSLATION');
    }

    private function _projection($params) {
        $s = tan($params['fov'] * 0.5 * M_PI / 180) * $params['near'];
        $r = $params['ratio'] * $s;
        $l = -$r; 
        $t = $s;
        $b = -$t; 
        $this->_vtcX = new Vector(['dest' => new Vertex(['x' => 2 * $params['near'] / ($r - $l), 'y' => 0, 'z' => 0])]);
        $this->_vtcY = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 2 * $params['near'] / ($t - $b), 'z' => 0])]);
        $this->_vtcZ = new Vector(['dest' => new Vertex(['x' => 0, 'y' => 0, 'z' => -(($params['far'] + $params['near']) / ($params['far'] - $params['near'])), 'w' => 0])]);
        $this->_vtx0 = new Vertex(['x' => 0, 'y' => 0, 'z' => -((2 * $params['far'] * $params['near']) / ($params['far'] - $params['near']))]);
        $this->_construct_message('PROJECTION');
    }

    function mult(Matrix $rhs) {
        $Xx = $this->_vtcX->x * $rhs->_vtcX->x + $this->_vtcY->x * $rhs->_vtcX->y + $this->_vtcZ->x * $rhs->_vtcX->z + $this->_vtx0->x * $rhs->_vtcX->w;
        $Xy = $this->_vtcX->y * $rhs->_vtcX->x + $this->_vtcY->y * $rhs->_vtcX->y + $this->_vtcZ->y * $rhs->_vtcX->z + $this->_vtx0->y * $rhs->_vtcX->w;
        $Xz = $this->_vtcX->z * $rhs->_vtcX->x + $this->_vtcY->z * $rhs->_vtcX->y + $this->_vtcZ->z * $rhs->_vtcX->z + $this->_vtx0->z * $rhs->_vtcX->w;

        $Yx = $this->_vtcX->x * $rhs->_vtcY->x + $this->_vtcY->x * $rhs->_vtcY->y + $this->_vtcZ->x * $rhs->_vtcY->z + $this->_vtx0->x * $rhs->_vtcY->w;
        $Yy = $this->_vtcX->y * $rhs->_vtcY->x + $this->_vtcY->y * $rhs->_vtcY->y + $this->_vtcZ->y * $rhs->_vtcY->z + $this->_vtx0->y * $rhs->_vtcY->w;
        $Yz = $this->_vtcX->z * $rhs->_vtcY->x + $this->_vtcY->z * $rhs->_vtcY->y + $this->_vtcZ->z * $rhs->_vtcY->z + $this->_vtx0->z * $rhs->_vtcY->w;

        $Zx = $this->_vtcX->x * $rhs->_vtcZ->x + $this->_vtcY->x * $rhs->_vtcZ->y + $this->_vtcZ->x * $rhs->_vtcZ->z + $this->_vtx0->x * $rhs->_vtcZ->w;
        $Zy = $this->_vtcX->y * $rhs->_vtcZ->x + $this->_vtcY->y * $rhs->_vtcZ->y + $this->_vtcZ->y * $rhs->_vtcZ->z + $this->_vtx0->y * $rhs->_vtcZ->w;
        $Zz = $this->_vtcX->z * $rhs->_vtcZ->x + $this->_vtcY->z * $rhs->_vtcZ->y + $this->_vtcZ->z * $rhs->_vtcZ->z + $this->_vtx0->z * $rhs->_vtcZ->w;

        $this->_vtcX->x = $Xx;
        $this->_vtcX->y = $Xy;
        $this->_vtcX->z = $Xz;

        $this->_vtcY->x = $Yx;
        $this->_vtcY->y = $Yy;
        $this->_vtcY->z = $Yz;

        $this->_vtcZ->x = $Zx;
        $this->_vtcZ->y = $Zy;
        $this->_vtcZ->z = $Zz;
        return $this;
    }

    function __get($name) {
        // var_dump($name." GET\n");
        if (in_array($name, ['_vtcX','_vtcY','_vtcZ','_vtx0'])){
            return $this->$name;
        }
    }

    private function _construct_message($preset) {
        if (self::$verbose)
            echo "Matrix $preset preset instance constructed\n";
    }

    function __toString() {
        $res = "M | vtcX | vtcY | vtcZ | vtxO\n";
        $res .= "-----------------------------\n";
        $res .= "x | ".sprintf("%.2f", $this->_vtcX->x)." | ".sprintf("%.2f", $this->_vtcY->x)." | ".sprintf("%.2f", $this->_vtcZ->x)." | ".sprintf("%.2f", $this->_vtx0->x)."\n";
        $res .= "y | ".sprintf("%.2f", $this->_vtcX->y)." | ".sprintf("%.2f", $this->_vtcY->y)." | ".sprintf("%.2f", $this->_vtcZ->y)." | ".sprintf("%.2f", $this->_vtx0->y)."\n";
        $res .= "z | ".sprintf("%.2f", $this->_vtcX->z)." | ".sprintf("%.2f", $this->_vtcY->z)." | ".sprintf("%.2f", $this->_vtcZ->z)." | ".sprintf("%.2f", $this->_vtx0->z)."\n";
        $res .= "w | ".sprintf("%.2f", $this->_vtcX->w)." | ".sprintf("%.2f", $this->_vtcY->w)." | ".sprintf("%.2f", $this->_vtcZ->w)." | ".sprintf("%.2f", $this->_vtx0->w)."\n";
        return $res;
    }
}

Vertex::$verbose = False;
Vector::$verbose = False;

Matrix::$verbose = True;

print( 'Let\'s start with an harmless identity matrix :' . PHP_EOL );
$I = new Matrix( array( 'preset' => Matrix::IDENTITY ) );
print( $I . PHP_EOL . PHP_EOL );

print( 'So far, so good. Let\'s create a translation matrix now.' . PHP_EOL );
$vtx = new Vertex( array( 'x' => 20.0, 'y' => 20.0, 'z' => 0.0 ) );
$vtc = new Vector( array( 'dest' => $vtx ) );
$T  = new Matrix( array( 'preset' => Matrix::TRANSLATION, 'vtc' => $vtc ) );
print( $T . PHP_EOL . PHP_EOL );

print( 'A scale matrix is no big deal.' . PHP_EOL );
$S  = new Matrix( array( 'preset' => Matrix::SCALE, 'scale' => 10.0 ) );
print( $S . PHP_EOL . PHP_EOL );

print( 'A Rotation along the OX axis :' . PHP_EOL );
$RX = new Matrix( array( 'preset' => Matrix::RX, 'angle' => M_PI_4 ) );
print( $RX . PHP_EOL . PHP_EOL );

print( 'Or along the OY axis :' . PHP_EOL );
$RY = new Matrix( array( 'preset' => Matrix::RY, 'angle' => M_PI_2 ) );
print( $RY . PHP_EOL . PHP_EOL );

print( 'Do a barrel roll !' . PHP_EOL );
$RZ = new Matrix( array( 'preset' => Matrix::RZ, 'angle' => 2 * M_PI ) );
print( $RZ . PHP_EOL . PHP_EOL );

print( 'The bad guy now, the projection matrix : 3D to 2D !' . PHP_EOL );
print( 'The values are arbitray. We\'ll decipher them in the next exercice.' . PHP_EOL );
$P = new Matrix( array( 'preset' => Matrix::PROJECTION,
						'fov' => 60,
						'ratio' => 640/480,
						'near' => 1.0,
						'far' => -50.0 ) );
print( $P . PHP_EOL . PHP_EOL );

print( 'Matrices are so awesome, that they can be combined !' . PHP_EOL );
print( 'This is a model matrix that scales, then rotates around OY axis,' . PHP_EOL );
print( 'then rotates around OX axis and finally translates.' . PHP_EOL );
print( 'Please note the reverse operations order. It\'s not an error.' . PHP_EOL );
$M = $T->mult( $RX )->mult( $RY )->mult( $S );
print( $M . PHP_EOL . PHP_EOL );

// print( 'What can you do with a matrix and a vertex ?' . PHP_EOL );
// $vtxA = new Vertex( array( 'x' => 1.0, 'y' => 1.0, 'z' => 0.0 ) );
// print( $vtxA . PHP_EOL );
// print( $M . PHP_EOL );
// print( 'Transform the damn vertex !' . PHP_EOL );
// $vtxB = $M->transformVertex( $vtxA );
// print( $vtxB . PHP_EOL . PHP_EOL );