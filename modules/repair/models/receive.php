<?php
/**
 * @filesource modules/repair/models/receive.php
 *
 * 
 *
 * @see http://www.kotchasan.com/
 */

namespace Repair\Receive;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;
use Kotchasan\File;
use Kotchasan\Files;
use Kotchasan\Database\Sql;
/**
 * module=repair-receive
 
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูลรายการที่เลือก
     * ถ้า $id = 0 หมายถึงรายการใหม่
     * คืนค่าข้อมูล object ไม่พบคืนค่า null
     *
     * @param int $id ID
     * @param string $product_no
     *
     * @return object|null
     */
    public static function get($id, $customer_id = '')
    {     
         
        if (empty($id)) {
                        // ใหม่
                            return (object) array(
                                'id' => 0,
                                'product_no' => '',
                                'topic' => '',
                                'job_description' => '',
                                'comment' => '',
                                'status_id' => 0,
                                'category_id' => '',
                                'type_id' => '',
                                'model_id' => '',
                                'urgency' => '',
                                'customer_id' => '',
                                'customer_name' => '',
                                'address' => '',
                                'contact_name' => '',
                                'contact_tel' => '',
                                'contact_id' => '',
                                'approve_name' => '',
                                'approve_id' => '',
                                'type_job_number' => '',
                                
                            );
        } else {

            $q0_name = static::createQuery()
            ->select('U1.name as send_approve2')
            ->from('user U1')
            ->where(array('U1.id', 'R.send_approve'));
            $q0_group = static::createQuery()    
                ->select('U3.status as s_group')
                ->from('user U3')
                ->where(array('U3.id', 'R.create_by'));
            $q1 = static::createQuery()
                ->select('repair_id', Sql::MAX('id', 'max_id'))
                ->from('repair_status')
                ->groupBy('repair_id');
            return  static::createQuery() 
                ->from('repair R')
                ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
                ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
                ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
                ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
                ->join('user U', 'LEFT', array('U.id', 'R.create_by')) //customer_id
                ->join('customer C', 'LEFT', array('C.id', 'R.customer_id'))
                ->join('contact CT', 'LEFT', array('CT.id', 'R.contact_id'))
               /* ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
                ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
                ->join('customer C', 'LEFT', array('C.id', 'R.customer_id'))
               // ->join('contact T', 'LEFT', array('T.address_id', 'c.id'))*/
                ->where(array('R.id', $id))
                ->order('id') 
               ->first('R.*'
                             , 'V.topic'
                            ,'V.category_id'
                            ,'V.model_id'
                            ,'V.type_id'
                            ,'C.id as customer_id'
                            ,'R.type_job_number'
                            ,'R.type_work'
                            ,SQL::DATE_FORMAT('R.request_date', '%Y-%m-%d','DATE_REQ') 
                            ,SQL::DATE_FORMAT('R.request_date', '%H:%i','TIME_REQ') 
                            , 'U.name', 'U.phone',  'S.create_date as date_approve','S.status', 'S.comment', 'S.operator_id', 'S.id status_id',array( $q0_name,'send_approve2'),array( $q0_group,'s_group'),SQL::SUM('S.cost','COST'),'C.address','C.customer_name','CT.contact_name','CT.contact_tel','CT.position'
                        )
                        ;
        }
    }
 /**
     * Query check contact
     *
     * @return array
     */
    public static function Checkcontact($name,$tel,$cus_id)
    {    

        $where = array();
        if(!empty($name)){
            $where[] = array('contact_name','LIKE','%'.$name.'%');
        }
        if(!empty($tel)){
            $where[] = array('contact_tel','LIKE','%'.$tel.'%');
        }
        if(!empty($cus_id)){
            $where[] = array('customer_id',$cus_id);
        }
        
        return   \Kotchasan\Model::createQuery()  
        ->select(SQL::IFNULL('id',-1,'C_id'),SQL::COUNT('id','C_count') )
        ->from('contact')
        ->where( $where)
        ->execute();  
    }
         /**
     * Query รายชื่อทรัพย์สิน
     *
     * @return array
     */
    public static function allProduct($index)
    {    
        
        $where = array();
        if(!empty($index) && $index != 0){
            $where = array('I.inventory_id',$index);
          //  $where[] = array('V.inuse',1);
        }
        

        return   \Kotchasan\Model::createQuery() 
        ->select('I.inventory_id','I.product_no')
        ->from('inventory_items I')
        ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
        ->where($where)
        ->andWhere(array('V.inuse',1))
        ->order('I.inventory_id ASC')
        ->toArray()
        ->execute();  
    }

    /**
     * อ่านรายชื่อทรัพย์สิน
     *
     * @return \static
     */
    public static function createProduct()
    {
        $index = 0;
        $obj = new static();
        $obj->product_no = array();
        $obj->type_work_id = array();
        foreach (self::allProduct($index) as $item) {
            $obj->product_no[$item['inventory_id']] = $item['product_no'];

        }
        return $obj;
    }

    public function getProduct($id)
    {
        return isset($this->product_no[$id]) ? $this->product_no[$id] : '';
    }

    public  static function getid(){
        return   \Kotchasan\Model::createQuery() 
        ->select('id')
        ->from('repair')
        ->order('id DESC')
        ->limit(1)
        ->execute();  
    }
    /**
     * บันทึกค่าจากฟอร์ม (receive.php)
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {       
        $ret = array();
        // session, token, member
        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            try {
                // รับค่าจากการ POST
                $repair = array(
                    'request_date'          =>     $request->post('date_request')->topic().' '.$request->post('time_request')->topic(),
                    'customer_id'           =>     $request->post('customer_id')->topic(),
                    'product_no'          =>   $request->post('product_no')->topic(),
                    'type_work'            =>    $request->post('product_no')->toInt(),
                    'job_description'    =>     $request->post('job_description')->textarea(),
                    'send_approve'        =>      $request->post('approve_id')->toInt(),  
                    'type_job_number'  =>   $request->post('type_job_number')->toInt(),  
                ); 
                $contact = array(
                    'customer_id'           =>     $request->post('customer_id')->topic(),
                    'contact_name'      =>     $request->post('contact_name')->topic(),
                    'contact_tel'           =>     $request->post('contact_tel')->topic(),
                    'create_date'          => date('Y-m-d H:i:s'),
                    'modify_date'          => date('Y-m-d H:i:s'),
                    'modify_by'             => $login['id'],
                    'status'                    => 1,
                );

              //หาประเภทรายการ
              $type_work_name = \repair\Receive\Model::createProduct();  
                foreach($type_work_name->product_no as $k => $value){
                    if( $k == $repair['product_no']){
                        $type_work = $value;
                    }
                }
                $repair['product_no'] =$type_work;            
                // Database
                $db = $this->db();   
                // ตาราง
                $repair_table = $this->getTableName('repair'); 
                $repair_status_table = $this->getTableName('repair_status'); 
                $contact_table = $this->getTableName('contact');   
                $con_chx = self::Checkcontact( $contact['contact_name'],$contact['contact_tel'],$repair['customer_id']);

                    //check contect
                    if($con_chx[0]->C_id == -1 && $con_chx[0]->C_count == 0){
                            $model = new \Kotchasan\Model();
                            $query_index = $model->db()->createQuery()->first(Sql::NEXT('id', $contact_table, '','next_id')); 
                            $repair ['contact_id'] =$query_index->next_id;
                                // บันทึกผู้ติดต่อ
                                $db->insert($contact_table, $contact);       
                    }else{
                            $repair ['contact_id']  =     $request->post('contact_id')->topic();
                    }
                    // ไม่พบรายการที่เลือก
                    if ($request->post('date_request')->topic() == '' ) { 
                        $ret['ret_date_request'] = Language::get('Please select').' '.Language::get('request date'); 
                        }elseif ($repair['customer_id'] == '' ) {
                            $ret['ret_customer_id'] = Language::get('Please select').' '.Language::get('Customer Name'); 
                        }elseif($repair['contact_id'] == ''){
                            $ret['ret_contact_name'] = Language::get('Please select').' '.Language::get('Contact_name'); 
                        }elseif($repair['type_job_number'] == ''){
                            $ret['alert']  = Language::get('Please select').' '.Language::get('type_repair'); 
                        }elseif($repair['product_no'] == ''){
                            $ret['alert']  = Language::get('Please select').' '.Language::get('types of objective'); 
                        }elseif($repair['send_approve'] == '' || $repair['send_approve'] == '0'){
                            $ret['ret_approve_name'] = Language::get('Please select').' '.Language::get('Approve');   
                        }else {   
                       
                    // สามารถจัดการรายการซ่อมได้
                    $can_manage_repair = Login::checkPermission($login, 'can_manage_technical'); //can_manage_repair
                    // ตรวจสอบรายการที่เลือก
                    $index = self::get( $request->post('id')->toInt(), $repair['customer_id'] );

                            if (!$index || $index->id > 0 && ($login['id'] != $index->customer_id && !$can_manage_repair)) {
                                // ไม่พบรายการที่แก้ไข
                                $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
                            } else {    
                                        if ($index->id == 0) {
                                                foreach(self::$cfg->type_job_number as $k => $value){
                                                    if($repair['type_job_number'] ==$k){
                                                        $type_job_run = $value;
                                                    }
                                                }
                                                $repair['create_by'] = $login['id'];
                                                $repair['create_date'] = date('Y-m-d H:i:s');
                                                $name_pro = \repair\Receive\Model::allProduct($repair['type_work'] );
                                                $repair ['product_no'] = $name_pro[0]['product_no'];
                                                
                                               $Getlastid =  self::getid();
                                               $next_id = $Getlastid[0]->id + 1;

                                             
                                                ///<------------------------------------------------- อัปโหลดไฟล์ -------------------------------------->
                                                $dir = ROOT_PATH.DATA_FOLDER.'file_attachment_user/';  
                                                $i=0;

                                                foreach ($request->getUploadedFiles() as $item => $file) {
                                                

                                                        if(!empty($file->hasUploadFile())){

                                                                if ($file->hasUploadFile()) {

                                                                            if (!File::makeDirectory($dir)) {
                                                                                // ไดเรคทอรี่ไม่สามารถสร้างได้
                                                                                $check_alert = 1;
                                                                                $ret['alert'] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'file_attachment_user/');
                                                                                $ret['ret_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'file_attachment_user/');
                                                                                // ตรวจสอบนามสกุลของไฟล์
                                                                            }else if (!$file->validFileExt(array('jpg', 'jpeg', 'gif', 'png'),array('pdf'))) {
                                                                                // error ชนิดของไฟล์ไม่ถูกต้อง
                                                                                $check_alert = 2;
                                                                                $ret['alert'] = Language::get('The type of file is invalid');
                                                                                $ret['ret_'.$item] = Language::get('The type of file is invalid');
                                                                            
                                                                            }else {
                                                                                    try {
                                                                                        $i= $i+1;
                                                                                        $check_alert = 0;
                                                                                            // $file->resizeImage(array('jpg', 'jpeg', 'png'), $dir, 'U_'.str_replace('/','_',$repair['job_id']).'.jpg', self::$cfg->inventory_w);//$save['id']   
                                                                                            $file->moveTo($dir.$type_job_run.'_'. $next_id.'_'.$i.'.jpg'); //  $file->moveTo($dir.$file->getClientFilename().'.'.$file->getClientFileExt());
                                                                                            $repair ['attachment_no'] = $i;
                                                                                                                                                                            
                                                                                    } catch (\Exception $exc) {
                                                                                        // ไม่สามารถอัปโหลดได้
                                                                                        $ret['ret_'.$item] = Language::get($exc->getMessage());
                                                                                    }
                                                                            }
                                                                } elseif ($file->hasError()) {
                                                                    // ข้อผิดพลาดการอัปโหลด
                                                                    $ret['ret_'.$item] = Language::get($file->getErrorMessage());
                                                                }

                                                        }else{   
                                                                    $check_alert = 0;
                                                                    $repair ['attachment_no'] = 0;
                                                                }
                                                }  
                                                  
                                                if($check_alert == 0) {
                                                  
                                                        // job_id
                                                        $repair['job_id'] = \Index\Number\Model::get(0,$type_job_run, $repair_table, 'job_id');  
                                                        // บันทึกรายการแจ้งซ่อม
                                                        $log = array(
                                                        'repair_id' => $db->insert($repair_table, $repair),
                                                        'member_id' => $login['id'],
                                                        'comment' => $request->post('comment')->topic(),
                                                        'status' => isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 1,
                                                        'create_date' => $repair['create_date'],
                                                        'operator_id' => 0,
                                                    ); 
                                                      
                                                    // บันทึกประวัติการทำรายการ แจ้งซ่อม
                                                    $db->insert($repair_status_table, $log);
                                                    // ใหม่ ส่งอีเมลไปยังผู้ที่เกี่ยวข้อง
                                                    $ret['alert'] = \Repair\Email\Model::send($log['repair_id']);
                                                }
                                                        ////<----------------------------------------- ปิด อัปโหลดไฟล์ -------------------------------------->    
                                            
                                        } else {
                                                // แก้ไขรายการแจ้งซ่อม
                                                $db->update($repair_table, $index->id, $repair);
                                                // คืนค่า
                                                $ret['alert'] = Language::get('Saved successfully');  
                                        }

                                        if ($can_manage_repair && $index->id > 0 ) {
                                            // สามารถจัดการรายการซ่อมได้
                                            $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'repair-setup', 'id' => null));
                                        } else if( $check_alert == 0) {
                                                // ใหม่
                                                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'repair-history', 'id' => null));
                                        }
                                    
                            }
                                            
                    }
            } catch (\Kotchasan\InputItemException $e) {
                $ret['alert'] = $e->getMessage();
            }
        }
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่าเป็น JSON
        echo json_encode($ret);
    }
}

