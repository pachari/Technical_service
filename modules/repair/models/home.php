<?php

/**
 * @filesource modules/repair/models/home.php
 *
 
 */

namespace Repair\Home;

use Gcms\Login;
use Kotchasan\Database\Sql;
use Kotchasan\Language;
use Gcms\Config;
use Kotchasan\Http\Request;
use Kotchasan\Collection;

/** 
 * module=repair-home
 * 
 * โมเดลสำหรับอ่านข้อมูลแสดงในหน้า  Home
 
 */


 
class Model extends \Kotchasan\Model
{
    /**
     * อ่านงานซ่อมใหม่วันนี้
     *
     * @return object
     */
    public static function getNew($login)
    {

            $where = array(
                array(Sql::DATE('S.create_date'), date('Y-m-d')),
            );
            // พนักงาน
                $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair'));
                if ($isStaff) {
                    $status = isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 8;
                    $where[] = array('S.status', $status);
                } else {
                    $where[] = array('R.create_by', $login['id']);
                }
                $q1 = static::createQuery()
                    ->select('repair_id', Sql::min('id', 'id'))
                    ->from('repair_status')
                    ->groupBy('repair_id');
                $query = static::createQuery()
                    ->selectCount()
                    ->from('repair_status S')
                    ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
                    ->where($where);
                if (!$isStaff) {
                    $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
                }
                $search = $query->toArray()
                    ->execute();

                if (!empty($search)) {
                    return (object) array(
                        'isStaff' => $isStaff,
                        'count' => $search[0]['count'],
                    );
                }
                return 0;
    }


    public static function getNew3($login)
    {
        /*$where = array(
            array(Sql::DATE('S.create_date'), date('Y-m-d')),
        );*/
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair'));
        if ($isStaff) {
            $status = 11;//isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 11;
            $where[] = array('S.status', $status);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);


        if (!$isStaff) { // if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
        } else {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('R.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();
        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getStatusclose($login)
    {
        /*$where = array(
            array(Sql::DATE('S.create_date'), date('Y-m-d')),
        );*/
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair'));
        if ($isStaff) {
          //  $status = isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 1;
            $status = array('7'); 
            $where[] = array('S.status', $status);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);


        if (!$isStaff) { // if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
        } else {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('R.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();
        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getNotSendapprove($login)
    {
        /*$where = array(
            array(Sql::DATE('S.create_date'), date('Y-m-d')),
        );*/
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair'));
        if ($isStaff) {
            $status = 12;//isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 11;
            $where[] = array('S.status', $status);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);


        if (!$isStaff) { // if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
        } else {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('R.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();
        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getStatuscancel($login)
    {
        /*$where = array(
            array(Sql::DATE('S.create_date'), date('Y-m-d')),
        );*/
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair'));
        if ($isStaff) {
          //  $status = isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 1;
            $status = array('6'); 
            $where[] = array('S.status', $status);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);


        if (!$isStaff) { // if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
        } else {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('R.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();
        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getStatuswaitParts($login)
    {
        /*$where = array(
            array(Sql::DATE('S.create_date'), date('Y-m-d')),
        );*/
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair'));
        if ($isStaff) {
          //  $status = isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 1;
            $status = array('3'); 
            $where[] = array('S.status', $status);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);


        if (!$isStaff) { // if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
        } else {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('R.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();
        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }

    public static function getAlltoday($login)
    {
        $q3 =  date('Y-m' . '-01 00:00:00');
        $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));


        $where = array(
            // array(Sql::DATE('S.create_date'), date('Y-m-d')),

        );
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair'));
        if ($isStaff) {
            //$status = isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 1;
           // $where[] = array('S.status', $status);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::min('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);

        if (!$isStaff) { // if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $query->andwhere(SQL::BETWEEN('R.create_date', $q3, $q2));
        } else {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $query->andwhere(SQL::BETWEEN('R.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();

        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getSendapprove($login)
    {
        $where = array();
        // array(Sql::DATE('S.create_date'), date('Y-m-d')),
        //);
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair', 'approve_manage_repair', 'approve_repair'));
        if ($isStaff) {

            $status = isset(self::$cfg->repair_status) ? self::$cfg->repair_status : 8;
            $where[] = array('S.status', 8);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            //->select(date('Y-m-d'))
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);

        if (!$isStaff) { // if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
        } else {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('S.create_date', $q3, $q2));
        }

        $search = $query->toArray()
            ->execute();

        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getSendapprove2($login)
    {
        $where = array();
        // array(Sql::DATE('S.create_date'), date('Y-m-d')),
        //);
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('approve_repair'));
        if ($isStaff) {

            $status = isset(self::$cfg->repair_status) ? self::$cfg->repair_status : 8;
            $where[] = array('S.status', 8);
        } else {
            $where[] = array('S.status', 8);
            //$where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))    
            ->where($where);

      if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $query->join('user U', 'INNER', array('U.id', 'R.create_by'));

            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->Andwhere(SQL::BETWEEN('S.create_date', $q3, $q2));
            $query->Andwhere(array('U.head', $login['id'])); 
          //  $query->join('user U', 'INNER', array('U.id', 'R.create_by'));
        }
    //    $query->Andwhere(array('U.head', $login['id'])); 
        /*else{
            $query->join('user U', 'INNER', array('U.id', 'R.create_by'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->Andwhere(SQL::BETWEEN('S.create_date', $q3, $q2));
            $query->Andwhere(array('U.head', $login['id'])); 
        }*/
     
        $search = $query->toArray()
            ->execute();

        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getApprove($login)
    {
        $where = array();
        // array(Sql::DATE('S.create_date'), date('Y-m-d')),
        //);
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair', 'approve_manage_repair', 'approve_repair'));
        if ($isStaff) {

            //$status = isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 8;
            $where[] = array('S.status', 9);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);
        if ($isStaff) { //if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('S.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();

        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function getNoneApprove($login)
    {
        $where = array();
        // array(Sql::DATE('S.create_date'), date('Y-m-d')),
        //);
        // พนักงาน
        $isStaff = Login::checkPermission($login, array('can_manage_repair', 'can_repair', 'approve_manage_repair', 'approve_repair'));
        if ($isStaff) {

            //$status = isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 8;
            $where[] = array('S.status', 10);
        } else {
            $where[] = array('R.create_by', $login['id']);
        }
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $query = static::createQuery()
            ->selectCount()
            ->from('repair_status S')
            ->join(array($q1, 'T'), 'INNER', array(array('T.repair_id', 'S.repair_id'), array('T.id', 'S.id')))
            ->where($where);
        if ($isStaff) { //if (!$isStaff) {
            $query->join('repair R', 'INNER', array('R.id', 'S.repair_id'));
            $q3 =  date('Y-m' . '-01 00:00:00');
            $q2 = SQL::LAST_DAY(date('Y-m-d 23:59:59'));
            $query->andwhere(SQL::BETWEEN('S.create_date', $q3, $q2));
        }
        $search = $query->toArray()
            ->execute();

        if (!empty($search)) {
            return (object) array(
                'isStaff' => $isStaff,
                'count' => $search[0]['count'],
            );
        }
        return 0;
    }
    public static function get_monthly()
    {

        return  static::createQuery()
            ->select(
                (array(
                    Sql::YEAR('create_date', 'YEAR'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 1, 1, 0), '1'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 2, 1, 0), '2'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 3, 1, 0), '3'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 4, 1, 0), '4'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 5, 1, 0), '5'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 6, 1, 0), '6'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 7, 1, 0), '7'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 8, 1, 0), '8'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 9, 1, 0), '9'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 10, 1, 0), '10'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 11, 1, 0), '11'),
                    Sql::SUM(Sql::IF(Sql::MONTH('create_date'), 12, 1, 0), '12')
                ))
            )
            ->from('repair')
            ->toArray()
            ->execute();
    }
    public static function get_status($params)
    {

        if(!empty($params['from']) && !empty($params['to'])){
                
            if($params['member_id'] == '-1'){
                $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
            }else{  
                $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                $where[] = array('U.status',$params['member_id']);
            }
        }else{
            $where = (array(SQL::MONTH('R.create_date'), SQL::MONTH(date('Y-m-d H:i:s'))));
        }    
       
        
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'max_id'))
            ->from('repair_status')
            ->groupBy('repair_id');

            return  static::createQuery() 
            ->select(
                (array(
                    Sql::YEAR('R.create_date', 'YEAR'),
                    Sql::SUM(Sql::IF('S.status', 1, 1, 0), '1'),
                    Sql::SUM(Sql::IF('S.status', 2, 1, 0), '2'),
                    Sql::SUM(Sql::IF('S.status', 3, 1, 0), '3'),
                    Sql::SUM(Sql::IF('S.status', 4, 1, 0), '4'),
                    Sql::SUM(Sql::IF('S.status', 5, 1, 0), '5'),
                    Sql::SUM(Sql::IF('S.status', 6, 1, 0), '6'),
                    Sql::SUM(Sql::IF('S.status', 7, 1, 0), '7'),
                    Sql::SUM(Sql::IF('S.status', 8, 1, 0), '8'),
                    Sql::SUM(Sql::IF('S.status', 9, 1, 0), '9'),
                    Sql::SUM(Sql::IF('S.status', 10, 1, 0), '10'),
                    Sql::SUM(Sql::IF('S.status', 11, 1, 0), '11'),
                    Sql::SUM(Sql::IF('S.status', 12, 1, 0), '12'),
                    Sql::count('R.create_date', 'ALL')
                ))
            )
            ->from('repair R')
            ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
            ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->join('user U', 'LEFT', array('U.id', 'R.create_by'))
            ->where($where)
            ->toArray()
            ->execute();
            
    }
    public static function get_group($params)
    {

        if(!empty($params['from']) && !empty($params['to'])){
                
            if($params['member_id'] == '-1'){
                $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
            }else{  
                $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                $where[] = array('U.status',$params['member_id']);
            }
        }else{
            $where = (array(SQL::MONTH('R.create_date'), SQL::MONTH(date('Y-m-d H:i:s'))));
        }    

            return  static::createQuery()
            ->select(
                (array(
                          //  Sql::SUM(Sql::IF('U.status', 0, 1, 0), '0'),
                            Sql::SUM(Sql::IF('U.status', 1, 1, 0), '1'),
                            Sql::SUM(Sql::IF('U.status', 2, 1, 0), '2'),
                            Sql::SUM(Sql::IF('U.status', 3, 1, 0), '3'),
                            Sql::SUM(Sql::IF('U.status', 4, 1, 0), '4'),
                            Sql::SUM(Sql::IF('U.status', 5, 1, 0), '5'),
                            Sql::SUM(Sql::IF('U.status', 6, 1, 0), '6'),
                            Sql::SUM(Sql::IF('U.status', 7, 1, 0), '7'),
                            Sql::SUM(Sql::IF('U.status', 8, 1, 0), '8'),
                            Sql::SUM(Sql::IF('U.status', 9, 1, 0), '9'),
                            Sql::SUM(Sql::IF('U.status', 10, 1, 0), '10'),
                            Sql::SUM(Sql::IF('U.status', 11, 1, 0), '11'),
                            Sql::SUM(Sql::IF('U.status', 12, 1, 0), '12'),
                            Sql::SUM(Sql::IF('U.status', 13, 1, 0), '13'),
                            Sql::SUM(Sql::IF('U.status', 14, 1, 0), '14'),
                            Sql::SUM(Sql::IF('U.status', 15, 1, 0), '15'),
                            Sql::SUM(Sql::IF('U.status', 16, 1, 0), '16'),
                            Sql::SUM(Sql::IF('U.status', 17, 1, 0), '17'),
                            Sql::SUM(Sql::IF('U.status', 18, 1, 0), '18'),
                            Sql::SUM(Sql::IF('U.status', 19, 1, 0), '19'),
                            Sql::SUM(Sql::IF('U.status', 20, 1, 0), '20'),
                            Sql::SUM(Sql::IF('U.status', 21, 1, 0), '21'),
                            Sql::SUM(Sql::IF('U.status', 22, 1, 0), '22'),
                            Sql::SUM(Sql::IF('U.status', 23, 1, 0), '23'),
                            Sql::SUM(Sql::IF('U.status', 24, 1, 0), '24'),
                            Sql::SUM(Sql::IF('U.status', 25, 1, 0), '25'),
                            Sql::SUM(Sql::IF('U.status', 26, 1, 0), '26'),
                            Sql::SUM(Sql::IF('U.status', 27, 1, 0), '27'),
                            Sql::SUM(Sql::IF('U.status', 28, 1, 0), '28'),
                            Sql::SUM(Sql::IF('U.status', 29, 1, 0), '29'),
                            Sql::SUM(Sql::IF('U.status', 30, 1, 0), '30'),
                            Sql::SUM(Sql::IF('U.status', 31, 1, 0), '31'),
                            Sql::SUM(Sql::IF('U.status', 32, 1, 0), '32'),
                            Sql::SUM(Sql::IF('U.status', 33, 1, 0), '33'),
                            Sql::SUM(Sql::IF('U.status', 34, 1, 0), '34'),
                            Sql::SUM(Sql::IF('U.status', 35, 1, 0), '35'),
                            Sql::SUM(Sql::IF('U.status', 36, 1, 0), '36'),
                            Sql::SUM(Sql::IF('U.status', 37, 1, 0), '37'),
                            Sql::SUM(Sql::IF('U.status', 38, 1, 0), '38'),
                ))
            )
            ->from('repair R')
            ->join('user U', 'LEFT', array('U.id', 'R.create_by'))
            ->where($where)
            //->groupBy('U.status')
            ->toArray()
            ->execute() ;
               
    }
    public static function get_category($params)
    {
           //Query ตามการค้นหาช่วงวันที่ User เลือก
           if(!empty($params['from']) && !empty($params['to'])){
                
                if($params['member_id'] == '-1'){
                    $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                    $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                }else{  
                    $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                    $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                    $where[] = array('U.status',$params['member_id']);
                }
            }else{
                $where = (array(SQL::MONTH('R.create_date'), SQL::MONTH(date('Y-m-d H:i:s'))));
            }    

        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'max_id'))
            ->from('repair_status')
            ->groupBy('repair_id');

        return  static::createQuery()
            ->select(
                (array(
                    Sql::SUM(Sql::IF('V.category_id', 2, 1, 0), '2'),
                    Sql::SUM(Sql::IF('V.category_id', 3, 1, 0), '3'),
                    Sql::SUM(Sql::IF('V.category_id', 4, 1, 0), '4'),
                    Sql::SUM(Sql::IF('V.category_id', 5, 1, 0), '5'),
                ))
            )
            ->from('repair R')
            ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
            ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->join('user U', 'LEFT', array('U.id', 'R.create_by'))
            ->where($where)
            ->toArray()
           ->execute();
        
        
    }
    public static function get_type( $params)
    {
     
         //Query ตามการค้นหาช่วงวันที่ User เลือก
            if(!empty($params['from']) && !empty($params['to'])){
                
                    if($params['member_id'] == '-1'){
                        $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                        $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                    }else{  
                        $where[] = array(Sql::DATE('R.create_date'), '>=', $params['from']);
                        $where[] = array(Sql::DATE('R.create_date'), '<=', $params['to']);
                        $where[] = array('U.status', $params['member_id']);//$params['login_id']['status']);
                    }
            }else{
              $where = (array(SQL::MONTH('R.create_date'), SQL::MONTH(date('Y-m-d H:i:s'))));
            }
            return static::createQuery()
            ->select(SQL::COUNT('R.id','count'),'R.product_no'    )
            ->from('repair R')
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->where($where)
            ->groupby('R.product_no')
            ->order('I.inventory_id ASC')
            ->toArray()
            ->execute(); 


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

        foreach (\Repair\Home\Model::allCategory($type) as $item) {
            $obj->$type[$item['type_id']] = $item['topic'];
        }
        return $obj;
    }
    public static function allCategory($type)
    {
       
            $q2 = static::createQuery() 
            ->select('C.topic')
            ->from('category C')
            ->Where(array('C.category_id','V.type_id'))
            ->andWhere(array( array('c.type', $type)));
            
            return static::createQuery()  //return
            ->select('V.type_id',array($q2,'topic')) //'V.type_id',
            ->from('repair R')
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->groupBy('V.type_id')
            ->toArray()
            ->execute(); 
       
    }

    
    
}
