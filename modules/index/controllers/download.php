<?php
/**
 *
 */

namespace Index\Download;

use Kotchasan\Http\Request;
use Kotchasan\Language;
use Gcms\Login;
use Kotchasan\Date;

/**
 * module=index-download
 *
 */
 
class Controller extends \Kotchasan\Controller
{
  
    public function export(Request $request)
    {
        if ($request->isReferer()) {
                $header = array();
                if (Login::checkPermission(Login::isMember(), 'report_technical')) { //report
                    
                        //รับค่าไปค้นหาข้อมูล
                        $params = array(); 
                        $params['no']                        = '';
                        $params['status']                   = $request->get('status')->toInt();
                        $params['product_no']          = $request->get('product_no')->toInt();
                        $params['topic_id']                = $request->get('topic_id')->toInt();
                        $params['user_id']                 = $request->get('user_id')->toInt();
                        $params['memberstatus']     = $request->get('memberstatus')->toInt();
                        $params['operator_id']          = $request->get('operator_id')->toInt();
                        $params['begindate']            = $request->get('begindate')->topic();
                        $params['enddate']               = $request->get('enddate')->topic();
                        //ส่วนหัวคอลัมน์ ใช้ eng เนื่องจากบางที csv อ่านไทยไม่ได้
                        $header['no']                       = Language::get('number'); 
                        $header['job_id']                  = Language::get('Job No.');
                        $header['status']                  = Language::get('Status');
                        $header['begindate']            = Language::get('Received date');
                         //  $header['enddate']        = Language::get('End date');
                        $header['requestdate']         = Language::get('request date');
                        //   $header['time']                   = Language::get('working_hours');
                        $header['customer_name']   = Language::get('Customer Name');
                        $header['address']                = Language::get('Address');
                        $header['contact_name']      = Language::get('Contact_name');
                        $header['contact_tel']           = Language::get('Contact_tel');
                        $header['user_id']                 = Language::get('Informer');
                        $header['memberstatus']     = Language::get('Member');
                        $header['product_no']          = Language::get('types of objective');
                        $header['job_description']   = Language::get('Description');
                        $header['send_approve2']                    = Language::get('Approve');
                        $header['date_approve']                       = Language::get('Transaction date');
                        $header['comment_approve']               = Language::get('Comment');
                        $header['operator_id']                          = Language::get('Technical Service man');
                        $header['date_approve_operator']       = Language::get('Transaction date');
                        $header['comment']                             = Language::get('Comment');
                        //  $header['cost']                                   = Language::get('Cost');
               
                            /*   1 วัน=24 ชั่วโมง / 24 ชั่วโมง=3,600 นาที / 3,600 นาที=86,400 วินาที */$i=0;
                         /*   foreach(\index\Report\Model::toDataTable2($params) as $value){
                                
                                $time = DATE::DATEDiff($value->create_date,$value->end_date);
                                $Alltime = $time['d'].':'.$time['h'].':'.$time['i'];//.':'.$time['s']; $time['m'].':'.
                                
                                if( $Alltime <> ''){
                                        $in[$i]["id"]                  = $value->id;
                                        $in[$i]["job_id"]           = $value->job_id;
                                        $in[$i]["status"]           = Language::get($value->status);
                                        $in[$i]["create_date"]  = $value->create_date;
                                        $in[$i]["end_date"]      = $value->end_date;
                                        $in[$i]["product_no"]  = $value->product_no;
                                    // $in[$i]["cost"]            = $value->cost ;
                                        //$in[$i]["Alltime2"]     = $value->Alltime2;
                                        $in[$i]["Alltime"]          = $Alltime;

                                    $i+=1;
                                } 
                            } */

                        $datas = array();
                        $person  = array();
                        // data report
                        foreach (\Index\Download\Model::getAll($params) as $item) { 
                            
                                    //หาเวลารวม
                                    $time = DATE::DATEDiff($item->create_date,$item->enddate);
                                    $Alltime = $time['d'].':'.$time['h'].':'.$time['i'];//.':'.$time['s']; $time['m'].':'. 
                                    //เช็คกลุ่มผู้ใช้งาน
                                    $gmember = \Index\Member\Model::getMemberstatus($item->gstatus);
                                        
                                        if($item->status == 7 || $item->status == 9 || $item->status == 10 || $item->status == 11 || $item->status == 12 ){

                                                //หาข้อมูลอนุมัติหัวหน้างาน
                                                $arr_app = \Repair\Printrepair\Model::getapp($item->id);  
                                                for($i=0;$i<= count($arr_app);$i++){
                                                    if($arr_app[$i]->status == 9 || $arr_app[$i]->status == 10 || $arr_app[$i]->status == 11 || $arr_app[$i]->status == 12){ 
                                                        $A[$i] = $arr_app[$i]->comment.' ';
                                                        $B[$i] = $arr_app[$i]->date_approve;
                                                    }
                                                } foreach($A as $comment)foreach($B as $date_approve)                

                                                 if($item->status == 7 || $item->status == 9 || $item->status == 10){ 
                                                        //ดึงข้อมูลลงคอลัมน์
                                                        ++$person['no'];
                                                        $person['job_id']                           = $item->job_id;
                                                        $person['status']                           = Language::get($item->repairstatus);
                                                        $person['begindate']                    = $item->create_date;
                                                        //   $person['enddate']                   = $item->enddate;
                                                        $person['requestdate']                 = $item->request_date;
                                                        //  $person['time']                          =  $Alltime;
                                                        $person['customer_name']          = $item->customer_name;
                                                        $person['address']                       = $item->address;
                                                        $person['contact_name']             = $item->contact_name;
                                                        $person['contact_tel']                  = $item->contact_tel;
                                                        $person['user_id']                        = $item->name;
                                                        $person['memberstatus']            = $gmember;
                                                        $person['product_no']                  = $item->product_no;
                                                        $person['job_description']           = $item->job_description;
                                                        $person['send_approve2']            = $item->send_approve2;
                                                        $person['date_approve']              = $date_approve;
                                                        $person['comment_approve']      = $comment;
                                                        $person['operator_id']                  = $item->name_close;
                                                        $person['date_approve_operator']      = $item->date_approve;
                                                        $person['comment']                     = $item->comment;
                                                        //     $person['cost']                           = $item->cost;
                                                    }else{
                                                           //ดึงข้อมูลลงคอลัมน์
                                                            ++$person['no'];
                                                            $person['job_id']                           = $item->job_id;
                                                            $person['status']                           = Language::get($item->repairstatus);
                                                            $person['begindate']                    = $item->create_date;
                                                            //   $person['enddate']                   = $item->enddate;
                                                            $person['requestdate']                 = $item->request_date;
                                                            //  $person['time']                          =  $Alltime;
                                                            $person['customer_name']          = $item->customer_name;
                                                            $person['address']                       = $item->address;
                                                            $person['contact_name']             = $item->contact_name;
                                                            $person['contact_tel']                  = $item->contact_tel;
                                                            $person['user_id']                        = $item->name;
                                                            $person['memberstatus']            = $gmember;
                                                            $person['product_no']                  = $item->product_no;
                                                            $person['job_description']           = $item->job_description;
                                                            $person['send_approve2']            = $item->send_approve2;
                                                            $person['date_approve']              = $date_approve;
                                                            $person['comment_approve']      = $comment;
                                                            $person['operator_id']                  = '';
                                                            $person['date_approve_operator']      = '';
                                                            $person['comment']                     = '';
                                                            //     $person['cost']                           = $item->cost;
                                                    }

                                        }else{
                                                    //ดึงข้อมูลลงคอลัมน์แบบไม่ใช่สถานะ 9-12
                                                    ++$person['no'];
                                                    $person['job_id']                           = $item->job_id;
                                                    $person['status']                           = Language::get($item->repairstatus);
                                                    $person['begindate']                    = $item->create_date;
                                                    //   $person['enddate']                    = $item->enddate;
                                                    $person['requestdate']                 = $item->request_date;
                                                    // $person['time']                          =  $Alltime;
                                                    $person['customer_name']          = $item->customer_name;
                                                    $person['address']                       = $item->address;
                                                    $person['contact_name']             = $item->contact_name;
                                                    $person['contact_tel']                  = $item->contact_tel;
                                                    $person['user_id']                        = $item->name;
                                                    $person['memberstatus']            = $gmember;
                                                    $person['product_no']                 = $item->product_no;
                                                    $person['job_description']          = $item->job_description;     
                                                    $person['send_approve2']          = '';
                                                    $person['date_approve']             = '';
                                                    $person['comment_approve']     = '';
                                                    $person['operator_id']                = '';
                                                    $person['date_approve_operator']  = '';
                                                    $person['comment']                   = '';
                                                    //    $person['cost']                         = $item->cost;
                                    
                                        }

                    $datas[] = $person;
                }
                //mb_convert_encoding($header, 'Windows-874','utf-8');
                // export
                return \Kotchasan\Csv::send('Report Technical Service Online', $header, $datas, self::$cfg->csv_language); 
                
            }else {
                    // 404
                    header('HTTP/1.0 404 Not Found');
            }
                exit;

        }
    }


}
