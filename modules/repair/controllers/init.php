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
        //**********Technical************** */
         //เมนูจัดการลูกค้า
         $permissions['can_manage_customer'] = '{LNG_Customer list} '; //  ({LNG_Technical Service system})
        //เมนูจัดการงาน
        $permissions['can_manage_technical'] = '{LNG_Can manage the} {LNG_Technical Service jobs}';  //$permissions['can_manage_repair']
       // $permissions['can_approve_manage_repair'] = '{LNG_Can approve manage repair}';
        //การส่งอีเมล
        $permissions['send_email'] = '{LNG_Emailing} ';
        $permissions['send_email_cc'] = '{LNG_Emailing} {LNG_email_cc}';

        //**********Sum********************** */
         //เมนูแจ้งงาน
         $permissions['can_repair'] = '{LNG_New Job}';
         //เมนูรายงาน
         $permissions['report'] = '{LNG_report}';
         //เมนูอนุมัติงาน
        $permissions['approve_repair'] = '{LNG_Can approve manage repair} ';

        //**********Repair************** */
        //เมนูจัดการงาน
        $permissions['can_manage_repair'] = '{LNG_Can manage repair} ({LNG_Repair System})';  

         //**********Room************** */
        //ส่วน Booking Room
        $permissions['can_manage_room'] = '{LNG_Can manage room} ({LNG_Room})';
        $permissions['can_approve_room'] = '{LNG_Can be approve} ({LNG_Room})';

        return $permissions;
    }
}
