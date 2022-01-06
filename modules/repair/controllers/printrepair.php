<?php
/**
 * @filesource modules/repair/controllers/printrepair.php
 *
 
 */

namespace Repair\Printrepair;

use Gcms\Login;
//use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;
use Kotchasan\Date;
//use Kotchasan\Currency;

/**
 *  module=repair-printrepair
 
 */
class Controller extends \Kotchasan\Controller //extends \Gcms\Controller
{
    /**
     * รายละเอียดการซ่อม
     *
     * @param Request $request
     *
     * @return string
     */
     public function render(Request $request)
    {

        // สมาชิก
        if (Login::isMember()) {
            // อ่านข้อมูลที่เลือก
            $index = \Repair\Detail\Model::get($request->request('id')->toInt());
            // template
          $templates = Language::get('Repair');

            if ( isset($index) ) { //$index->status   || && isset($templates[$index->status])
                // โหลด template
                $billing = $this->getTemplate('QUO');
                $item = \Repair\Printrepair\Model::get($index->id);
                /*เอารูปภาพE-sig approve มาแสดง  */
                //เอาจากการอัพโหลดรูปลายเซ็นต์
                $img = is_file(ROOT_PATH.DATA_FOLDER.'approve/'.'R'.$index->id.'-'.Date::format($index->date_approve, 'md').'.jpg') ? WEB_URL.DATA_FOLDER.'approve/'.'R'.$index->id.'-'.Date::format($index->date_approve, 'md').'.jpg' : WEB_URL.'modules/inventory/img/noesig.png';   
                //เอาจากรูปที่แนบไว้ในข้อมูลส่วนตัว
                //$img    = is_file(ROOT_PATH.DATA_FOLDER.'E-signature/'.'Esig_'.$item->send_approve.'.jpg') ? WEB_URL.DATA_FOLDER.'E-signature/'.'Esig_'.$item->send_approve.'.jpg' : WEB_URL.'modules/inventory/img/noesig.png';
                /*เอารูปภาพE-sig User ที่เปิด Job มาแสดง  */
                $imgU  = is_file(ROOT_PATH.DATA_FOLDER.'E-signature/'.'Esig_'.$item->user.'.jpg') ? WEB_URL.DATA_FOLDER.'E-signature/'.'Esig_'.$item->user.'.jpg' : WEB_URL.'modules/inventory/img/noesig.png';
                /*เอารูปภาพE-sig User ที่ปิด Job   */
                $imgUC = is_file(ROOT_PATH.DATA_FOLDER.'E-signature/'.'Esig_'.$item->operator_id.'.jpg') ? WEB_URL.DATA_FOLDER.'E-signature/'.'Esig_'.$item->operator_id.'.jpg' : WEB_URL.'modules/inventory/img/noesig.png';
               
                //เช็คกลุ่มผู้ใช้งาน
                $gmember = \Index\Member\Model::getMemberstatus($index->s_group);

                 // Table detail แสดงรายละเอียดหัวงาน
                                $detail = '';
                                //   $detail .= ' <caption style="background-color: white;color: black;  font-weight: bold; font-family: THSarabunNew,Tahoma,Loma;">เลขที่ '.$index->job_id.'</caption>';
                                //   $detail .= ' <caption style="background-color: white;color: black; font-weight: bold; font-family: THSarabunNew,Tahoma,Loma;">วันที่ขอ '.Date::format($item->create_date, 'd M Y H:i').' </caption>';
                                //   $detail .= ' <caption >สำหรับผู้ขอรับบริการ (User) </caption>';
                                $detail .= '<tr>';
                                $detail .= '<th >เลขที่ </th>';
                                $detail .= '<td colspan="3" style="border-bottom-style: solid; border-color: darkgray;">'.$index->job_id.'</td>';
                                $detail .= '</tr>';
                                $detail .= '<tr>';
                                $detail .= '<th >วันที่ขอ </th>';
                                $detail .= '<td style="border-bottom-style: solid; border-color: darkgray;">'.Date::format($item->create_date, 'd M Y H:i').'</td>';
                                $detail .= '<th >วันที่ต้องการ </th>';
                                $detail .= '<td style="border-bottom-style: solid; border-color: darkgray;">'.Date::format($item->request_date, 'd M Y H:i').'</td>';
                                $detail .= '</tr>';
                                $detail .= '<tr>';
                                $detail .= '<th >ลูกค้า </th>';
                                $detail .= '<td style="border-bottom-style: solid; border-color: darkgray;">'.$index->customer_name.'</td>';
                                $detail .= '<th >ชื่อผู้ติดต่อ </th>';
                                $detail .= '<td style="border-bottom-style: solid; border-color: darkgray;">'.$index->contact_name.'</td>';
                                $detail .= '</tr>';
                                $detail .= '<tr>';
                                $detail .= '<th style="width: 10%;">ที่อยู่ </th>';
                                $detail .= '<td style="width: 50%;">'.$index->address.'</td>';
                                $detail .= '<th style="width: 12%;">โทรศัพท์ </th>';
                                $detail .= '<td>'.$index->contact_tel.'</td>';
                                $detail .= '</tr>';

                 // Table detailtopic แสดงวัตถุประส่งค์ แบบ checkbox 
                                $detailtopic = '';
                                $detailtopic = '<caption style="border-style:solid;">จุดประสงค์</caption>';
                            if($index->product_no == 'สร้างตัวอย่าง'){
                                $detailtopic .= '<tr style="text-align:center;">';
                              //  $detailtopic .= '<th style="width:70px">จุดประสงค์ </th>';
                                $detailtopic .= '<td style="width:30%;border-top-style: groove;"><input type="checkbox" checked>	สร้างตัวอย่าง</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"><input type="checkbox" disabled="disabled">ตรวจเยี่ยม</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"><input type="checkbox" disabled="disabled">อื่นๆ</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"></td>';
                                $detailtopic .= '</tr>';
                            }else if($index->product_no == 'ตรวจเยี่ยม'){
                                $detailtopic .= '<tr style="text-align:center;">';
                             //   $detailtopic .= '<th style="width:70px">จุดประสงค์ </th>';
                                $detailtopic .= '<td style="width:30%;border-top-style: groove;"><input type="checkbox"  disabled="disabled">	สร้างตัวอย่าง</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"><input type="checkbox"  checked>ตรวจเยี่ยม</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"><input type="checkbox"  disabled="disabled" >อื่นๆ</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"></td>';
                                $detailtopic .= '</tr>';
                            }else{
                                $detailtopic .= '<tr style="text-align:center;">';
                            //    $detailtopic .= '<th style="width:70px">จุดประสงค์ </th>';
                                $detailtopic .= '<td style="width:30%;border-top-style: groove;"><input type="checkbox"  disabled="disabled">	สร้างตัวอย่าง</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"><input type="checkbox"  disabled="disabled">ตรวจเยี่ยม</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"><input type="checkbox"  checked>อื่นๆ</td>';
                                $detailtopic .= '<td style="border-top-style: groove;"></td>';
                                //  $detailtopic .= '<td >'.$index->topic.'</td>';
                                $detailtopic .= '</tr>';
                            }

                // Table detail แสดงรายละเอียดหัวงาน
                                $detailtask = '';
                                $detailtask .= '<tr>';
                                $detailtask .= '<th style="width:70px">รายละเอียด </th>';
                                $detailtask .= '<td>'.$index->job_description.'</td>';
                                $detailtask .= '</tr>';
                                $detailtask .= '<tr>';

                 //สำหรับผู้อนุมัติ (Approved)
                                $detailapprove = '';
                                $arr_app = \Repair\Printrepair\Model::getapp($index->id);   
                            if($item->status == 7 || $item->status == 9 || $item->status == 10 || $item->status == 11 || $item->status == 12   ){ //$item->status != 1 && $item->status != 2 && $item->status != 3 && $item->status != 5 && $item->status != 6 
                             
                                
                                for($i=0;$i< count($arr_app);$i++){
                                    if(!empty($arr_app[$i]->status)){
                                        if($arr_app[$i]->status == 9 ){
                                            $A[$i] = $arr_app[$i]->comment.' ';
                                            $B[$i] = $arr_app[$i]->date_approve;
                                        }
                                    }
                                } //foreach($A as $comment)foreach($B as $date_approve) 
                                $detailapprove .= '<tr>';
                                $detailapprove .= '<td><img class=signature src="'.$imgU.'"></td>'; 
                                $detailapprove .= '<td> <img class=signature src="'.$img.'"></td>'; 
                                $detailapprove .= '</tr>';
                                $detailapprove .= '<tr>';
                               // $detailapprove .= '<td style="border-left-style:solid;">'.$item->name.' ('.$gmember.')</td>';
                                $detailapprove .= '<td>'.$item->name.'</td>';
                                $detailapprove .= '<td>'.$item->send_approve2.'</td>';
                                $detailapprove .= '</tr>';
                                $detailapprove .= '<tr>';
                                $detailapprove .= '<th  style="text-align: center;width: 55%;">ผู้ขอ</th>';
                                $detailapprove .= '<th style="text-align: center;">ผู้อนุมัติ / หัวหน้าแผนกขึ้นไป</th>';
                                $detailapprove .= '</tr>';
    
                            }else{
                                $detailapprove .= '<tr>';
                                $detailapprove .= '<td><img class=signature src="'.$imgU.'"></td>'; 
                                $detailapprove .= '<td ><img class=signature src="'.WEB_URL.'modules/inventory/img/noesig.png'.'"></td>'; 
                                $detailapprove .= '</tr>';
                                $detailapprove .= '<tr>';
                                $detailapprove .= '<td>'.$item->name.'</td>';
                                $detailapprove .= '<td></td>';
                                $detailapprove .= '</tr>';
                                $detailapprove .= '<tr>';
                                $detailapprove .= '<th  style="text-align: center;width: 55%;">ผู้ขอ</th>';
                                $detailapprove .= '<th  style="text-align: center;">ผู้อนุมัติ / หัวหน้าแผนกขึ้นไป</th>';
                                $detailapprove .= '</tr>';
                            } 

                 //สำหรับฝ่ายTC ส่วน approve
                                $detailtcapprove = '';
                                $detailtcapprove .= '<caption style="border-style:solid;">ตอบกลับจากฝ่ายเทคนิค</caption>';
                            if($item->status == 7 || $item->status == 9 || $item->status == 10    ){    
                            if($item->status == 7 || $index->status == 9){
                                $detailtcapprove .= '<tr>';
                                $detailtcapprove .= '<td  style="width: 420px;"><input type="checkbox"  checked>อนุมัติ</td>';
                                $detailtcapprove .= '<td><input type="checkbox" disabled="disabled">ไม่อนุมัติ</td>';
                                $detailtcapprove .= '</tr>';
                            }else{ // if($item->status == 10)
                                $detailtcapprove .= '<tr>';
                                $detailtcapprove .= '<td style="width: 420px;border-left-style:solid;"><input type="checkbox" disabled="disabled"  >อนุมัติ</td>';
                                $detailtcapprove .= '<td><input type="checkbox" checked >ไม่อนุมัติ</td>';
                                $detailtcapprove .= '</tr>';
                                }
                        }else {
                                $detailtcapprove .= '<tr>';
                                $detailtcapprove .= '<td style="width: 420px;border-left-style:solid;"><input type="checkbox"  disabled="disabled" >อนุมัติ</td>';
                                $detailtcapprove .= '<td><input type="checkbox"  disabled="disabled">ไม่อนุมัติ</td>';
                                $detailtcapprove .= '</tr>';
                        }

                        if( $item->status == 7 || $item->status == 9 || $item->status == 10  ){  
                              //สำหรับฝ่าย TC ส่วนรายละเอียด
                                $detailtc = '';
                                $detailtc .= '<tr>';
                                $detailtc .= '<th style="width:70px" >รายละเอียด </th>';
                                $detailtc .= '<td>'.$item->comment.'</td>';
                                $detailtc .= '</tr>';
                                $detailtc .= '<tr>';

                            //สำหรับฝ่าย TC คนอนุมัติ
                                $detailtchead = '';
                                $detailtchead .= '<tr>';
                                $detailtchead .= '<td></td>'; 
                                $detailtchead .= '<td style="text-align: center;"><img class=signature src="'.$imgUC.'"></td>'; 
                                $detailtchead .= '</tr>';
                                $detailtchead .= '<tr style="text-align:center;">';
                                $detailtchead .= '<td style="width:55%"></td>';
                                $detailtchead .= '<td>'.$item->name_close.'</td>';
                                $detailtchead .= '</tr>';
                                $detailtchead .= '<tr>';
                                $detailtchead .= '<th  ></th>';
                                $detailtchead .= '<th  style="text-align: center;">ผู้อนุมัติ / หัวหน้าแผนกขึ้นไป</th>';
                                $detailtchead .= '</tr>';
                        }else{
                             //สำหรับฝ่าย TC ส่วนรายละเอียด
                                $detailtc = '';
                                $detailtc .= '<tr>';
                                $detailtc .= '<th style="width:70px" >รายละเอียด </th>';
                                $detailtc .= '<td></td>';
                                $detailtc .= '</tr>';
                                $detailtc .= '<tr>';

                            //สำหรับฝ่าย TC คนอนุมัติ
                                $detailtchead = '';
                                $detailtchead .= '<tr>';
                                $detailtchead .= '<td></td>'; 
                                $detailtchead .= '<td ><img class=signature src="'.WEB_URL.'modules/inventory/img/noesig.png'.'"></td>';  
                                $detailtchead .= '</tr>';
                                $detailtchead .= '<tr style="text-align:center;">';
                                $detailtchead .= '<td style="width:55%"></td>';
                                $detailtchead .= '<td></td>';
                                $detailtchead .= '</tr>';
                                $detailtchead .= '<tr>';
                                $detailtchead .= '<th  ></th>';
                                $detailtchead .= '<th  style="text-align: center;">ผู้อนุมัติ / หัวหน้าแผนกขึ้นไป</th>';
                                $detailtchead .= '</tr>';
                        }



                             
                 

                // ภาษาที่ใช้งานอยู่
                $lng = Language::name();
                // ใส่ลงใน template
                $content = array(
                    '/%Job%/' => 'เลขที่'.$index->job_id,
                    '/{LANGUAGE}/' => $lng,
                    '/{CONTENT}/' => $billing['detail'],
                    '/{WEBURL}/' => WEB_URL,
                    '/{TITLE}/' => $billing['title'].'_No.'.$index->job_id,
                    '/<tr>[\r\n\s\t]{0,}<td>[\r\n\s\t]{0,}%DETAIL%[\r\n\s\t]{0,}<\/td>[\r\n\s\t]{0,}<\/tr>/' => $detail,
                    '/<tr>[\r\n\s\t]{0,}<td>[\r\n\s\t]{0,}%DETAILTOPIC%[\r\n\s\t]{0,}<\/td>[\r\n\s\t]{0,}<\/tr>/' => $detailtopic,
                    '/<tr>[\r\n\s\t]{0,}<td>[\r\n\s\t]{0,}%DETAILTASK%[\r\n\s\t]{0,}<\/td>[\r\n\s\t]{0,}<\/tr>/' => $detailtask,
                    '/<tr>[\r\n\s\t]{0,}<td>[\r\n\s\t]{0,}%DETAILTCAPPROVE%[\r\n\s\t]{0,}<\/td>[\r\n\s\t]{0,}<\/tr>/' => $detailtcapprove,
                    '/<tr>[\r\n\s\t]{0,}<td>[\r\n\s\t]{0,}%DETAILTC%[\r\n\s\t]{0,}<\/td>[\r\n\s\t]{0,}<\/tr>/' => $detailtc,
                    '/<tr>[\r\n\s\t]{0,}<td>[\r\n\s\t]{0,}%DETAILTCHEAD%[\r\n\s\t]{0,}<\/td>[\r\n\s\t]{0,}<\/tr>/' => $detailtchead,
                    '/<tr>[\r\n\s\t]{0,}<td>[\r\n\s\t]{0,}%DETAILAPPROVE%[\r\n\s\t]{0,}<\/td>[\r\n\s\t]{0,}<\/tr>/' => $detailapprove,
                    '/%LOGO%/' => is_file(ROOT_PATH.DATA_FOLDER.'/logo/logo.png') ? '<img  class="logo" src="'.WEB_URL.DATA_FOLDER.'/logo/logo.png">' : '',
                    '/%LOGO_2%/' => is_file(ROOT_PATH.DATA_FOLDER.'/logo/latterhead-01_0.png') ? '<img class="logo2"  src="'.WEB_URL.DATA_FOLDER.'/logo/latterhead-01_0.png">' : '',
                    '/%LOGO_3%/' => is_file(ROOT_PATH.DATA_FOLDER.'/logo/latterhead-03.png') ? '<img class="logo3"  src="'.WEB_URL.DATA_FOLDER.'/logo/latterhead-03.png">' : '',
                    '/%LOGO_4%/' => is_file(ROOT_PATH.DATA_FOLDER.'/logo/latterhead-02.png') ? '<img class="logo4"  src="'.WEB_URL.DATA_FOLDER.'/logo/latterhead-02.png">' : '',
                );
                \Repair\Printrepair\View::toPrint($content); 

               
            }
        }
    }

    /**
     * อ่าน template
     *
     * @param string $tempate
     *
     * @return array|null คืนค่าข้อมูล template ถ้าไม่พบคืนค่า null
     */
    public function getTemplate($tempate)
    {
      
        $file = ROOT_PATH.'modules/repair/template/QUO.html';

        if (is_file($file)) {
            // โหลด template
            $file = file_get_contents($file);
            // parse template
            $patt = '/(.*?)<title>(.*?)<\/title>?(.*?)(<detail>(.*?)<\/detail>)?(.*?)<body>(.*?)<\/body>(.*?)/isu';
            $billing = array();
            if (preg_match($patt, $file, $match)) {
                $billing['title'] = $match[2];
                $billing['detail'] = $match[7];
                if (preg_match_all('/<item>([a-z]{0,})<\/item>/isu', $match[6], $items)) {
                    foreach ($items[1] as $i => $row) {
                        if ($row != '') {
                            $billing['details'][] = $row;
                        }
                    }
                }
            }
            return $billing;
        }
        return null;
    }
}
