<?php
/**
 * @filesource modules/repair/models/email.php
 *
 
 */

namespace Repair\Email;

use Kotchasan\Date;
use Kotchasan\Language;
use Kotchasan\Text;

/**
 * ส่งอีเมลไปยังผู้ที่เกี่ยวข้อง
 
 */
class Model extends \Kotchasan\KBase
{
    /**
     * ส่งอีเมลแจ้งการทำรายการ
     *
     * @param int $id
     */
    public static function send($id)
    {
        $sq1_approve =  \Kotchasan\Model::createQuery()
                   ->select('U1.name as send_approve')
                   ->from('user U1')
                   ->where(array('U1.id', 'R.send_approve'));
        $sq1_emailapprove =  \Kotchasan\Model::createQuery()
                   ->select('U2.username as username_approve')
                   ->from('user U2')
                   ->where(array('U2.id', 'R.send_approve'));

        $sq2_s_group =  \Kotchasan\Model::createQuery()
                   ->select('U3.status as s_group')
                   ->from('user U3')
                   ->where(array('U3.id', 'R.create_by'));

        $sq2_topic =  \Kotchasan\Model::createQuery()
                   ->select('C.topic as topic')
                   ->from('category C')
                   ->where(array('C.category_id','S.status'))
                   ->andWhere(array('C.type','repairstatus'));
      
                   // ตรวจสอบรายการที่ต้องการ
        $order = \Kotchasan\Model::createQuery()
            ->from('repair R')
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->join('user U', 'LEFT', array('U.id', 'R.create_by'))
            ->join('customer C', 'LEFT', array('C.id', 'R.customer_id'))
            ->join('contact CT', 'LEFT', array('CT.id', 'R.contact_id'))
            ->join('repair_status S', 'LEFT' , array('S.repair_id','R.id'))
            ->where(array('R.id', $id))
            ->order('S.id DESC')
            ->limit(1)
            ->first('R.job_id'
                        , 'R.product_no'
                        , 'V.topic'
                        , 'R.job_description'
                        , 'R.create_date'
                        , 'R.request_date'
                        , 'U.username'
                        , 'U.name' 
                        , 'S.comment' 
                        , 'S.urgency' 
                        , 'S.status'
                        , array($sq1_approve,'send_approve')
                        , array($sq1_emailapprove,'username_approve')
                        , array($sq2_s_group,'s_group')  
                        , 'S.create_date as approve_date' 
                        , 'R.create_by as customer_id'
                        , array($sq2_topic,'category')
                        ,'R.id'
                        ,'C.address'
                        ,'C.customer_name'
                        ,'CT.contact_name'
                        ,'CT.contact_tel'
                        ,'CT.position' 
                    );  

            //เช็คกลุ่มผู้ใช้งาน
            $gmember = \Index\Member\Model::getMemberstatus($order->s_group);
            if ($order) {
                $ret = array();
                    if (self::$cfg->noreply_email != '' || !empty(self::$cfg->line_api_key)) {
                        //เช็คส่งอีเมลกรณี ส่งขออนุมัติรายการแจ้งซ่อม
                        if($order->status == 8){
                                // ข้อความ
                                $content = array(
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_List of}'.'{LNG_Technical Service} : </b>'.$order->job_id, 
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Received date} :'.'</b>'.Date::format($order->create_date, 'd M Y H:i'),
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_request date} :'.'</b>'.Date::format($order->request_date, 'd M Y H:i'),
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_types of objective} :'.'</b>'.$order->product_no,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Detail} :'.'</b>'.$order->job_description,  
                                    '<br>',
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Customer Name} :'.'</b>'.$order->customer_name,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Address} :'.'</b>'.$order->address,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Contact_name} :'.'</b>'.$order->contact_name.'('.$order->contact_tel.')',
                                '<br>',
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Informer} :'.'</b>'.$order->name.'  {LNG_Member status} :'.'</b>'.$gmember,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Approve} :'.'</b>'.$order->send_approve,
                                    '<br>',
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Status} : {LNG_'.$order->category.'}</b>',
                                // '<br>',
                                // '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Technical Service note} :'.'</b>'.$order->comment,
                                    
                                );   
                                //เช็คส่งเมลสถานะ ส่งขออนุมัติ
                                $msg = Language::trans(implode("\n", $content));
                                $send_approve_msg = $msg.""; //"\nLink url: ".WEB_URL.'index.php?module=repair-approve'
                                // หัวข้ออีเมล
                                $subject = '['.self::$cfg->web_title.'] ';
                                
                                // ส่งอีเมลไปยังผู้อนุมัติ
                                $err = \Kotchasan\Email::send($order->send_approve.'<'.$order->username_approve.'>', self::$cfg->noreply_email, $subject, nl2br($msg));
                                if ($err->error()) {
                                    $ret[] = strip_tags($err->getErrorMessage());
                                }
                                // อีเมลของผู้ดูแล
                                $query = \Kotchasan\Model::createQuery()
                                    ->select('username', 'name')
                                    ->from('user')
                                    ->where(array(
                                        array('social', 0),
                                        array('active', 1),
                                        array('username', '!=', $order->username),
                                    ))
                                    ->andWhere(array(
                                        array('status', 1),
                                        array('permission', 'LIKE', '%,can_config%'),
                                    ), 'OR')
                                    ->cacheOn();
                                $emails = array();
                                foreach ($query->execute() as $item) {
                                    $emails[$item->username] = $item->name.'<'.$item->username.'>';
                                }
                                if (!empty($emails)) {
                                    $err = \Kotchasan\Email::send(implode(',', $emails), self::$cfg->noreply_email, $subject, nl2br($send_approve_msg));
                                    if ($err->error()) {
                                        $ret[] = strip_tags($err->getErrorMessage());
                                    }
                                }
                                if (!empty(self::$cfg->line_api_key)) {
                                    // ส่งไลน์
                                    $err = \Gcms\Line::send($send_approve_msg);
                                    if ($err != '') {
                                        $ret[] = $err;
                                    }
                                } 
                        //เช็คส่งเมลสถานะ ไม่อนุมัติ/อนุมัติ/ส่งมอบเรียบร้อย/ยกเลิกการซ่อม/ซ่อมไม่สำเร็จ/ซ่อมสำเร็จ/รออะไหล่/กำลังดำเนินการ
                        }else if($order->status == 12 || $order->status == 11 || $order->status == 10 || $order->status == 9 || $order->status == 5 || $order->status == 4 || $order->status == 3 || $order->status == 2 ){    
                            // ข้อความ
                                $content2= array(
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_List of}'.'{LNG_Technical Service}  : </b>'.$order->job_id,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Received date} :'.'</b>'.Date::format($order->create_date, 'd M Y H:i'),
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_request date} :'.'</b>'.Date::format($order->request_date, 'd M Y H:i'),
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_types of objective} :'.'</b>'.$order->product_no,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Detail} :'.'</b>'.$order->job_description,                        
                                    '<br>',
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Customer Name} :'.'</b>'.$order->customer_name,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Address} :'.'</b>'.$order->address,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Contact_name} :'.'</b>'.$order->contact_name.'('.$order->contact_tel.')',
                                '<br>',
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Informer} :'.'</b>'.$order->name.'  {LNG_Member status} :'.'</b>'.$gmember,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Approve} :'.'</b>'.$order->send_approve,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Transaction date} :'.'</b>'.Date::format($order->approve_date, 'd M Y H:i'),
                                    '<br>',
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Status} :'.'</b> {LNG_'.$order->category.'} ',
                                );
                    
                                    //เช็คส่งเมลสถานะ อนุมัติ/ไม่อนุมัติ
                                    $msg = Language::trans(implode("\n", $content2));
                                    //$send_approve_msg = $msg."\nURL: ".WEB_URL.'index.php?module=repair-approve';
                                    $admin_msg = $msg.""; //"\nLink url: ".WEB_URL.'index.php?module=repair-setup'                                   
                                    // หัวข้ออีเมล
                                    $subject = '['.self::$cfg->web_title.'] ';                
                                    // ส่งอีเมลไปยังผู้ทำรายการเสมอ
                                    $err = \Kotchasan\Email::send($order->name.'<'.$order->username.'>', self::$cfg->noreply_email, $subject, nl2br($msg));
                                    if ($err->error()) {
                                        $ret[] = strip_tags($err->getErrorMessage());
                                    }
                                    // อีเมลของผู้ดูแล
                                    $query = \Kotchasan\Model::createQuery()
                                        ->select('username', 'name')
                                        ->from('user')
                                        ->where(array(
                                            array('social', 0),
                                            array('active', 1),
                                            array('username', '!=', $order->username),
                                        ))
                                        ->andWhere(array(
                                            array('status', 1),
                                            array('permission', 'LIKE', '%,can_config,%'),//can_manage_repair
                                        ), 'OR')
                                        ->cacheOn();
                                    $emails = array();          
                                    foreach ($query->execute() as $item) {
                                        $emails[$item->username] = $item->name.'<'.$item->username.'>';
                                    }
                                    if (!empty($emails)) {
                                        $err = \Kotchasan\Email::send(implode(',', $emails), self::$cfg->noreply_email, $subject, nl2br($admin_msg));
                                        if ($err->error()) {
                                            $ret[] = strip_tags($err->getErrorMessage());
                                        }
                                    }
                                    if (!empty(self::$cfg->line_api_key)) {
                                        // ส่งไลน์
                                        $err = \Gcms\Line::send($admin_msg);
                                        if ($err != '') {
                                            $ret[] = $err;
                                        }
                                    } 
                            //เช็คส่งเมลสถานะ ส่งมอบเรียบร้อย/ยกเลิกการซ่อม
                        }else if($order->status == 7 || $order->status == 6){  
                                    // ข้อความ
                                    $content3 = array(
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_List of}'.'{LNG_Technical Service}  : </b>'.$order->job_id,
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Received date} :'.'</b>'.Date::format($order->create_date, 'd M Y H:i'),
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_request date} :'.'</b>'.Date::format($order->request_date, 'd M Y H:i'),
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_types of objective} :'.'</b>'.$order->product_no,
                                    '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Detail} :'.'</b>'.$order->job_description,
                                        '<br>',
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Customer Name} :'.'</b>'.$order->customer_name,
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Address} :'.'</b>'.$order->address,
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Contact_name} :'.'</b>'.$order->contact_name.'('.$order->contact_tel.')',
                                        '<br>',
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Informer} :'.'</b>'.$order->name.'  {LNG_Member status} :'.'</b>'.$gmember,
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Approve} :'.'</b>'.$order->send_approve,
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Transaction date} :'.'</b>'.Date::format($order->approve_date, 'd M Y H:i'),
                                        '<br>',
                                        '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Status} :'.'</b> {LNG_'.$order->category.'} ',
                                        //'File Download :'.'<a href="'.WEB_URL.'index.php?_module=repair-setup& &id='.$order->id.'>ไฟล์แบบฟอร์มแจ้งปัญหาการใช้งานไอที (IT)</a></b>',
                                    );   
                                        $msg = Language::trans(implode("\n", $content3));
                                        //$send_approve_msg = $msg."\nURL: ".WEB_URL.'index.php?module=repair-approve';
                                        //$uri = self::$request->createUriWithGlobals(WEB_URL.'index.php');
                                        //$file_print = $uri->createBackUri(array('module' => 'repair-printrepair', 'id' => $order->id));
                                        $admin_msg = $msg.""; //"\nLink url: ".WEB_URL.'index.php?module=repair-setup'
                                        $msg2 = $msg.""; //"\nFile :".'<a href="'.WEB_URL.'index.php?_module=repair-setup& &id='.$order->id.'">แบบฟอร์มแจ้งปัญหาการใช้งานไอที (IT) : '.$order->job_id.'</a>"'
                                        $admin_msg2 = $msg.""; //"\nLink แบบฟอร์มแจ้งปัญหา: <a href=".WEB_URL.'index.php?_module=repair-setup& &id='.$order->id.">".WEB_URL.'index.php?_module=repair-setup& &id='.$order->id."</a> "
                        
                                        // หัวข้ออีเมล
                                        $subject = '['.self::$cfg->web_title.'] ';                
                                        // ส่งอีเมลไปยังผู้ทำรายการเสมอ
                                        $err = \Kotchasan\Email::send($order->name.'<'.$order->username.'>', self::$cfg->noreply_email, $subject, nl2br($msg2));
                                        if ($err->error()) {
                                            $ret[] = strip_tags($err->getErrorMessage());
                                        }
                                        // อีเมลของผู้ดูแล
                                        $query = \Kotchasan\Model::createQuery()
                                            ->select('username', 'name')
                                            ->from('user')
                                            ->where(array(
                                                array('social', 0),
                                                array('active', 1),
                                                array('username', '!=', $order->username),
                                            ))
                                            ->andWhere(array(
                                                array('status', 1),
                                                array('permission', 'LIKE', '%,can_manage_repair,%'),
                                            ), 'OR')
                                            ->cacheOn();
                                        $emails = array();
                        
                                        foreach ($query->execute() as $item) {
                                            $emails[$item->username] = $item->name.'<'.$item->username.'>';
                                        }
                                        if (!empty($emails)) {
                                            $err = \Kotchasan\Email::send(implode(',', $emails), self::$cfg->noreply_email, $subject, nl2br($admin_msg));
                                            if ($err->error()) {
                                                $ret[] = strip_tags($err->getErrorMessage());
                                            }
                                        }
                                        if (!empty(self::$cfg->line_api_key)) {
                                            // ส่งไลน์
                                            $err = \Gcms\Line::send($admin_msg2);
                                            if ($err != '') {
                                                $ret[] = $err;
                                            }
                                        } 
                                        
                        }else{
                            // ข้อความ
                            $content4 = array(
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Technical Service} :'.'</b> '.$order->job_id,
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Received date} :'.'</b>'.Date::format($order->create_date, 'd M Y H:i'),
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_request date} :'.'</b>'.Date::format($order->request_date, 'd M Y H:i'),
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_types of objective} :'.'</b>'.$order->product_no,
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Detail} :'.'</b>'.$order->job_description,
                                '<br>',
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Customer Name} :'.'</b>'.$order->customer_name,
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Address} :'.'</b>'.$order->address,
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Contact_name} :'.'</b>'.$order->contact_name.'('.$order->contact_tel.')',
                                '<br>',
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Informer} :'.'</b>'.$order->name.'  {LNG_Member status} :'.'</b>'.$gmember,
                                '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Approve} :'.'</b>'.$order->send_approve,
                            // '<b style="font-family":"Kanit";"font-size":"30px">'.'{LNG_Status} : {LNG_Technical Service} </b>', //.Language::get('pending')        
                            );
                            //เช็คส่งเมลสถานะ ส่งขออนุมัติ
                            $msg = Language::trans(implode("\n", $content4));
                            $admin_msg = $msg.""; //"\nLink url: ".WEB_URL.'index.php?module=repair-setup'
                                // หัวข้ออีเมล
                                $subject = '['.self::$cfg->web_title.'] ';     
                                // ส่งอีเมลไปยังผู้ทำรายการเสมอ
                                $err = \Kotchasan\Email::send($order->name.'<'.$order->username.'>', self::$cfg->noreply_email, $subject, nl2br($msg));
                                if ($err->error()) {
                                    $ret[] = strip_tags($err->getErrorMessage());
                                }
                                // อีเมลของผู้ดูแล
                                $query = \Kotchasan\Model::createQuery()
                                    ->select('username', 'name')
                                    ->from('user')
                                    ->where(array(
                                        array('social', 0),
                                        array('active', 1),
                                        array('username', '!=', $order->username),
                                    ))
                                    ->andWhere(array(
                                        array('status', 1),
                                        array('permission', 'LIKE', '%,can_manage_repair,%'),
                                    ), 'OR')
                                    ->cacheOn();
                                $emails = array();

                                foreach ($query->execute() as $item) {
                                    $emails[$item->username] = $item->name.'<'.$item->username.'>';
                                }
                                if (!empty($emails)) {
                                    $err = \Kotchasan\Email::send(implode(',', $emails), self::$cfg->noreply_email, $subject, nl2br($admin_msg));
                                    if ($err->error()) {
                                        $ret[] = strip_tags($err->getErrorMessage());
                                    }
                                }
                                if (!empty(self::$cfg->line_api_key)) {
                                    // ส่งไลน์
                                    $err = \Gcms\Line::send($admin_msg);
                                    if ($err != '') {
                                        $ret[] = $err;
                                    }
                                } 
                            }
                    }    
                    // คืนค่า
                    if (self::$cfg->noreply_email != '' || !empty(self::$cfg->line_api_key)) {
                        return empty($ret) ? Language::get('Your message was sent successfully') : implode("\n", $ret);
                    } else {
                        return Language::get('Saved successfully');
                    }
            }
        // not found
        return Language::get('Unable to complete the transaction');
    }
}
