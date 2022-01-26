<?php
/**
 * @filesource modules/index/views/customer.php
 *
 */

namespace Index\Customer;

use Kotchasan\DataTable;
use Kotchasan\Date;
use Kotchasan\Http\Request;

/**
 * module=customer
 *
 */
class View extends \Gcms\View
{
    /**
     * ตารางรายชื่อสมาชิก
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        
        // ค่าที่ส่งมา
        $params = array(
            'customer_type' => $request->request('customer_type', -1)->toInt(),
        );
        // สถานะลูกค้า
        $cus_type = array(-1 => '{LNG_all items}');
        foreach (\index\Customer\Model::getCustomerType() as $key => $value) {         
            $cus_type[$key+1] =  $value->type_name;         
        } 
        // URL สำหรับส่งให้ตาราง
        $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => \Index\Customer\Model::toDataTable($params),
            /* รายการต่อหน้า */
            'perPage' => $request->cookie('customer_perPage', 30)->toInt(),
            /* เรียงลำดับ */
            'sort' => $request->cookie('customer_sort', 'id desc')->toString(),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('contact_name','contact_tel'), 
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('customer_code','customer_name', 'address'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/index/model/customer/action',
            'actionCallback' => 'dataTableActionCallback',
            'actions' => array(
                array(
                    'id' => 'action',
                    'class' => 'ok',
                    'text' => '{LNG_With selected}',
                    'options' => array(
                       /* 'sendpassword' => '{LNG_Get new password}',
                        'active_1' => '{LNG_Current staff}',
                        'active_0' => '{LNG_Past employees}',*/
                        'delete' => '{LNG_Delete}',
                    ),
                ),
            ),
            /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
            'filters' => array(
                array(
                    'name' => 'customer_type',
                    'text' => '{LNG_Type}{LNG_Customer}',
                    'options' => $cus_type,
                    'value' =>   $params['customer_type'],
                ),
            ),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'id' => array(
                    'text' => '{LNG_number}',
                ),
                'customer_type' => array(
                    'text' => '{LNG_Type}',
                    'class' => 'center',
                ),
                'customer_code' => array(
                    'text' => '{LNG_Cus_code}',
                    'class' => 'center',
                ),
                'customer_name' => array(
                    'text' => '{LNG_Customer Name}',
                    'sort' => 'customer_name',
                ),
               'address' => array(
                    'text' =>  '{LNG_Address}',
                    'sort' => 'address',
                ),
               'create_date' => array(
                    'text' => '{LNG_Created}',
                    'class' => 'center',
                ), 
                'modify_date' => array(
                    'text' => '{LNG_modify}',
                    'class' => 'center',
                ), 
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
             'customer_type' => array(
                    'class' => 'center',
                ),
                'create_date' => array(
                    'class' => 'center',
                ),
                'modify_date' => array(
                    'class' => 'center',
                ),
            ),
            /* ปุ่มแสดงในแต่ละแถว */
            'buttons' => array(
                array(
                    'class' => 'icon-edit button green',
                    'href' => $uri->createBackUri(array('module' => 'editcustomer', 'id' => ':id')),
                    'text' => '{LNG_Edit}',
                ),
            ),
            /* ปุ่มเพิม */
            'addNew' => array(
                'class' => 'float_button icon-register',
                'href' => $uri->createBackUri(array('module' => 'registercustomer', 'id' => 0)), //
                'title' => '{LNG_Register}',
            ),
        ));
        // save cookie
        setcookie('customer_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('customer_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);
        // คืนค่า HTML

        
        return $table->render();
    }

    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว
     *
     * @param array  $item ข้อมูลแถว
     * @param int    $o    ID ของข้อมูล
     * @param object $prop กำหนด properties ของ TR
     *
     * @return array คืนค่า $item กลับไป
     */
    public function onRow($item, $o, $prop)
    {
        $item['create_date'] = Date::format($item['create_date'], 'd M Y H:i');
        $item['modify_date'] = Date::format($item['modify_date'], 'd M Y  H:i');
        /*if ($item['active'] == 1) {
            $item['active'] = '<span class="icon-valid access" title="{LNG_Current staff}"></span>';
            $item['lastvisited'] = empty($item['lastvisited']) ? '-' : Date::format($item['lastvisited'], 'd M Y H:i').' ('.number_format($item['visited']).')';
        } else {
            $item['active'] = '<span class="icon-valid disabled" title="{LNG_Past employees}"></span>';
            $item['lastvisited'] = '-';
        }
        if ($item['social'] == 1) {
            $item['social'] = '<span class="icon-facebook notext"></span>';
        } elseif ($item['social'] == 2) {
            $item['social'] = '<span class="icon-google notext"></span>';
        } else {
            $item['social'] = '';
        }
        $item['status'] = isset(self::$cfg->member_status[$item['status']]) ? '<span class=status'.$item['status'].'>{LNG_'.self::$cfg->member_status[$item['status']].'}</span>' : '';
        $item['phone'] = self::showPhone($item['phone']);*/
        return $item;
    }
}
