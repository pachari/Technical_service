<?php
/**
 * @filesource modules/inventory/models/category.php
 *
 
 */

namespace Inventory\Category;

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
        $this->categories = Language::get('INVENTORY_CATEGORIES', array());
    }
}
