<?php
/**
 * @filesource modules/repair/controllers/initmenu.php
 *
 
 */

namespace Repair\Initmenu;

use Gcms\Login;
use Kotchasan\Http\Request;

/**
 * Init Menu
 
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * ฟังก์ชั่นเริ่มต้นการทำงานของโมดูลที่ติดตั้ง
     * และจัดการเมนูของโมดูล.
     *
     * @param Request                $request
     * @param \Index\Menu\Controller $menu
     * @param array                  $login
     */
    public static function execute(Request $request, $menu, $login)
    {
        $submenus = array(
            array(
                'text' => '{LNG_Get a Technical Service}',
                'url' => 'index.php?module=repair-receive',
            ),
            array(
                'text' => '{LNG_History}',
                'url' => 'index.php?module=repair-history',
            ),
        );
        // อนุมัติรายการซ่อม 
        if (Login::checkPermission($login, array('approve_manage_repair', 'approve_repair'))) {
            $submenus[] = array(
                'text' => '{LNG_List of} {LNG_approve_wait}',
                'url' => 'index.php?module=repair-approve',
            );
        }
        // สามารถจัดการรายการซ่อมได้, ช่างซ่อม
        if (Login::checkPermission($login, array('can_manage_repair', 'can_repair'))) {
            $submenus[] = array(
                'text' => '{LNG_List of} {LNG_Technical Service}', //LNG_Technical Service man
                'url' => 'index.php?module=repair-setup',
            );
        } 
        // เมนูแจ้งซ่อม
            $menu->add('repair', '{LNG_Technical Service jobs}', null, $submenus);
            $menu->addTopLvlMenu('repair', '{LNG_Technical Service jobs}', null, $submenus, 'member');
        // สามารถตั้งค่าระบบได้
        if (Login::checkPermission($login, 'can_config')) {
            $menu->add('settings', '{LNG_Technical Service}', null, array(
                array(
                    'text' => '{LNG_Module settings}',
                    'url' => 'index.php?module=repair-settings',
                ),
                array(
                    'text' => '{LNG_Technical Service status}',
                    'url' => 'index.php?module=repair-repairstatus',
                ),
            ), 'repair');
        }
        // ลูกค้า 
        if (Login::checkPermission($login,'can_manage_customer')) {
            $menu->addTopLvlMenu('Customer', '{LNG_Customer list}',  'index.php?module=customer',null   );
        }
        // รายงาน 
        if (Login::checkPermission($login, array('report'))) {
            $submenus2 = array(
                    array(
                        'text' => '{LNG_Summary}', 
                        'url' => 'index.php?module=report',
                        ),
                     array(
                        'text' => '{LNG_Graph-report}',
                        'url' => 'index.php?module=reportg',
                         ),
            );
            // เมนูรายงาน
            $menu->add('reportG', '{LNG_report}', null, $submenus2);
            $menu->addTopLvlMenu('reportG', '{LNG_report}', null, $submenus2, 'report');
        }
    }
}
