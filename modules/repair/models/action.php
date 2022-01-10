<?php
/**
 * @filesource modules/repair/models/action.php
 *
 
 */

namespace Repair\Action;

use Gcms\Login;
use Kotchasan\File;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * รับงานซ่อม
 
 */
class Model extends \Kotchasan\Model
{
    /**
     * รับค่า submit จากฟอร์ม action
     *
     * @param Request $request
     *
     * @return JSON
     */
    public function submit(Request $request)
    {
        $ret = array();
        // session, token, can_manage_repair, can_repair

        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if (Login::checkPermission($login, array('can_manage_repair', 'can_repair'))) {
                

                    // อัปโหลดไฟล์
                    $dir = ROOT_PATH.DATA_FOLDER.'file_attachment/';  
                     /* @var $file \Kotchasan\Http\UploadedFile */        
                    foreach ($request->getUploadedFiles() as $item => $file) {
                        if ($item == 'file_attachment') { 
                            $fi = '';          
                                if ($file->hasUploadFile()) {
                                    if (!File::makeDirectory($dir)) {
                                        // ไดเรคทอรี่ไม่สามารถสร้างได้
                                        $ret['ret_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'file_attachment/');
                                    }else {
                                        try {
                                            $file->resizeImage(array('pdf'), $dir, 'R'.$request->post('repair_id')->toInt().'-'.date('mdHis').'.pdf', self::$cfg->inventory_w);//$save['id']                                          
                                            $fi = 'R'.$request->post('repair_id')->toInt().'-'.date('mdHis').'.pdf';
                                        } catch (\Exception $exc) {
                                            // ไม่สามารถอัปโหลดได้
                                            $ret['ret_'.$item] = Language::get($exc->getMessage());
                                        }
                                    }
                            

                                } elseif ($file->hasError()) {
                                    // ข้อผิดพลาดการอัปโหลด
                                    $ret['ret_'.$item] = Language::get($file->getErrorMessage());
                                    $fi = '';

                                }
                        //    }
                        }

                    }
                   
                    try {
                        $save = array(
                            'member_id' => $login['id'],
                            'comment' => $request->post('comment')->topic(),
                            'status' => $request->post('status')->toInt(),
                           
                            'operator_id' => $login['id'],
                            'cost' => $request->post('cost')->toDouble(),
                            'create_date' => date('Y-m-d H:i:s'),
                            'repair_id' => $request->post('repair_id')->toInt(),
                            'attachment' => $fi,
                            'operator_id' => $request->post('operator_id', $login['id'])->toInt(),
                        );
    
                         
                    if (empty($save['status'])) {
                        // ไม่ได้เลือก status
                        $ret['ret_status'] = 'Please select';

                    } else {
                             // บันทึก
                             $this->db()->insert($this->getTableName('repair_status'), $save);
                             // อนุมัติ ส่งอีเมลไปยังผู้ที่เกี่ยวข้อง
                             $ret['alert'] = \Repair\Email\Model::send($save['repair_id']);

                             // คืนค่า
                             $ret['alert'] = Language::get('Saved successfully');
                             $ret['modal'] = 'close';
                             $ret['location'] = 'reload';
                             // clear
                             $request->removeToken();
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

    public function submit2(Request $request)
    {
        $ret = array();
        // session, token, approve_manage_repair, approve_repair

        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if (Login::checkPermission($login, array( 'approve_repair'))) {
                try {
                    $save = array(
                        'member_id' => $login['id'],
                        'comment' => $request->post('comment')->topic(),
                        'status' => $request->post('status')->toInt(),
                        'operator_id' => $login['id'],
                        'cost' => $request->post('cost')->toDouble(),
                        'create_date' => date('Y-m-d H:i:s'),
                        'repair_id' => $request->post('repair_id')->toInt(),
                        
                    );
                    // อัปโหลดไฟล์
              // $dir = ROOT_PATH.DATA_FOLDER.'approve/';  
                     /* @var $file \Kotchasan\Http\UploadedFile */        
                     /*    foreach ($request->getUploadedFiles() as $item => $file) {
                        if ($item == 'signature_approve') {
           
                            if ($file->hasUploadFile()) {
                                if (!File::makeDirectory($dir)) {
                                    // ไดเรคทอรี่ไม่สามารถสร้างได้
                                    $ret['ret_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'approve/');
                                }else {
                                    try {
                                        $file->resizeImage(array('jpg', 'jpeg', 'png'), $dir, 'R'.$request->post('repair_id')->toInt().'-'.date('md').'.jpg', self::$cfg->inventory_w);//$save['id']                                          
                                    } catch (\Exception $exc) {
                                        // ไม่สามารถอัปโหลดได้
                                        $ret['ret_'.$item] = Language::get($exc->getMessage());
                                    }
                                }
                          

                            } elseif ($file->hasError()) {
                                // ข้อผิดพลาดการอัปโหลด
                                $ret['ret_'.$item] = Language::get($file->getErrorMessage());

                            }
                        }

                    }*/
                    if (empty($ret)) {
                        // อัปโหลดไฟล์
                        $dir = ROOT_PATH.DATA_FOLDER.'approve/';
                        foreach ($request->getUploadedFiles() as $item => $file) {
                            if ($item == 'signature_approve') {
                            //if (preg_match('/^E-signature)$/', $item, $match)) {
                                /* @var $file \Kotchasan\Http\UploadedFile */
                                if (!File::makeDirectory($dir)) {
                                    // ไดเรคทอรี่ไม่สามารถสร้างได้
                                    $ret['ret_'.$item]  = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'approve/');
                                } elseif ($file->hasUploadFile()) {
                                    if (!$file->validFileExt(array('jpg', 'jpeg', 'png'))) {
                                        // ชนิดของไฟล์ไม่รองรับ
                                        $ret['ret_'.$item]  = Language::get('The type of file is invalid'); //.$match[1]]
                                    } else {
                                        try {
                                           // $file->moveTo($dir.$match[1].'.png');
                                           $file->moveTo($dir.'R'.$request->post('repair_id')->toInt().'-'.date('md').'.jpg');
                                        } catch (\Exception $exc) {
                                            // ไม่สามารถอัปโหลดได้
                                            $ret['ret_'.$item]  = Language::get($exc->getMessage()); //.$match[1]]
                                        }
                                    }
                                } elseif ($file->hasError()) {
                                    // ข้อผิดพลาดการอัปโหลด
                                    $ret['ret_'.$item]    = Language::get($file->getErrorMessage()); //'.$match[1]] 
                                }
                            }
                        }
                    }


                    if (empty($save['status']) || $save['status'] == '8' ) {
                        // ไม่ได้เลือก status
                        $ret['ret_status'] = 'Please select';
                    }elseif($file->hasUploadFile()){

                          // บันทึก
                            $this->db()->insert($this->getTableName('repair_status'), $save);

                            if($save['status'] == 9 || $save['status'] == 10 || $save['status'] == 11 || $save['status'] == 12){
                                // อนุมัติ ส่งอีเมลไปยังผู้ที่เกี่ยวข้อง
                                $ret['alert'] = \Repair\Email\Model::send($save['repair_id']);
                            } 
                              
                            // คืนค่า
                            $ret['alert'] = Language::get('Saved successfully');
                            $ret['modal'] = 'close';
                            $ret['location'] = 'reload';
                            // clear
                            $request->removeToken(); 
                    } else {
                        $ret['ret_signature_approve'] = 'Please select'; 
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
