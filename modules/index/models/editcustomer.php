<?php
/**
 * @filesource modules/index/models/editcustomer.php
 */

namespace Index\Editcustomer;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=editcustomer
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูลสมาชิกที่ $id
     * คืนค่าข้อมูล array ไม่พบคืนค่า false
     *
     * @param int $id
     *
     * @return array|bool
     */
    public static function get($id)
    {
        if (!empty($id)) {
            $cus = static::createQuery()
                ->select('CT.id AS contact_id' ,'CT.contact_name','CT.contact_tel','C.id AS customer_id','C.customer_name','C.customer_code','C.address','C.customer_type','T.id AS customer_type_id')
                ->from('customer C')
                ->join('contact CT', 'LEFT', array('CT.customer_id', 'C.id'))
                ->join('customer_type T', 'LEFT', array('T.id', 'C.customer_type'))
                ->where(array('C.id', $id))
                ->toArray()
                ->limit(1)
                ->execute();
            return $cus;
        }
        return false;
    }
    /**
     * อ่านหมวดหมู่สำหรับใส่ลงใน DataTable
     * ถ้าไม่มีคืนค่าข้อมูลเปล่าๆ 1 แถว
     *
     * @param string $type
     *
     * @return array
     */
    public static function toDataTable($cus)
    {
        // Query ข้อมูลหมวดหมู่จากตาราง contact
            $query =  static::createQuery()
            ->select('contact_name','contact_tel','id AS contact_id')
            ->from('contact')
            ->where(array('customer_id',$cus));
          
                $result = array();
                foreach ($query->execute() as $item) {
                    $result[$item->contact_id] = array(
                        'contact_name' => $item->contact_name,
                        'contact_tel' => $item->contact_tel,
                        'contact_id' => $item->contact_id,
                    );
                }
                //แสดงค่าว่าง
                if (empty($result)) {
                    $result[1] = array(
                        'contact_name' => '',
                        'contact_tel' =>'',
                        'contact_id' =>'',
                    );
                }
        return $result;
    }

    /**
     * บันทึกข้อมูล (editcustomer.php)
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {
        $ret = array();
        // session, token, สมาชิก และไม่ใช่สมาชิกตัวอย่าง
        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if (Login::notDemoMode($login)) {
                try {
                    $customer_id = $request->post('customer_id')->toInt();
                    $save_cust = array(
                        'customer_name' => $request->post('customer_name')->topic(),
                        'customer_code' => $request->post('customer_code')->topic(),
                        'address' => $request->post('address')->topic(),
                        'customer_type' => $request->post('customer_type')->toInt(),
                        'modify_date' => date('Y-m-d H:i:s'),
                        'modify_by' => $login['id'],
                        'status' => 1,
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
                            $save_con[$key]['customer_id'] =   $customer_id;
                            $save_con[$key]['status'] =   1;
                        }
                    }
                    // ชื่อตาราง
                    $table_customer = $this->getTableName('customer');
                    $table_contact = $this->getTableName('contact');
                    // database connection
                    $db = $this->db();
                     // ไม่ได้กรอก ชื่อลูกค้า เช็ครหัสลูกค้าซ้ำ
                  /*   if (isset($save_cust['customer_code'])) {
                        if (empty($save_cust['customer_code'])) {
                            $ret['ret_customer_code'] = 'Please fill in';
                        } else {
                            // ตรวจสอบข้อมูลซ้ำ
                            $search = $db->first($table_customer, array('customer_code', $save_cust['customer_code']));
                            if ($search) {
                                $ret['ret_customer_code'] = Language::replace('This :name already exist', array(':name' => $save_cust['customer_code']));
                            }
                        }
                    }*/
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
                                    $db->update($table_customer, $customer_id, $save_cust);
                                }
                            if (!empty($save_con)) {
                                // $db->update($table_contact, $contact_id, $save_con);
                                if($save_con[0]['contact_name'] != ''){
                                    // ลบข้อมูลเดิม
                                    $db->delete($table_contact, array('customer_id', $customer_id), 0);
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
                       /* // reload หน้าเว็บ
                        $ret['location'] = 'reload';*/
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
}
