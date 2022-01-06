<?php
/**
 * @filesource modules/index/models/registercustomer.php
 *
 */

namespace Index\RegisterCustomer;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=registercustomer
 *
 */
class Model extends \Kotchasan\Model
{
   /**
     * ดึงข้อมูล id ล่าสุด (registercustomer.php)
     *
     * @param Request $request
     */
     public static function get()
    { 
        return  static::createQuery() 
         ->select('id')
         ->from('customer')
         ->order('id DESC')
         ->first();
    }
      /**
     * บันทึกข้อมูล (registercustomer.php)
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {
        $ret = array();
        $index = self::get();
        // session, token, สมาชิก และไม่ใช่สมาชิกตัวอย่าง
        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if (Login::notDemoMode($login)) {
                try {
                    $save_cust = array(
                        'customer_name' => $request->post('customer_name')->topic(),
                        'customer_code' => $request->post('customer_code')->topic(),
                        'address' => $request->post('address')->topic(),
                        'customer_type' => $request->post('customer_type')->toInt(),
                        'create_date' => date('Y-m-d H:i:s'),
                        'create_by' => $login['id'],
                        'modify_date' => date('Y-m-d H:i:s'),
                        'modify_by' => $login['id'],
                        'status' => 1,
                        'country' => 1,
 
                    );
                    $save_con = array();
                    $contact_exists = array();
                    foreach ($request->post('contact_name')->topic() as $key => $value) {
                        if (isset($contact_exists[$value])) {
                            $ret['ret_contact_name_'.$key] = Language::replace('This :name already exist', array(':name' => 'contact_tel'));
                        } else {
                            $contact_exists[$value] = $value;
                            $save_con[$key]['contact_name'] = $value;
                        }
                    }
                    foreach ($request->post('contact_tel')->topic() as $key => $value) {
                        if (isset($save_con[$key]) && $value != '') {
                            $save_con[$key]['contact_tel'] = $value;
                            $save_con[$key]['create_date'] = date('Y-m-d H:i:s');
                            $save_con[$key]['modify_date'] = date('Y-m-d H:i:s');
                            $save_con[$key]['modify_by'] =  $login['id'];
                            $save_con[$key]['customer_id'] =  $index->id+1;
                        }
                    }
                    // ชื่อตาราง
                    $table_customer = $this->getTableName('customer');
                    $table_contact = $this->getTableName('contact');
                    // database connection
                    $db = $this->db();
                    // ไม่ได้กรอก ชื่อลูกค้า เช็ครหัสลูกค้าซ้ำ
                    if (isset($save_cust['customer_code'])) {
                        if (empty($save_cust['customer_code'])) {
                            $ret['ret_customer_code'] = 'Please fill in';
                        } else {
                            // ตรวจสอบข้อมูลซ้ำ
                            $search = $db->first($table_customer, array('customer_code', $save_cust['customer_code']));
                            if ($search) {
                                $ret['ret_customer_code'] = Language::replace('This :name already exist', array(':name' => $save_cust['customer_code']));
                            }
                        }
                    }
                    // ไม่ได้กรอก ชื่อลูกค้า
                    if ($save_cust['customer_name'] == '') {
                        $ret['ret_customer_name'] = 'Please fill in';
                    }
                    // ไม่ได้กรอก ที่อยู่
                    if ($save_cust['address'] == '') {
                            $ret['ret_address'] = 'Please fill in';
                    }      
                    // บันทึก
                    if (empty($ret)) {
                            if (!empty($save_cust)) {
                                      $db->insert($table_customer, $save_cust);
                                }
                            if (!empty($save_con)) {
                                if($save_con[0]['contact_name'] != ''){
                                    // เพิ่มข้อมูลใหม่
                                    foreach ($save_con as $item) {
                                        if (isset($item['contact_name'])) {
                                            $db->insert($table_contact, $item);
                                        }
                                    }
                                }
                            }  
                        // คืนค่า
                        $ret['alert'] = Language::get('Saved successfully');
                        // เคลียร์
                        $request->removeToken();
                         // ไปหน้าเดิม แสดงรายการ
                        $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'customer', 'id' => null));
                    }
                } catch (\Kotchasan\InputItemException $e) {
                    $ret['alert'] = $e->getMessage();
                }
            }
        }
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่าเป็น JSON
        echo json_encode($ret);
    }

    /**
     * ลงทะเบียนสมาชิกใหม่
     * คืนค่าแอเรย์ของข้อมูลสมาชิกใหม่
     *
     * @param Model $model
     * @param array $save       ข้อมูลสมาชิก
     * @param array $permission
     *
     * @return array
     */
    public static function execute($model, $save, $permission = null)
    {
        if (!isset($save['username'])) {
            $save['username'] = '';
        }
        if (!isset($save['password'])) {
            $save['password'] = '';
        } else {
            $save['salt'] = \Kotchasan\Password::uniqid();
            $save['password'] = sha1(self::$cfg->password_key.$save['password'].$save['salt']);
        }
        $save['create_date'] = date('Y-m-d H:i:s');
        $save['session_id'] = session_id();
        if ($permission === null) {
            // permission ถ้าเป็น null สามารถทำได้ทั้งหมด
            $permission = array_keys(\Gcms\Controller::getPermissions());
        } else {
            // สมาชิกทั่วไปใช้ค่าเริ่มต้นของโมดูล
            $permission = \Gcms\Controller::initModule($permission, 'newRegister', $save);
        }
        $save['permission'] = empty($permission) ? '' : ','.implode(',', $permission).',';
        // บันทึกลงฐานข้อมูล
        $save['id'] = $model->db()->insert($model->getTableName('user'), $save);
        // คืนค่าแอเรย์ของข้อมูลสมาชิกใหม่
        $save['permission'] = array();
        foreach ($permission as $key => $value) {
            $save['permission'][] = $value;
        }
        return $save;
    }
}
