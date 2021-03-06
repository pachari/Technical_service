<?php
/**
 * @filesource modules/inventory/controllers/init.php
 *
 
 */

namespace Inventory\Init;

/**
 * Init Module
 
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * รายการ permission ของโมดูล.
     *
     * @param array $permissions
     *
     * @return array
     */
    public static function updatePermissions($permissions)
    {
        $permissions['can_manage_inventory'] = '{LNG_types of objective}'; //{LNG_Can manage the}    ({LNG_Technical Service system})
        return $permissions;
    }
}
