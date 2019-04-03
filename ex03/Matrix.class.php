<?php

require_once('Vector.class.php');

class Matrix {
    static $verbose = false;
    const   CUSTOM      = '_custom',
            IDENTITY    = '_identity',
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

    static function doc() {
        return file_get_contents('Matrix.doc.txt');
    }

    function __construct($params = []) {
        $this->$params['preset']($params);
    }

    // @TODO
    private function _custom($params) {
        $this->_vtcX = $params['vtcX'];
        $this->_vtcY = $params['vtcY'];
        $this->_vtcZ = $params['vtcZ'];
        $this->_vtx0 = $params['vtx0'];
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
        $Xw = $this->_vtcX->w * $rhs->_vtcX->x + $this->_vtcY->w * $rhs->_vtcX->y + $this->_vtcZ->w * $rhs->_vtcX->z + $this->_vtx0->w * $rhs->_vtcX->w;

        $Yx = $this->_vtcX->x * $rhs->_vtcY->x + $this->_vtcY->x * $rhs->_vtcY->y + $this->_vtcZ->x * $rhs->_vtcY->z + $this->_vtx0->x * $rhs->_vtcY->w;
        $Yy = $this->_vtcX->y * $rhs->_vtcY->x + $this->_vtcY->y * $rhs->_vtcY->y + $this->_vtcZ->y * $rhs->_vtcY->z + $this->_vtx0->y * $rhs->_vtcY->w;
        $Yz = $this->_vtcX->z * $rhs->_vtcY->x + $this->_vtcY->z * $rhs->_vtcY->y + $this->_vtcZ->z * $rhs->_vtcY->z + $this->_vtx0->z * $rhs->_vtcY->w;
        $Yw = $this->_vtcX->w * $rhs->_vtcY->x + $this->_vtcY->w * $rhs->_vtcY->y + $this->_vtcZ->w * $rhs->_vtcY->z + $this->_vtx0->w * $rhs->_vtcY->w;

        $Zx = $this->_vtcX->x * $rhs->_vtcZ->x + $this->_vtcY->x * $rhs->_vtcZ->y + $this->_vtcZ->x * $rhs->_vtcZ->z + $this->_vtx0->x * $rhs->_vtcZ->w;
        $Zy = $this->_vtcX->y * $rhs->_vtcZ->x + $this->_vtcY->y * $rhs->_vtcZ->y + $this->_vtcZ->y * $rhs->_vtcZ->z + $this->_vtx0->y * $rhs->_vtcZ->w;
        $Zz = $this->_vtcX->z * $rhs->_vtcZ->x + $this->_vtcY->z * $rhs->_vtcZ->y + $this->_vtcZ->z * $rhs->_vtcZ->z + $this->_vtx0->z * $rhs->_vtcZ->w;
        $Zw = $this->_vtcX->w * $rhs->_vtcZ->x + $this->_vtcY->w * $rhs->_vtcZ->y + $this->_vtcZ->w * $rhs->_vtcZ->z + $this->_vtx0->w * $rhs->_vtcZ->w;

        $Wx = $this->_vtcX->x * $rhs->_vtx0->x + $this->_vtcY->x * $rhs->_vtx0->y + $this->_vtcZ->x * $rhs->_vtx0->z + $this->_vtx0->x * $rhs->_vtx0->w;
        $Wy = $this->_vtcX->y * $rhs->_vtx0->x + $this->_vtcY->y * $rhs->_vtx0->y + $this->_vtcZ->y * $rhs->_vtx0->z + $this->_vtx0->y * $rhs->_vtx0->w;
        $Wz = $this->_vtcX->z * $rhs->_vtx0->x + $this->_vtcY->z * $rhs->_vtx0->y + $this->_vtcZ->z * $rhs->_vtx0->z + $this->_vtx0->z * $rhs->_vtx0->w;
        $Ww = $this->_vtcX->w * $rhs->_vtx0->x + $this->_vtcY->w * $rhs->_vtx0->y + $this->_vtcZ->w * $rhs->_vtx0->z + $this->_vtx0->w * $rhs->_vtx0->w;

        return new Matrix([
            'preset' => self::CUSTOM,
            'vtcX' => new Vector(['dest' => new Vertex(['x' => $Xx, 'y' => $Xy, 'z' => $Xz, 'w' => $Xw + 1])]),
            'vtcY' => new Vector(['dest' => new Vertex(['x' => $Yx, 'y' => $Yy, 'z' => $Yz, 'w' => $Yw + 1])]),
            'vtcZ' => new Vector(['dest' => new Vertex(['x' => $Zx, 'y' => $Zy, 'z' => $Zz, 'w' => $Zw + 1])]),
            'vtx0' => new Vertex(['x' => $Wx, 'y' => $Wy, 'z' => $Wz, 'w' => $Ww])
        ]);
    }

    function transformVertex(Vertex $vtx) {
        $x = $this->_vtcX->x * $vtx->x + $this->_vtcY->x * $vtx->y + $this->_vtcZ->x * $vtx->z + $this->_vtx0->x * $vtx->w;
        $y = $this->_vtcX->y * $vtx->x + $this->_vtcY->y * $vtx->y + $this->_vtcZ->y * $vtx->z + $this->_vtx0->y * $vtx->w;
        $z = $this->_vtcX->z * $vtx->x + $this->_vtcY->z * $vtx->y + $this->_vtcZ->z * $vtx->z + $this->_vtx0->z * $vtx->w;
        $w = $this->_vtcX->w * $vtx->x + $this->_vtcY->w * $vtx->y + $this->_vtcZ->w * $vtx->z + $this->_vtx0->w * $vtx->w;

        return new Vertex(compact('x', 'y', 'z', 'w'));
    }
    
    private function _construct_message($preset) {
        if (self::$verbose)
            echo "Matrix $preset preset instance constructed\n";
    }

    function __get($name) {
        if (in_array($name, ['_vtcX','_vtcY','_vtcZ','_vtx0']))
            return $this->$name;
    }

    function __destruct() {
        if (self::$verbose)
            echo "Matrix instance destructed.\n";
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
