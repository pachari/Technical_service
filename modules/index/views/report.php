<?php
/**
 * @filesource modules/index/views/report.php
 *
 
 */

namespace Index\Report;

use Gcms\Login;
use Kotchasan\DataTable;
use Kotchasan\Date;
use Kotchasan\Http\Request;

use function PHPSTORM_META\type;

/**
 * module=report
 
 */
class View extends \Gcms\View
{
    /**
     * @var obj
     */
    private $statuses;
    /**
     * @var obj
     */
    private $operators;

    /**
     * รายการซ่อม (ช่างซ่อม)
     *
     * @param Request $request
     * @param array   $login
     *
     * @return string
     */
    public function render(Request $request, $login)
    {
        
		/*// สำหรับปุ่ม export
        $export = array();*/
        $params = array(
            'status' => $request->request('status', -1)->toInt(),
        );
        $isAdmin = Login::checkPermission($login, 'report');
        // สถานะการซ่อม
        $this->statuses     = \Repair\Status\Model::create();
        $this->operators    = \Repair\Operator\Model::create();
        $this->userrepair   = \index\Report\Model::create(); 
        $this->catagory_id  = \index\Report\Model::createCategory('category_id');
        $this->model_id     = \index\Report\Model::createCategory('model_id');
        $this->type_id      = \index\Report\Model::createCategory('type_id');
        $this->topic_id     = \index\Report\Model::createCategory('topic_id');
        $this->product_no   = \index\Report\Model::createProduct('product_no');
        
          //ดึงข้อมูล Product มาแสดงให้เลือก
          $product_no = array();
          if ($isAdmin) {
              $product_no[0] = '{LNG_all items}';
              $params['product_no'] = $request->request('product_no')->topic();
          } else {
              $params['product_no'] = array(0, $login['id']);
          } 
          foreach ($this->product_no->toSelectProduct() as $k => $v) {
              if ($isAdmin || $k == $login['id']) {
                  $product_no[$k] = $v;
              }
          }

         // ดึงข้อมูล หมวดหมู่ มาแสดงให้เลือก
         $catagory_id = array();
         $model_id = array();
         $type_id = array();
         $topic_id = array();
         if ($isAdmin) {
             $catagory_id[0] = '{LNG_all items}';
             $model_id[0] = '{LNG_all items}';
             $type_id[0] = '{LNG_all items}';
             $topic_id[0] = '{LNG_all items}';
          /*   $params['category_id'] = $request->request('category_id')->toInt();
             $params['model_id'] = $request->request('model_id')->toInt();
             $params['type_id'] = $request->request('type_id')->toInt();*/
             $params['topic_id'] = $request->request('topic_id')->toInt();
         } else {
             /* $params['category_id'] = array(0, $login['id']);
             $params['model_id'] = array(0, $login['id']);
             $params['type_id'] = array(0, $login['id']);*/
             $params['topic_id'] = array(0, $login['id']);
         }
         foreach ($this->catagory_id->toSelectCategory('category_id') as $k => $v) {
             if ($isAdmin || $k == $login['id']) {
                 $catagory_id[$k] = $v;
             }
         }
         foreach ($this->model_id->toSelectCategory('model_id') as $k => $v) {
            if ($isAdmin || $k == $login['id']) {
                $model_id[$k] = $v;
            }
        }
        foreach ($this->type_id->toSelectCategory('type_id') as $k => $v) {
            if ($isAdmin || $k == $login['id']) {
                $type_id[$k] = $v;
            }
        }
        foreach ($this->topic_id->toSelectCategory('topic_id') as $k => $v) {
            if ($isAdmin || $k == $login['id']) {
                $topic_id[$k] = $v;
            }
        }

        //ดึงข้อมูล User มาแสดงให้เลือก
        $userrepair = array();
        if ($isAdmin) {
            $userrepair[0] = '{LNG_all items}';
            $params['user_id'] = $request->request('user_id')->toInt();
        } else {
            $params['user_id'] = array(0, $login['id']);
        } 
        foreach ($this->userrepair->toselect() as $k => $v) {
            if ($isAdmin || $k == $login['id']) {
                $userrepair[$k] = $v;
            }
        }
        // สถานะสมาชิก
        $member_status = array(-1 => '{LNG_all items}');
        if ($isAdmin) {
            $params['memberstatus'] = $request->request('memberstatus', -1)->toInt();
        } 
        else { $params['memberstatus'] = array(0, $login['id']); }
        foreach (self::$cfg->member_status as $key => $value) {
            $member_status[$key] = '{LNG_'.$value.'}';
        }
        
        // ดึงข้อมูล ผู้ดำเนินการ มาแสดงให้เลือก
        $operators = array();
        if ($isAdmin) {
            $operators[0] = '{LNG_all items}';
            $params['operator_id'] = $request->request('operator_id')->toInt();
        } else {
            $params['operator_id'] = array(0, $login['id']);
        }
        foreach ($this->operators->toSelect() as $k => $v) {
            if ($isAdmin || $k == $login['id']) {
                $operators[$k] = $v;
            }
        }
        $params['begindate'] = $request->request('begindate')->topic();
        $params['enddate'] = $request->request('enddate')->topic();
       
        // URL สำหรับส่งให้ตาราง
        $uri = self::$request->createUriWithGlobals(WEB_URL.'index.php');
        
            /*   1 วัน=24 ชั่วโมง / 24 ชั่วโมง=3,600 นาที / 3,600 นาที=86,400 วินาที */$i=0;
            foreach(\index\Report\Model::toDataTable2($params) as $value){
                
                $time = DATE::DATEDiff($value->create_date,$value->end_date);
                $Alltime = $time['d'].':'.$time['h'].':'.$time['i'];//.':'.$time['s']; $time['m'].':'.
                
                if( $Alltime <> ''){
                        $in[$i]["id"]       = $value->id;
                        $in[$i]["job_id"]   = $value->job_id;
                        $in[$i]["status"]   = $value->status;
                        $in[$i]["create_date"]  = $value->create_date;
                        $in[$i]["request_date"]  = $value->request_date;
                        $in[$i]["customer_name"]   = $value->customer_name;
                      
                      //  $in[$i]["end_date"]     = $value->end_date;
                        $in[$i]["product_no"]   = $value->product_no;
                      //  $in[$i]["topic"]        = $value->topic;
                      //  $in[$i]["cost"]         = $value->cost ;
                        //$in[$i]["Alltime2"]      = $value->Alltime2;
                        $in[$i]["Alltime"]      = $Alltime;

                    $i+=1;
                } 
                } 

        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
           // 'model' => //\index\Report\Model::toDataTable($params),
            'datas' => $in,
            'perPage' => $request->cookie('report_perPage', 30)->toInt(),
            /* เรียงลำดับ */
            'sort' => $request->cookie('report_sort', 'create_date desc')->toString(),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('id', 'today', 'end', 'remain' , 'active'),
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('job_id','topic','product_no','cost','Alltime'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/index/model/report/action',
            //'action' => 'index.php/repair/model/report/action',
            'actionCallback' => 'dataTableActionCallback',
            
            'actions' => array(
                  array(
                    'class' => 'button orange icon-excel',
                    'id' => 'export&'.http_build_query($params),
                    'text' => '{LNG_Download} {LNG_List of }{LNG_Technical Service}',   
                ),
            ),
            /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
            'filters' =>  array(
                array(
                    'name' => 'begindate',
                    'type' => 'date',
                    'text' => '{LNG_Received date} ',
                   'value' => $params['begindate'], 
                ),
                array(
                    'name' => 'enddate',
                    'type' => 'date',
                    'text' => '{LNG_to}',
                    'value' => $params['enddate'],      
                ), 
                array(
                    'name' => 'status',
                    'text' => '{LNG_Technical Service status}',
                    'options' => array(-1 => '{LNG_all items}') + $this->statuses->toSelect(),
                    'value' => $params['status'],
                ),
                array(
                    'name' => 'memberstatus',
                    'text' => '{LNG_Member}',
                    'options' => $member_status,
                    'value' => $params['memberstatus'],
                    ),
                array(
                    'name' => 'user_id',
                    'text' => '{LNG_Informer}',
                    'options' => $userrepair,
                    'value' => $params['user_id'],
                ),
                array(
                    'name' => 'operator_id',
                    'text' => '{LNG_Operator}',
                    'options' => $operators,
                    'value' => $params['operator_id'],
                ),
                array(
                    'name' => 'product_no',
                    'text' => '{LNG_types of objective}',
                    'options' => $product_no,
                    'maxlength' => 20,
                    'value' => $params['product_no'],
                ),
                array(
                    'class' => 'icon-reset button brown notext',
                    'href' =>'index.php?module=report',
                    'style' => '-webkit-border-radius: 5px;',
                    'title' => '{LNG_Reset}'
                ),    
            ),
            
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                    'job_id' => array(
                        'text' => '{LNG_Job No.}',
                    ),
                    'status' => array(
                        'text' => '{LNG_Technical Service status}',
                        'class' => 'center',
                        'sort' => 'status',
                    ),
                    'create_date' => array(
                        'text' => '{LNG_Received date}',
                        'class' => 'center',
                        'sort' => 'create_date',
                    ),
                    'request_date' => array(
                        'text' => '{LNG_request date}',
                        'sort' => 'create_date',
                    ),
                    'customer_name' => array(
                        'text' => '{LNG_Customer Name}',
                        'sort' => 'create_date',
                    ),
                    'product_no' => array(
                        'text' => '{LNG_types of objective}',
                        'class' => 'left',
                    ),
                    'Alltime' => array(
                        'text' => '{LNG_working_hours}', // (d:h:i)
                        'class' => 'right',
                    ),
                    'name' => array(
                        'text' => '{LNG_Informer}',
                        'sort' => 'name',
                    ),                  
                  /*  'cost' => array(
                        'text' => '{LNG_Cost}',
                        'class' => 'center',
                    ),*/
                    'operator_id' => array(
                        'text' => '{LNG_Operator}',
                        'class' => 'center',
                    ),
                
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'create_date' => array(
                    'class' => 'center',
                ),
                'operator_id' => array(
                    'class' => 'center',
                ),
                'status' => array(
                    'class' => 'center',
                ), 
                'cost' => array(
                    'class' => 'right',
                ),
                'Alltime' => array(
                    'class' => 'right',
                   
                ),
            ),
            /* ปุ่มแสดงในแต่ละแถว */
            'buttons' => array(
                'description' => array(
                    'class' => 'icon-report button purple',
                    'href' => $uri->createBackUri(array('module' => 'repair-detail', 'id' => ':id')),
                    'title' => '{LNG_Technical Service job description}',
                ),
            ),
        ));

        
        // สามารถแก้ไขใบรับซ่อมได้
       /* if ($isAdmin) {
            $table->actions[] = array(
                'id' => 'action',
                'class' => 'ok',
                'text' => '{LNG_With selected}',
                'options' => array(
                    'delete' => '{LNG_Delete}',
                ),
            );
            $table->buttons['edit'] = array(
                'class' => 'icon-edit button green',
                'href' => $uri->createBackUri(array('module' => 'repair-receive', 'id' => ':id')),
                'title' => '{LNG_Edit} {LNG_Technical Service details}',
            );
        }*/
    
        // save cookie
        setcookie('report_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('report_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);
        // คืนค่า HTML
        
        return $table->render2();
        
    }

    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว
     *
     * @param array  $item ข้อมูลแถว
     * @param int    $o    ID ของข้อมูล
     * @param object $prop กำหนด properties ของ TR
     *
     * @return array คืนค่า $item กลับไป
     */
    public function onRow($item, $o, $prop)
    {
        $item['create_date']    = Date::format($item['create_date'], 'd M Y H:i');
        $item['request_date']    = Date::format($item['request_date'], 'd M Y H:i');
        $item['status']         = '<mark class=term style="background-color:'.$this->statuses->getColor($item['status']).'">'.$this->statuses->get($item['status']).'</mark>';
        $item['operator_id']    = $this->operators->get($item['operator_id']);
        $item['user_id']        = $this->userrepair->getuser($item['user_id']);
        $item['memberstatus']   = isset(self::$cfg->member_status[$item['status']]) ? '<span class=status'.$item['status'].'>{LNG_'.self::$cfg->member_status[$item['status']].'}</span>' : '';
        $item['begindate']      = Date::format($item['begindate'], 'd M Y H:i');
        $item['enddate']        = Date::format($item['enddate'], 'd M Y H:i'); 
        $item['category_id']    = $this->catagory_id->getCategory($item['category_id'],'category_id'); 
        $item['model_id']       = $this->model_id->getCategory($item['model_id'],'model_id'); 
        $item['type_id']        = $this->type_id->getCategory($item['type_id'],'type_id'); 
        $item['product_no']     = $this->product_no->getProduct($item['product_no']);  
        return $item;
    }
}
