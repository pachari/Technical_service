<?php
/**
 * @filesource modules/inventory/controllers/initmenu.php
 *
 
 */

namespace Inventory\Initmenu;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * Init Menu
 
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * ฟังก์ชั่นเริ่มต้นการทำงานของโมดูลที่ติดตั้ง
     * และจัดการเมนูของโมดูล
     *
     * @param Request                $request
     * @param \Index\Menu\Controller $menu
     * @param array                  $login
     */
    public static function execute(Request $request, $menu, $login)
    {
        // เมนูตั้งค่า
        $submenus = array();
        if (Login::checkPermission($login, 'can_config')) {
            $submenus[] = array(
                'text' => '{LNG_Module settings}',
                'url' => 'index.php?module=inventory-settings',
            );
        }
        // สามารถบริหารจัดการได้
        if (Login::checkPermission($login, 'can_config')) { //can_manage_inventory
            $submenus[] = array(
                'text' => '{LNG_List of}',
                'url' => 'index.php?module=inventory-setup',
            ); 
            foreach (Language::get('INVENTORY_CATEGORIES', array()) as $type => $text) {
                $submenus[] = array(
                    'text' => $text,
                    'url' => 'index.php?module=inventory-categories&amp;type='.$type,
                );
            }
        }
        if (!empty($submenus)) {
            $menu->add('settings', '{LNG_types of objective}', null, $submenus, 'inventory');
        }
    }
}
