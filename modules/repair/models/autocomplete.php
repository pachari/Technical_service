<?php
/**
 * @filesource modules/repair/models/autocomplete.php
 *
 
 */

namespace Repair\Autocomplete;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Database\Sql;

/**
 * ค้นหา สำหรับ autocomplete
 
 */
class Model extends \Kotchasan\Model
{
    /**
     * ค้นหา Inventory สำหรับ autocomplete
     * เฉพาะรายการที่ตัวเองรับผิดชอบ และ ที่ไม่มีผู้รับผิดชอบ
     * คืนค่าเป็น JSON
     *
     * @param Request $request
     */
    public function find(Request $request)
    {
      
        if ($request->initSession() && $request->isReferer() && Login::isMember()) {
            try {
               
                        // ข้อมูลที่ส่งมา
                        if ($request->post('customer_name')->exists()) {
                            $search = $request->post('customer_name')->topic();
                            $order = 'customer_name';
                        }
                        //ส่วนที่จะเอาคำมาค้นหา
                        $where = array();
                        if (isset($search)) {
                            $where[] = array($order, 'LIKE', "%$search%");
                        }

                            $query = $this->db()->createQuery()
                                ->select('id as customer_id','customer_name','address')
                                ->from('customer')
                                ->where($where) 
                                ->order('id')
                                ->limit($request->post('count', 20)->toInt())
                                ->toArray();                  

                                if (isset($order)) {
                                    $query->order($order);
                                }
                                $result = $query->execute();
                                if (!empty($result)) {
                                    // คืนค่า JSON 
                                    echo json_encode($result);
                                }
            } catch (\Kotchasan\InputItemException $e) {
            }         
        }           
    }
    public function find2(Request $request)
    {
        if ($request->initSession() && $request->isReferer() && Login::isMember()) {
            try {
                    
                        $where = array();
                        
                        // ข้อมูลที่ส่งมา
                        if ($request->post('contact_name')->exists()) {
                            $search = $request->post('contact_name')->topic();
                            $order = 'contact_name';
                            $count =  self::count_contact($request->post('customer_id')->topic()); 
                            if($count[0]->count <= 0){
                                $where[] = array('id',1);
                            }else{ 
                                $where[] = array('customer_id',  $request->post('customer_id')->topic()) ;
                            }
                        }
                    //ส่วนที่จะเอาคำมาค้นหา
                        if (isset($search)) {
                            $where[] = array($order, 'LIKE', "%$search%");
                        }
                    $query = $this->db()->createQuery()
                        ->select('T.contact_name','T.contact_tel','T.id as contact_id')
                        ->from('contact T')
                        ->where($where)
                        ->order('id')
                        ->limit($request->post('count', 20)->toInt())
                        ->toArray();   

                        if (isset($order)) {
                            $query->order($order);
                        }
                        $result = $query->execute();
                        if (!empty($result)) {
                            // คืนค่า JSON 
                            echo json_encode($result);
                        }
            } catch (\Kotchasan\InputItemException $e) {
            }         
        }           
    }
    public static function count_contact($id){
        return \Kotchasan\Model::createQuery()
            ->select(SQL::COUNT('id','count')) 
            ->from('contact')
            ->where(array('customer_id',$id))
            ->order('id')
            ->execute();
    }
    public function find3(Request $request)
    {
         $request->initSession()  ;
         $login = Login::isMember();
        if ($request->initSession() && $request->isReferer() && Login::isMember()) {
            try {                
                        $where = array();
                        // ข้อมูลที่ส่งมา
                        $search =  $login['head'];
                        $order = 'id';
                        //ส่วนที่จะเอาคำมาค้นหา
                        if (isset($search)) {
                        //  $where[] = array($order, 'LIKE', "%$search%");
                          $where[] = array($order, $search);
                        }

                    $query = $this->db()->createQuery()
                        ->select('name as approve_name','id as approve_id','id_card as employee')
                        ->from('user')
                        ->where($where)
                        ->toArray();   

                        if (isset($order)) {
                            $query->order($order);
                        }
                        $result = $query->execute();
                        if (!empty($result)) {
                            // คืนค่า JSON 
                            echo json_encode($result);
                        }
            } catch (\Kotchasan\InputItemException $e) {
            }         
        }           
    }
}
