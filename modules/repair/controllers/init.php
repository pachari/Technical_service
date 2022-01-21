<?php
/**
 * @filesource modules/repair/controllers/init.php
 *
 
 */

namespace Repair\Init;

/**
 * Init Module
 
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * รายการ permission ของโมดูล
     *
     * @param array $permissions
     *
     * @return array
     */
    public static function updatePermissions($permissions)
    {
        //เมนูจัดการงาน
        $permissions['can_manage_repair'] = '{LNG_Can manage repair}';
        //เมนูแจ้งงาน
        $permissions['can_repair'] = '{LNG_Technical Service jobs}';
       // $permissions['can_approve_manage_repair'] = '{LNG_Can approve manage repair}';
       //เมนูอนุมัติงาน
        $permissions['approve_repair'] = '{LNG_Can approve manage repair}';
        //เมนูจัดการลูกค้า
        $permissions['can_manage_customer'] = '{LNG_Customer list}';
        //เมนูรายงาน
        $permissions['report'] = '{LNG_report}';
        //ส่วน Booking Room
        $permissions['can_manage_room'] = '{LNG_Can manage room}';
        $permissions['can_approve_room'] = '{LNG_Can be approve} {LNG_Room}';
        $permissions['send_email'] = '{LNG_Emailing}';
        $permissions['send_email_cc'] = '{LNG_Emailing} {LNG_email_cc}';
        return $permissions;
    }
}
