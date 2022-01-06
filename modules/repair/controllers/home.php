<?php

/**
 *
 */

namespace Repair\Home;


use Gcms\Login;
use Kotchasan\Http\Request;


/**
 *
 */
class Controller extends \Kotchasan\KBase
{


    /**
     * ฟังก์ชั่นสร้าง card แบบกล่องจำนวน
     *
     * @param Request         $request
     * @param \Kotchasan\Html $card
     * @param array           $login
     */
    public static function addCard(Request $request, $card, $login)
    {
        $arr = array('0');
        $datas = \Repair\Home\Model::getNew($login ,  $arr);
        $datas2 = \Repair\Home\Model::getNew3($login);
        $datas_close = \Repair\Home\Model::getStatusclose($login);
        $datas_cancel = \Repair\Home\Model::getStatuscancel($login);
        $datas_waitParts = \Repair\Home\Model::getStatuswaitParts($login);
        $datas_Alltoday = \Repair\Home\Model::getAlltoday($login);
        $datas_Sendapprove = \Repair\Home\Model::getSendapprove($login);
        $datas_NotSendapprove = \Repair\Home\Model::getNotSendapprove($login);
        $datas_Sendapprove2 = \Repair\Home\Model::getSendapprove2($login);
        $datas_NoneApprove = \Repair\Home\Model::getNoneApprove($login);
        $datas_Approve = \Repair\Home\Model::getApprove($login);
      
       if ($datas->isStaff) {
            \Index\Home\Controller::renderCard($card, 'icon-calendar', '{LNG_Technical Service list}', number_format($datas_Alltoday->count), '{LNG_Per Month}', 'index.php?module=repair-setup'); // '{LNG_Job today}
            \Index\Home\Controller::renderCard($card, 'icon-users', '{LNG_Technical Service list}', number_format($datas->count), '{LNG_Job today}', 'index.php?module=repair-setup');
            \Index\Home\Controller::renderCard($card, 'icon-new', '{LNG_Technical Service list}', number_format($datas2->count), '{LNG_Send_Tc}', 'index.php?module=repair-setup&amp;status=11');// . (isset(self::$cfg->repair_first_status) ? '&amp;status=' . self::$cfg->repair_first_status : ''));
            \Index\Home\Controller::renderCard($card, 'icon-clock', '{LNG_Technical Service list}', number_format($datas_NotSendapprove->count), '{LNG_Not_Send_Tc}', 'index.php?module=repair-setup&amp;status=12');
            \Index\Home\Controller::renderCard($card, 'icon-clock', '{LNG_Technical Service list}', number_format($datas_Sendapprove->count), '{LNG_approve_wait}', 'index.php?module=repair-setup&amp;status=8');
            \Index\Home\Controller::renderCard($card, 'icon-verfied', '{LNG_Technical Service list}', number_format($datas_Approve->count), '{LNG_Approved}', 'index.php?module=repair-setup&amp;status=9');
            \Index\Home\Controller::renderCard($card, 'icon-close', '{LNG_Technical Service list}', number_format($datas_NoneApprove->count), '{LNG_Disapproved}', 'index.php?module=repair-setup&amp;status=10');
            \Index\Home\Controller::renderCard($card, 'icon-valid', '{LNG_Technical Service list}', number_format($datas_close->count), '{LNG_Closejob}', 'index.php?module=repair-setup&amp;status=7');
          //  \Index\Home\Controller::renderCard($card, 'icon-invalid', '{LNG_Technical Service list}', number_format($datas_cancel->count), '{LNG_Canceljob}', 'index.php?module=repair-setup&amp;status=6');  
          //   \Index\Home\Controller::renderCard($card, 'icon-compare', '{LNG_Technical Service list}', number_format($datas_waitParts->count), '{LNG_WaitParts}', 'index.php?module=repair-setup&amp;status=3');
        } else {
            \Index\Home\Controller::renderCard($card, 'icon-tags', '{LNG_Technical Service list}', number_format($datas_Alltoday->count), '{LNG_Per Month}', 'index.php?module=repair-history');
            \Index\Home\Controller::renderCard($card, 'icon-users', '{LNG_Technical Service list}', number_format($datas->count), '{LNG_Job today}', 'index.php?module=repair-history');
            \Index\Home\Controller::renderCard($card, 'icon-clock', '{LNG_Technical Service list}', number_format($datas_Sendapprove2->count), '{LNG_approve_wait}', 'index.php?module=repair-approve&amp;status=8');
        //    \Index\Home\Controller::renderCard($card, 'icon-new', '{LNG_Technical Service list}', number_format($datas2->count), '{LNG_Send_Tc}', 'index.php?module=repair-approve&amp;status=11');
         //   \Index\Home\Controller::renderCard($card, 'icon-verfied', '{LNG_Technical Service list}', number_format($datas_Approve->count), '{LNG_Approved}', 'index.php?module=repair-history&amp;status=9');
        }
    }

    //ส่วนแสดงกราฟ
    public static function addBlock(Request $request, $card, $login)
    {       

       $datas = \Repair\Home\Model::getNew($login);
        $data_monthly = \Repair\Home\Model::get_monthly($login);

        if ($datas->isStaff) {
            \Index\Home\Controller::renderCard2($card,  $data_monthly, '{LNG_Graph monthly report}', '{LNG_Month}', '{LNG_Technical Service} {LNG_all items}'); 
              
        }
    }

    

}
