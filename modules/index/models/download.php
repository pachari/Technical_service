<?php
/**
 * @filesource modules/index/models/download.php
 *
 
 */

namespace Index\Download;

use Gcms\Login;
use Kotchasan\Database\Sql;
use Kotchasan\Http\Request;
use Kotchasan\Language;
use Kotchasan\Date;


/**
 * เพิ่ม/แก้ไข ข้อมูลนักเรียน
 
 */
class Model extends \Kotchasan\Model
{
    /**
     * Query ข้อมูลนักเรียน.
     *
     * @param array $params
     * @param int   $active
     *
     * @return array
     */
    public static function getAll($params)
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
        $repairstatus = static::createQuery()
        ->select('C.topic')
        ->from('category C')
        ->where(array('C.category_id', 'S.status'))
        ->andwhere(array('C.type', 'repairstatus'));
        $catagory = static::createQuery()
            ->select('C.topic')
            ->from('category C')
            ->where(array('C.category_id', 'V.category_id'))
            ->andwhere(array('C.type', 'category_id'));
        $model = static::createQuery()
            ->select('C.topic')
            ->from('category C')
            ->where(array('C.category_id', 'V.model_id'))
            ->andwhere(array('C.type', 'model_id'));
        $type= static::createQuery()
            ->select('C.topic')
            ->from('category C')
            ->where(array('C.category_id', 'V.type_id'))
            ->andwhere(array('C.type', 'type_id'));
        $q0_name = static::createQuery()
            ->select('U1.name as send_approve2')
            ->from('user U1')
            ->where(array('U1.id', 'R.send_approve'));
        $q0_group = static::createQuery()    
            ->select('U3.status as s_group')
            ->from('user U3')
            ->where(array('U3.id', 'R.create_by'));
        $q2_user = static::createQuery()    
            ->select('U4.name as name_close')
            ->from('user U4')
            ->where(array('U4.id', 'S.operator_id'));
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'max_id'))
            ->from('repair_status')
            ->groupBy('repair_id');
            
        return static::createQuery()
            ->select('R.*', 'U.name', 'U.username', 'V.topic' ,'U.id_card','U.id as user','U.status as gstatus'
            , 'S.create_date as date_approve', 'S.status', 'S.comment'
            , 'S.operator_id', 'S.id status_id'
            ,'S.create_date as enddate','S.cost'
            ,array( $q0_name,'send_approve2')
            ,array( $q0_group,'s_group')
            ,array( $q2_user,'name_close')
            ,array( $catagory,'catagory')
            ,array( $model,'model')
            ,array( $type,'type')
            ,array( $repairstatus,'repairstatus')
            ,SQL::CONCAT(array(SQL::IFOVER(SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0,SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),SQL::IFHOUR(SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),'')),'Alltime', ':') 
            //,SQL::CONCAT(array(SQL::IFOVER(SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0,SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),SQL::IFHOUR(SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('DAY','R.create_date',$endtime),0),(SQL::IFMINUTES(SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('HOUR','R.create_date',$endtime),0)),(SQL::IFSECOND(SQL::TIMESTAMPDIFF('SECOND','R.create_date',$endtime),SQL::TIMESTAMPDIFF('MINUTE','R.create_date',$endtime)))),'Alltime', ':') 
            ,'C.address'
            ,'C.customer_name'
            ,'CT.contact_name'
            ,'CT.contact_tel'
            ,'CT.position' 
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
            ->order('S.id DESC')
            ->execute();


    }

}
