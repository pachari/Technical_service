<?php
/**
 * @filesource Kotchasan/Grid.php
 *
 
 */

namespace Kotchasan;

/**
 * Grid System
 
 */
class Grid extends \Kotchasan\Template
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->cols = 1;
    }

    /**
     * คืนค่าจำนวนคอลัมน์ของกริด
     *
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * กำหนดจำนวนกอลัมน์ของกริด
     *
     * @param int $cols จำนวนคอลัมน์ มากกว่า 0
     *
     * @return \static
     */
    public function setCols($cols)
    {
        $this->cols = max(1, (int) $cols);
        $this->num = $this->cols;
        return $this;
    }
}
