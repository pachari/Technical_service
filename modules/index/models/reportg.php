<?php
/**
 * @filesource modules/index/models/reportg.php
 *
 
 */

namespace Index\Reportg;

use Gcms\Login;
use Kotchasan\Database\Sql;
use Kotchasan\Http\Request;
use Kotchasan\Language;
use Kotchasan\Date;

/**
 * module=reportg
 
 */
class Model extends \Kotchasan\Model
{
 
   //graph get time of type
   public static function get_time_of_type( $params)
   {
        //Query ตามการค้นหาช่วงวันที่ User เลือก
           if(!empty($params['from']) && !empty($params['to'])){
               
                   if($params['member_id'] == '-1'){
                       $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                       $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                   }else{  
                       $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                       $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                       $where[] = array('U.status', $params['member_id']);
                   }
           }else{
             $where = (array(SQL::MONTH('R.create_date'), SQL::MONTH(date('Y-m-d H:i:s'))));
           }                

          $q1 = static::createQuery()
           ->select('repair_id', Sql::MAX('id', 'max_id'))
           ->from('repair_status')
           ->groupBy('repair_id');
           return static::createQuery() 
           ->select('R.id', 'R.job_id','S.status', 'R.create_date'  ,'S.create_date as end_date' , 'R.product_no', 'V.topic' ,'S.cost' ,'V.type_id','C.topic'
           )
            ->from('repair R')
            ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
            ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
             ->join('user U', 'LEFT', array('U.id', 'R.create_by'))
             ->join('category C', 'LEFT', array('C.category_id','V.type_id'))
            ->where($where)
            ->andWhere(array('C.type','type_id'))
            ->groupby('V.type_id')
            ->execute()
           ; 


   }
   
}
