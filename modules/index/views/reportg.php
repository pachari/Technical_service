<?php
/**
 * @filesource modules/index/views/reportg.php
 *
 
 */

namespace Index\Reportg;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Date;


/**
 * module=reportg
 
 */
class View extends \Gcms\View
{
    /**
     * รายงานกราฟแสดงการแจ้งซ่อม
     *
     * @param Request $request
     * @param array   $login
     * @param object  $profile
     *
     * @return string
     */
    public function render(Request $request)
    {
                $login = Login::isMember();
                $isAdmin = Login::checkPermission($login, 'report');
                $params = array(
                    'from' => $request->request('from', date('Y-m-d', strtotime('-7 days')))->date(),
                    'to' => $request->request('to', date('Y-m-d'))->date(),
                );
                // สถานะสมาชิก
                $member_status = array(-1 => '{LNG_all items}');
                if ($isAdmin) {
                    $params['member_id'] = $request->request('dash_Member', -1)->toInt();
                } 
                foreach (self::$cfg->member_status as $key => $value) {
                    $member_status[$key] = '{LNG_'.$value.'}';
                }
                $params['login_id'] = $login;
                //create form search
                $form = Html::create('form', array(
                    'id' => 'report_search_frm',
                    'class' => 'table_nav clear',
                    'action' => 'index.php?module=reportg', 
                    'ajax' => false,
                    'token' => false,
                ));
                $div = $form->add('div');
                $fieldset = $div->add('fieldset');
                    // from
                    $fieldset->add('date', array(
                        'id' => 'from',
                        'label' => '{LNG_Search}{LNG_from}{LNG_Begin date}', 
                        'value' => $params['from'],
                    ));
                    $fieldset = $div->add('fieldset');
                    // to
                    $fieldset->add('date', array(
                        'id' => 'to',
                        'label' => '{LNG_To}',
                        'value' => $params['to'],
                    ));
                    $fieldset = $div->add('fieldset');
                    // repair_first_status
                    $fieldset->add('select', array(
                        'id' => 'dash_Member',
                        'label' => '{LNG_Member}',
                        'options' => $member_status,
                        'value' => $params['member_id'],
                    ));
                    $fieldset = $div->add('fieldset');
                     // submit
                    $fieldset->add('submit', array(
                        'class' => 'button go',
                        'value' => 'GO',
                    ));
                    // id
                    $fieldset->add('hidden', array(
                        'id' => 'id',
                        'value' => $login['id'],
                    ));
                    // submit
                    $fieldset->add('hidden', array(
                        'id' => 'module',
                        'value' => 'reportg',
                    ));

        /*  ------------------------------------------------ กราฟที่ 1-------------------------------- */
                    // แสดงผล                   
                $content = '<section id=report class="setup_frm">'; 
                $content .= $form->render();
                $content .= '<article class="ggraphs clear">';
                $index =  \Repair\Home\Model::get_type($params); 
                    $title = '{LNG_Graph report} {LNG_Type}';
                    $list= '{LNG_List of}{LNG_Technical Service}' ;
                    $headtitle = '{LNG_Technical Service}';
                    $bodytitle = '{LNG_List of}{LNG_Technical Service}';
                        $str = ''; $str_2 = '';

                        for($i=0;$i<count($index);$i++){
                            if( ($index[0]['product_no']) != '0' || is_null($index[0]['product_no']) )  {  
                                $str =  $str.'<th>'. $index[$i]['product_no'].'</th>';
                            }else{  $str = '<th> 0 </th>';    }
                        } 
                        for($i=0;$i<count($index);$i++){
                                if( ($index[0]['count']) != '0' || is_null($index[0]['count']) )  {  
                                    $str_2 =  $str_2.'<td>'. $index[$i]['count'].'</td>';
                                }else{  $str_2 = '<td> 0 </td>';    }
                        } 
                    $content .= '<br><section class=clear>
                    <h4>'.$title.' '.$list.'</h4>
                    <div id="table" class="graphcs">
                        <canvas></canvas>
                        <table class="hidden">
                        <thead>
                            <tr>
                            <th>'.$headtitle.'</th>'. $str
                        .'  </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th> '.$bodytitle.'</th>'. $str_2
                        .'</tr> 
                        </tbody>
                        </table>
                    </div>
                    <script>
                        new GGraphs("table", {
                        type: "pie",
                            centerX: 50 + Math.round($G("table").getHeight() / 2),
                            labelOffset: 70,
                            centerOffset: 60,
                            strokeColor: null,
                            colors: [
                                    "#9400D3",
                                    "#696969",
                                    "#E65100",
                                    "#06d628",
                                    "#FF992A", 
                                    "#009ACD",
                                    "#FF1493",
                                    "#263238",
                                    "#120eeb",
                                    "#06d628",
                                    "#FF9999",
                                    "#8B4513",
                                    "#9999FF",
                                    "#CC66FF",
                                    "#FF3300",
                                    "#FFA500",
                                    "#336666",
                                    "#FFDAB9",
                                    "#CD5C5C",
                                    "#FFD700",
                                    "#9400D3",
                                    "#CC6600",
                                    "#FF9933",
                                    "#FF0066",
                                    "#778899",
                                    "#66CCCC",
                                    "#FFB90F",
                                    "#00CC00",
                                    "#BEBEBE",
                                    "#00FF7F",
                            ]
                        });
                    </script>
                    </section>'; 

    /*  ----------------------------- กราฟที่ 2-------------------------------- */
                // แสดงผล
                $content2 = '<section id=report class="setup_frm">';
                $content2 .= '<article class="ggraphs clear">';
                $index = \Repair\Home\Model::get_category($params);
                    $title = '{LNG_Graph report} {LNG_Category}';
                    $list= '{LNG_List of}{LNG_Technical Service}' ;
                    $headtitle = '{LNG_Technical Service}';
                    $bodytitle = '{LNG_List of}{LNG_Technical Service}';
                    $content2 = '<br><section class=clear>
                    <h4>'.$title.' '.$list.'</h4>
                    <div id="table2" class="graphcs">
                        <canvas></canvas>
                        <table class="hidden">
                        <thead>
                            <tr>
                            <th>'.$headtitle.'</th>
                            <th>วัสดุสำนักงาน</th>            
                            <th>Hardware</th>
                            <th>Software</th>
                            <th>ไม่ระบุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th> '.$bodytitle.'</th>
                            <td>'.$index[0]['2'].'</td>
                            <td>'.$index[0]['3'].'</td>
                            <td>'.$index[0]['4'].'</td>
                            <td>'.$index[0]['5'].'</td>
                        </tbody>
                        </table>
                    </div>
                    <script>
                        new GGraphs("table2", {
                        type: "pie",
                            centerX: 50 + Math.round($G("table2").getHeight() / 2),
                            labelOffset: 70,
                            centerOffset: 60,
                            strokeColor: null,
                            colors: [
                                "#9400D3",
                                "#696969",
                                "#E65100",
                                "#FF992A",
                                "#06d628",
                                "#009ACD",
                                "#FF1493",
                                "#263238",
                                "#120eeb",
                                "#06d628",
                                "#FF9999",
                                "#8B4513",
                                "#9999FF",
                                "#CC66FF",
                                "#FF3300",
                                "#FFA500",
                                "#336666",
                                "#FFDAB9",
                                "#CD5C5C",
                                "#FFD700",
                                "#9400D3",
                                "#CC6600",
                                "#FF9933",
                                "#FF0066",
                                "#778899",
                                "#66CCCC",
                                "#FFB90F",
                                "#00CC00",
                                "#BEBEBE",
                                "#00FF7F",
                            ]
                        });
                    </script>
                    </section>';
            
    /*  ----------------------------- กราฟที่ 3-------------------------------- */
                // แสดงผล
                $content3 = '<section id=report class="setup_frm">';
                $content3 .= '<article class="ggraphs clear">';
                $index3 =  \Repair\Home\Model::get_group($params);    
                        $str_3 = '';$gmember2 = '';
                        // สถานะสมาชิก
                            
                            foreach (self::$cfg->member_status as $key => $value) {
                                $gmember2 .=  '<th>{LNG_'.$value.'}</th>';
                            }
                            for($i=0;$i<=count($index3[0]);$i++){ 
                                if( ($index3[0]['0'.$i]) >= 0 ||  ($index3[0]['0'.$i]) != null)  {  
                                    $str_3 =  $str_3.'<td>'. $index3[0]['0'.$i].'</td>';
                                    
                                }else{  $str_3 = $str_3.'<td> 0 </td>'; }
                        } 

                        $title = '{LNG_Graph report} {LNG_Member status} '; //{LNG_Graph-report}
                        $list= '{LNG_List of} {LNG_Member status}' ;
                        $headtitle = '{LNG_Technical Service}';
                        $bodytitle = '{LNG_List of}{LNG_Technical Service}';
                            $content3 = '<br><section class=clear>
                            <h4>'.$title.'</h4>
                            <div id="table3" class="graphcs">
                            <canvas></canvas>
                            <table class="hidden">
                                <thead>
                                <tr>
                                    <th>'.$headtitle.'</th>'. $gmember2.'       
                                </tr>
                                </thead>
                                <tbody>
                                <tr> <th> '.$bodytitle.'</th> '.$str_3.'</tr>
                                </tbody>
                            </table>
                            </div>
                            <script>
                            new GGraphs("table3", {
                                type: "pie",
                                    centerX: 50 + Math.round($G("table3").getHeight() / 2),
                                    labelOffset: 70,
                                    centerOffset: 60,
                                    strokeColor: null,
                                    colors: [
                                        "#9400D3",
                                        "#696969",
                                        "#E65100",
                                        "#06d628",
                                        "#FF992A",                         
                                        "#009ACD",
                                        "#FF1493",
                                        "#263238",
                                        "#120eeb",
                                        "#06d628",
                                        "#FF9999",
                                        "#8B4513",
                                        "#9999FF",
                                        "#CC66FF",
                                        "#FF3300",
                                        "#FFA500",
                                        "#336666",
                                        "#FFDAB9",
                                        "#CD5C5C",
                                        "#FFD700",
                                        "#9400D3",
                                        "#CC6600",
                                        "#FF9933",
                                        "#FF0066",
                                        "#778899",
                                        "#66CCCC",
                                        "#FFB90F",
                                        "#00CC00",
                                        "#BEBEBE",
                                        "#00FF7F",
                                    ]
                            });
                            </script>
                        </section>';

    /*  ----------------------------- กราฟที่ 4-------------------------------- */
                // แสดงผล
                $content4 = '<section id=report class="setup_frm">';
                $content4 .= '<article class="ggraphs clear">';
                $index =  \Repair\Home\Model::get_status($params);                       
                            $title = '{LNG_Graph report} {LNG_Technical Service process}';
                            $list= '{LNG_List of}{LNG_all items}' ;
                            $headtitle = '';
                            $bodytitle = '{LNG_List of}{LNG_Technical Service}';
                                $content4 = '<br><section class=clear>
                                <h4>'.$title.' </h4>
                                <div id="table4" class="graphcs">
                                <canvas></canvas>
                                <table class="hidden">
                                    <thead>
                                    <tr>
                                        <th>'.$headtitle.'</th>'.
                                        /*<th>แจ้งซ่อม</th>            
                                        /*<th>กำลังดำเนินการ</th>
                                        <th>รออะไหล่</th>'.
                                    <th>ซ่อมสำเร็จ</th>
                                        <th>ซ่อมไม่สำเร็จ</th>*/
                                        '<th>{LNG_Canceljob}</th>
                                        <th>{LNG_Closejob}</th>
                                        <th>{LNG_approve_wait}</th>
                                        <th>{LNG_Approved}</th>
                                        <th>{LNG_Disapproved}</th>
                                        <th>{LNG_Send_Tc}</th>
                                        <th>{LNG_Not_Send_Tc}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th> '.$bodytitle.'</th>'.
                                        /*<td>'.$index[0]['1'].'</td>
                                    <td>'.$value[0]['2'].'</td>
                                    <td>'.$index[0]['3'].'</td>
                                    /* <td>'.$value[0]['4'].'</td>
                                    <td>'.$index[0]['5'].'</td>*/
                                    ' <td>'.$index[0]['6'].'</td>
                                        <td>'.$index[0]['7'].'</td>
                                        <td>'.$index[0]['8'].'</td>
                                        <td>'.$index[0]['9'].'</td>
                                        <td>'.$index[0]['10'].'</td>
                                        <td>'.$index[0]['11'].'</td>
                                        <td>'.$index[0]['12'].'</td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>
                                <script>
                                new GGraphs("table4", {
                                    type: "pie",
                                        centerX: 50 + Math.round($G("table4").getHeight() / 2),
                                        labelOffset: 70,
                                        centerOffset: 60,
                                        strokeColor: null,
                                        colors: [
                                            "#9400D3",
                                            "#696969",
                                            "#E65100",
                                            "#06d628",
                                            "#FF992A",
                                            "#009ACD",
                                            "#FF1493",
                                            "#263238",
                                            "#120eeb",
                                            "#06d628",
                                            "#FF9999",
                                            "#8B4513",
                                            "#9999FF",
                                            "#CC66FF",
                                            "#FF3300",
                                            "#FFA500",
                                            "#336666",
                                            "#FFDAB9",
                                            "#CD5C5C",
                                            "#FFD700",
                                            "#9400D3",
                                            "#CC6600",
                                            "#FF9933",
                                            "#FF0066",
                                            "#778899",
                                            "#66CCCC",
                                            "#FFB90F",
                                            "#00CC00",
                                            "#BEBEBE",
                                            "#00FF7F",
                                        ]
                                });
                                </script>
                            </section>';

    /*  ----------------------------- กราฟที่ 5-------------------------------- */
                    // แสดงผล                   
                $content5 = '<section id=report >'; //class="setup_frm"
                $content5 .= '<article class="ggraphs clear">';
                    $title = '{LNG_Graph report} {LNG_Type}';
                    $list= '{LNG_List of}{LNG_Technical Service} ({LNG_hour})' ;
                    $headtitle = '{LNG_Technical Service}';
                    $bodytitle = '{LNG_List of}{LNG_Technical Service} ';
                        $str = ''; $str_2 = '';
                        $value_name =\repair\Home\Model::createCategory('type_id') ; 
                            foreach($value_name->type_id as $value_name){
                                $str =  $str.'<th>'. $value_name.'</th>';
                            } 
                            $i =0;
                        foreach(\index\Reportg\Model::get_time_of_type($params) as $value2){
                        if($value2->type_id != ''){
                                    $time = DATE::DATEDiff($value2->create_date,$value2->end_date);
                                $time_h ='';
                                if($time['d'] > 0){ $time_h = ( $time['d']*24)+$time['h']; } else {$time_h = $time['h']; }
                                    $str_2 =  $str_2.'<td>'. $time_h.'</td>';
                            $i++;
                            }            
                        } 
                    $content5 .= '<br><section class=clear>
                        <h4>'.$title.' '.$list.'</h4>
                        <div id="table5" class="graphcs">
                            <canvas></canvas>
                            <table class="hidden">
                            <thead>
                                <tr>
                                <th>'.$headtitle.'</th>'. $str
                            .'  </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <th> '.$bodytitle.'</th>'.$str_2
                            .'</tr> 
                            </tbody>
                            </table>
                        </div>
                        <script>
                            new GGraphs("table5", {
                            type: "pie",
                                centerX: 50 + Math.round($G("table5").getHeight() / 2),
                                labelOffset: 70,
                                centerOffset: 60,
                                strokeColor: null,
                                colors: [
                                    "#9400D3",
                                    "#696969",
                                    "#E65100",
                                    "#06d628",
                                    "#FF992A",
                                    "#009ACD",
                                    "#FF1493",
                                    "#263238",
                                    "#120eeb",
                                    "#06d628",
                                    "#FF9999",
                                    "#8B4513",
                                    "#9999FF",
                                    "#CC66FF",
                                    "#FF3300",
                                    "#FFA500",
                                    "#336666",
                                    "#FFDAB9",
                                    "#CD5C5C",
                                    "#FFD700",
                                    "#9400D3",
                                    "#CC6600",
                                    "#FF9933",
                                    "#FF0066",
                                    "#778899",
                                    "#66CCCC",
                                    "#FFB90F",
                                    "#00CC00",
                                    "#BEBEBE",
                                    "#00FF7F",
                                ]
                            });
                        </script>
                        </section>'; 

            
    //ต่อ String  HTML
    $result = $content. ' ' .$content4. ' ' .$content3;
    return  $result ;


	}

    
}
