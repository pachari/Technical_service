<?php
/**
 * @filesource modules/index/models/category.php
 *
 
 */

namespace Index\Category;

use Kotchasan\Language;

/**
 * คลาสสำหรับอ่านข้อมูลหมวดหมู่
 
 */
class Model extends \Gcms\Category
{
    /**
     * init Class
     */
    public function __construct()
    {
        // ชื่อหมวดหมู่
        $this->categories = Language::get('CATEGORIES', array());
    }
}
