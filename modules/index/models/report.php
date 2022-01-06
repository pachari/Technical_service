<?php
/**
 * @filesource modules/index/models/report.php
 *
 
 */

namespace Index\Report;

use Gcms\Login;
use Kotchasan\Database\Sql;
use Kotchasan\Http\Request;
use Kotchasan\Language;
use Kotchasan\Date;

/**
 * module=report
 
 */
class Model extends \Kotchasan\Model
{
 

    /**
     * Query รายชื่อผู้แจ้งซ่อม
     *
     * @return array
     */
    public static function all()
    {
        return  \Kotchasan\Model::createQuery() //return
            ->select('id', 'name')
            ->from('user')
            ->where(array(
                array('active', 1),
                /*array('permission', 'LIKE', '%,can_repair,%'),*/
            ))
            ->order('id')
            ->toArray()
            ->execute();
           
    }

    /**
     * อ่านรายชื่อผู้แจ้งซ่อม
     *
     * @return \static
     */
    public static function create()
    {
        $obj = new static();
        $obj->userrepair = array();
        foreach (self::all() as $item) {
            $obj->userrepair[$item['id']] = $item['name'];
        }
        return $obj;
    }
    /**
     * อ่านรายชื่อผู้แจ้งซ่อมสำหรับใส่ลงใน select.
     *
     * @return array
     */
    public function toSelect()
    {
        return $this->userrepair;
    }
    
    public function getuser($id)
    {
        return isset($this->userrepair[$id]) ? $this->userrepair[$id] : '';
    }

     /**
     * Query รายชื่อทรัพย์สิน
     *
     * @return array
     */
    public static function allProduct()
    {      
          return  \Kotchasan\Model::createQuery() 
            ->select('product_no') 
            ->from('inventory_items')
           ->order('inventory_id ASC')
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
        $obj = new static();
        $obj->product_no = array();
        foreach (self::allProduct() as $item) {
            $obj->product_no[$item['product_no']] = $item['product_no'];
        }
        return $obj;
    }
    /**
     * อ่านรายชื่อทรัพย์สินสำหรับใส่ลงใน select.
     *
     * @return array
     */
    public function toSelectProduct()
    {
        return $this->product_no;
    }
    
    public function getProduct($id)
    {
        return isset($this->product_no[$id]) ? $this->product_no[$id] : '';
    }
    /**
     * Query รายชื่อ Category หมวดหมู่
     *
     * @return array
     */
    public static function allCategory($type)
    {
         // Query ข้อมูลหมวดหมู่จากตาราง category

         if($type == 'topic_id'){

            return  \Kotchasan\Model::createQuery() 
                ->select('id', 'topic')
                ->from('inventory')
                ->order('id')
                ->toArray()
                ->execute(); 
        }else{
                return  \Kotchasan\Model::createQuery() 
                ->select('category_id', 'topic')
                ->from('category')
                ->where(array(
                    array('type', $type),
                ))
                ->order('category_id')
                ->toArray()
                ->execute(); 
            }          
    }

    /**
     * อ่านรายชื่อ Category
     *
     * @return \static
     */
    public static function createCategory($type)
    {
        $obj = new static();
        $obj->$type = array();

        if($type == 'topic_id'){
            foreach (self::allCategory($type) as $item) {
                $obj->$type[$item['id']] = $item['topic'];
            }
        }else{
            foreach (self::allCategory($type) as $item) {
                $obj->$type[$item['category_id']] = $item['topic'];
            }
        }
        return $obj;

    }
    /**
     * อ่านรายชื่อ Category สำหรับใส่ลงใน select.
     *
     * @return array
     */
    public function toSelectCategory($type)
    {
        return $this->$type;
    }   
    public function getCategory($id,$type)
    {
        return isset($this->$type[$id]) ? $this->$type[$id] : '';
    }
    /**
     * Query ข้อมูลสำหรับส่งให้กับ DataTable
     *
     * @param array $params
     *
     * @return \Kotchasan\Database\QueryBuilder
     */
    public static function toDataTable($params)
    {

        $where = array();
        if (!empty($params['product_no'])) {
           // $where[] = array('R.product_no', $params['product_no']); 
            $where[] =array('R.product_no', 'LIKE', '%'.$params['product_no'].'%');
        }
        if (!empty($params['operator_id'])) {
            $where[] = array('S.operator_id', $params['operator_id']);
        }
        if ($params['status'] > -1) {
            $where[] = array('S.status', $params['status']);
        }
        if (!empty($params['user_id'])) {
            $where[] = array('R.create_by', $params['user_id']);
        }
        if ($params['memberstatus'] > -1) {
            $where[] = array('U.status', $params['memberstatus']); 
        }
        if ($params['begindate'] != '' || $params['enddate'] != '') {
                $where[] = Sql::BETWEEN('R.create_date', $params['begindate']." 00:00:00", $params['enddate']." 23:59:59");
        }
       if (!empty($params['category_id'])) {
            $where[] = array('V.category_id', $params['category_id']); 
        }
        if (!empty($params['model_id'])) {
            $where[] = array('V.model_id', $params['model_id']);   
        }
        if (!empty($params['type_id'])) {
            $where[] = array('V.type_id', $params['type_id']); 
        }
        if (!empty($params['topic_id'])) {
            $where[] = array('V.id', $params['topic_id']); 
        }
        

        $endtime = static::createQuery()
            ->select('create_date')
            ->from('repair_status')
            ->where(array('id','T.max_id'));
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'max_id'))
            ->from('repair_status')
            ->groupBy('repair_id');

            return  static::createQuery() //return  $a =
            ->select('R.id', 'R.job_id','S.status', 'R.create_date'  ,'S.create_date as enddate'
            , 'R.product_no'
            , 'V.topic'
           // , 'U.name'
           ,'S.cost'//,array( $endtime,'endtime') //'S.operator_id',
           // ,SQL::CONCAT(array(SQL::IFOVER(SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0,SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),SQL::IFHOUR(SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),'')),'Alltime', ':') 
            ,SQL::CONCAT(
                array(
                    SQL::IFOVER(
                        SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0,
                        SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),

                        SQL::IFHOUR(
                            SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                            SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                            SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),
                            
                            (SQL::IFMINUTES(
                                SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                                SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                                SQL::TIMESTAMPDIFF('HOUR','R.create_date',$endtime),0)
                            ),
                            
                            (SQL::IFSECOND(
                                SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                                SQL::TIMESTAMPDIFF('MINUTE','R.create_date',$endtime)
                            )
                        )
                    ),'Alltime', ':'
                    )
            
            )
            ->from('repair R')
            ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
            ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->join('user U', 'LEFT', array('U.id', 'R.create_by'))
            ->where($where)
            
            ;
    }
   
    public static function toDataTable2($params)
    {

        $login = Login::isMember();
        if($login["status"] != 1){
            $where2[] = (array('U.status',$login["status"]));
        }
        $where = array();
        if (!empty($params['type_id'])) {
            $where[] = array('V.type_id', $params['type_id']); 
        }
        if (!empty($params['product_no'])) {
            $where[] =array('R.product_no', 'LIKE', '%'.$params['product_no'].'%');
        }
        if (!empty($params['operator_id'])) {
            $where[] = array('S.operator_id', $params['operator_id']);
        }
        if ($params['status'] > -1) {
            $where[] = array('S.status', $params['status']);
        }
        if (!empty($params['user_id'])) {
            $where[] = array('R.create_by', $params['user_id']);
        }
        if ($params['memberstatus'] > -1) {
            $where[] = array('U.status', $params['memberstatus']); 
        }
       if (!empty($params['category_id'])) {
            $where[] = array('V.category_id', $params['category_id']); 
        }
        if (!empty($params['model_id'])) {
            $where[] = array('V.model_id', $params['model_id']);   
        }
        if (!empty($params['topic_id'])) {
            $where[] = array('V.id', $params['topic_id']); 
        }
        if ($params['begindate'] != '' || $params['enddate'] != '') {
            $where[] = Sql::BETWEEN('R.create_date', $params['begindate']." 00:00:00", $params['enddate']." 23:59:59");
         }
        

        $endtime = static::createQuery()
            ->select('create_date')
            ->from('repair_status')
            ->where(array('id','T.max_id'));
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'max_id'))
            ->from('repair_status')
            ->groupBy('repair_id');

         return static::createQuery() 
            ->select('R.id', 'R.job_id','S.status', 'R.create_date'  ,'S.create_date as end_date'
            , 'R.product_no'
            , 'V.topic'
            ,'C.address','C.customer_name','CT.contact_name','CT.contact_tel','CT.position','R.request_date'
           ,'S.cost' //'S.operator_id',
           ,SQL::CONCAT(
            array(
                SQL::IFOVER(
                    SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0,
                    SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),

                    SQL::IFHOUR(
                        SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                        SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                        SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),
                        
                        (SQL::IFMINUTES(
                            SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                            SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                            SQL::TIMESTAMPDIFF('HOUR','R.create_date',$endtime),0)
                        ),
                        
                        (SQL::IFSECOND(
                            SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),
                            SQL::TIMESTAMPDIFF('MINUTE','R.create_date',$endtime)
                        )
                    )
                ),'Alltime2', ':'
                )
            
            )
            ->from('repair R')
            ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
            ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->join('user U', 'LEFT', array('U.id', 'R.create_by'))
            ->join('customer C', 'LEFT', array('C.id', 'R.customer_id'))
            ->join('contact CT', 'LEFT', array('CT.id', 'R.contact_id'))
            ->where($where)
            ->andWhere($where2)
            ->execute()  
            ;


    }
   
    /**
     * รับค่าจาก action (report.php)
     *
     * @param Request $request
     */
    public function action(Request $request)
    {
        $ret = array();
        // session, referer, member, ไม่ใช่สมาชิกตัวอย่าง
        if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
            if (Login::notDemoMode($login)) {
                // รับค่าจากการ POST
                $action = $request->post('action')->toString();

                // id ที่ส่งมา
                if (preg_match_all('/,?([0-9]+),?/', $request->post('id')->toString(), $match)) {
                    if ($action === 'delete' && Login::checkPermission($login, 'can_manage_repair')) {
                        // ลบรายการสั่งซ่อม
                        $this->db()->delete($this->getTableName('repair'), array('id', $match[1]), 0);
                        $this->db()->delete($this->getTableName('repair_status'), array('repair_id', $match[1]), 0);
                        // reload
                        $ret['location'] = 'reload';
                    } elseif ($action === 'status' && Login::checkPermission($login, array('can_manage_repair', 'can_repair'))) {
                        // อ่านข้อมูลรายการที่ต้องการ
                        $index = \Repair\Detail\Model::get($request->post('id')->toInt());
                        if ($index) {
                            $ret['modal'] = Language::trans(\Repair\Action\View::create()->render($index, $login));
                        }
                    }
                  
                }elseif ($action == 'export') {

						// export รายชื่อ
						$params = $request->getParsedBody();
						unset($params['action']);
						unset($params['src']);
						$params['module'] = 'index-download';
						$params['type'] = 'report';
						$ret['location'] = WEB_URL.'export.php?'.http_build_query($params); 
				}
            }
        }
		
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่า JSON
        echo json_encode($ret);
    }
}
