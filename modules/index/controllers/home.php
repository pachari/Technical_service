<?php
/**
 * @filesource modules/index/controllers/home.php
 *
 
 */

namespace Index\Home;

use Gcms\Login;
use Kotchasan\Collection;
use Kotchasan\Html;
use Kotchasan\Date;
use Kotchasan\Http\Request;

/**
 * module=repair-home
 
 */
class Controller extends \Gcms\Controller
{
    /**
     * Dashboard
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {		
        // ไตเติล
        $this->title = self::$cfg->web_title.' - '.self::$cfg->web_description;
        // เมนู
        $this->menu = 'home';
        // สมาชิก
        $login = Login::isMember();
        // สำหรับตรวจสอบว่าเว็บไซต์ต้องเข้าระบบก่อนเสมอ (dashboard)
        if ($login) {
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-home">{LNG_Home}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-dashboard">{LNG_Dashboard}</h2>',
            ));

            // card
            $card = new Collection();
            $menu = new Collection();
            $block = new Collection();
            // โหลดโมดูลที่ติดตั้งแล้ว
            $modules = \Gcms\Modules::create();
            foreach ($modules->getControllers('Home') as $className) {
                if (method_exists($className, 'addCard')) {
                    $className::addCard($request, $card, $login);
                }
                if (method_exists($className, 'addMenu')) {
                    $className::addMenu($request, $menu, $login);
                }
                if (method_exists($className, 'addBlock')) {
                    $className::addBlock($request, $block, $login);
                }
	
            }
            // แสดงจำนวนสมาชิกทั้งหมด
            if ($card->count() < 4 && Login::checkPermission($login, 'can_config')) {
                self::renderCard($card, 'icon-users', '{LNG_Users}', number_format(\Index\Member\Model::getCount()), '{LNG_Member list}', 'index.php?module=member');
            }
            // dashboard
            $dashboard = $section->add('div', array(
                'class' => 'dashboard clear',
            ));
            // render card
            foreach ($card as $item) {
                $dashboard->add('div', array(
                    'class' => 'card',
                    'innerHTML' => $item,
                ));
            }
            // render quick menu
            if ($menu->count() > 0) {
                $dashboard = $section->add('div', array(
                    'class' => 'dashboard clear',
                ));
                $dashboard->add('h3', array(
                    'innerHTML' => '<span class=icon-menus>{LNG_Quick Menu}</span>',
                ));
                $n = 0;
                foreach ($menu as $k => $item) {
                    if ($n == 0 || $n % 4 == 0) {
                        $ggrid = $dashboard->add('div', array(
                            'class' => 'ggrid row',
                        ));
                    }
                    $ggrid->add('section', array(
                        'class' => 'qmenu block3 float-left',
                        'innerHTML' => $item,
                    ));
                    ++$n;
                }
            }
            // render block
            if ($block->count() > 0) {
                foreach ($block as $k => $item) {
                    $section->add('div', array(
                        'class' => 'dashboard clear',
                        'innerHTML' => $item,
                    ));
                }

            }
           
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }

    /**
     * ฟังก์ชั่นสร้าง card ในหน้า Home
     *
     * @param Collection $card
     * @param string     $icon
     * @param string     $title
     * @param string     $value
     * @param string     $link
     * @param string     $url
     * @param string     $target
     */
    public static function renderCard($card, $icon, $title, $value, $link, $url = null, $target = '')
    {
        if ($url === null) {
            $content = '<span class="card-item">';
            $end = '</span>';
        } else {
            $content = '<a class="card-item" href="'.$url.'"'.(empty($target) ? '' : ' target="'.$target.'"').'>';
            $end = '</a>';
        }
        $content .= '<span class="card-subitem '.$icon.' icon"></span>';
        $content .= '<span class="card-subitem">';
        $content .= '<span class="cuttext title" title="'.strip_tags($title).'">'.$title.'</span>';
        $content .= '<b class="cuttext">'.$value.'</b>';
        $content .= '<span class="cuttext title" title="'.strip_tags($link).'">'.$link.'</span>';
        $content .= '</span>'.$end;
        $card->set(\Kotchasan\Password::uniqid(), $content);
    }
    public static function renderCard2($card, $value ,$title,$headtitle,$bodytitle) 
    {		

      $content2 = '<br><section class=clear>
        <h4>'.$title.'</h4>
        <div id="table2" class="graphcs">
          <canvas></canvas>
          <table class="hidden">
            <thead>
              <tr>
                <th> '.$headtitle.'</th>
                <th>'.Date::monthName(1).'</th>
                <th>'.Date::monthName(2).'</th>
                <th>'.Date::monthName(3).'</th>
                <th>'.Date::monthName(4).'</th>
                <th>'.Date::monthName(5).'</th>
                <th>'.Date::monthName(6).'</th>
                <th>'.Date::monthName(7).'</th>
                <th>'.Date::monthName(8).'</th>
                <th>'.Date::monthName(9).'</th>
                <th>'.Date::monthName(10).'</th>
                <th>'.Date::monthName(11).'</th>
                <th>'.Date::monthName(12).'</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th> '.$bodytitle.'</th>
                <td>'.$value[0]['1'].'</td>
                <td>'.$value[0]['2'].'</td>
                <td>'.$value[0]['3'].'</td>
                <td>'.$value[0]['4'].'</td>
                <td>'.$value[0]['5'].'</td>
                <td>'.$value[0]['6'].'</td>
                <td>'.$value[0]['7'].'</td>
                <td>'.$value[0]['8'].'</td>
                <td>'.$value[0]['9'].'</td>
                <td>'.$value[0]['10'].'</td>
                <td>'.$value[0]['11'].'</td>
                <td>'.$value[0]['12'].'</td>
              </tr>'
              /* ต้องกลับมาเขียนเพิ่มถ้าข้ามปี
              <tr>
                <th> '.$bodytitle.'</th>
                <td>'.$value[1]['1'].'</td>
                <td>'.$value[2]['2'].'</td>
                <td>'.$value[3]['3'].'</td>
                <td>'.$value[4]['4'].'</td>
                <td>'.$value[5]['5'].'</td>
                <td>'.$value[6]['6'].'</td>
                <td>'.$value[7]['7'].'</td>
                <td>'.$value[8]['8'].'</td>
                <td>'.$value[9]['9'].'</td>
                <td>'.$value[10]['10'].'</td>
                <td>'.$value[11]['11'].'</td>
                <td>'.$value[12]['12'].'</td>
              </tr>*/
              .'
            </tbody>
          </table>
        </div>
        <script>
          new GGraphs("table2", {
            type: "bar",
            colors: [
              "#FF4500",
              "#008000",
            ]
          });
        </script>
      </section>';
          $card->set(\Kotchasan\Password::uniqid(), $content2); 

          
    }
    public static function renderCard3($card, $value ,$title,$headtitle,$bodytitle,$allitems,$list) 
    {
       $content3 = '<br><section class=clear>
          <h4>'.$title.' '.$allitems.' '.$value[0]['ALL'].' '.$list.'</h4>
          <div id="table3" class="graphcs">
            <canvas></canvas>
            <table class="hidden">
              <thead>
                <tr>
                <th>'.$headtitle.'</th>'.
                  /* <th>$headtitle</th>
                  <th>แจ้งซ่อม</th>'.            
                /*<th>กำลังดำเนินการ</th>
                  '<th>รออะไหล่</th>'.
                  /*<th>ซ่อมสำเร็จ</th>
                  <th>ซ่อมไม่สำเร็จ</th>
                  <th>ยกเลิกการซ่อม</th>
                  <th>ส่งมอบเรียบร้อย</th>
                  <th>ส่งอนุมัติซ่อม/สั่งซื้อ</th>
                  <th>อนุมัติ</th>
                  <th>ไม่อนุมัติ</th>*/
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
                  <th> '.$bodytitle.'</th>
                  <td>'.$value[0]['1'].'</td>'.
                  /*<td>'.$value[0]['2'].'</td>*/
                '<td>'.$value[0]['3'].'</td>'.
                /* <td>'.$value[0]['4'].'</td>*/
                '<td>'.$value[0]['5'].'</td>
                  <td>'.$value[0]['6'].'</td>
                  <td>'.$value[0]['7'].'</td>
                  <td>'.$value[0]['8'].'</td>
                  <td>'.$value[0]['9'].'</td>
                  <td>'.$value[0]['10'].'</td>
                </tr>
              </tbody>
            </table>
          </div>
          <script>
            new GGraphs("table3", {
              type: "pie",
                  centerX: 50 + Math.round($G("table3").getHeight() / 2),
                  labelOffset: 35,
                  centerOffset: 30,
                  strokeColor: null,
                  colors: [
                    "#660000",'.
                    /*"#120eeb",*/
                    '"#d940ff",
                    "#E65100",'.
                    /*"#06d628",*/
                    '"#FF992A",
                    "#06d628",
                    "#304FFE",
                    "#1B5E20",
                    "#263238",
                  ]
            });
          </script>
        </section>';
        $card->set(\Kotchasan\Password::uniqid(), $content3); 
    }
    public static function renderCard4($card, $value ,$title,$headtitle,$bodytitle,$list) 
    {
       $content4 = '<br><section class=clear>
                <h4>'.$title.' '.$list.'</h4>
                <div id="table4" class="graphcs">
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
                        <td>'.$value[0]['2'].'</td>
                        <td>'.$value[0]['3'].'</td>
                        <td>'.$value[0]['4'].'</td>
                        <td>'.$value[0]['5'].'</td>
                    </tbody>
                  </table>
                </div>
                <script>
                  new GGraphs("table4", {
                    type: "pie",
                        centerX: 50 + Math.round($G("table4").getHeight() / 2),
                        labelOffset: 35,
                        centerOffset: 30,
                        strokeColor: null,
                        colors: [
                          "#660000",
                          "#d940ff",
                          "#E65100",
                        ]
                  });
                </script>
              </section>';
        $card->set(\Kotchasan\Password::uniqid(), $content4); 
    }
    public static function renderCard5($card, $value ,$title,$headtitle,$bodytitle,$list) 
    { 

      $result = array();
      $str = '';
      foreach(\repair\Home\Model::createCategory('type_id') as $value_name){
            for($i=1;$i<30;$i++){
                if($value_name[$i] <> null ){
                  $result[0][$i] = $value_name[$i];
                  $str =  $str.'<th>'. $result[0][$i].'</th>';
                }
            } 
      } 
      $str_2 = '';
            for($i=0;$i<30;$i++){
                if($value[$i] <> null){
                  $str_2 =  $str_2.'<td>'. $value[$i]['count'].'</td>';
                }
            } 

            $content5 = '<br><section class=clear>
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
                    <th> '.$bodytitle.'</th>'. $str_2
                .'</tr> 
                </tbody>
              </table>
            </div>
            <script>
              new GGraphs("table5", {
                type: "pie",
                    centerX: 50 + Math.round($G("table5").getHeight() / 2),
                    labelOffset: 35,
                    centerOffset: 30,
                    strokeColor: null,
                    colors: [
                      "#660000",
                      "#d940ff",
                      "#E65100",
                      "#FF992A",
                      "#06d628",
                      "#304FFE",
                    ]
              });
            </script>
          </section>';
        $card->set(\Kotchasan\Password::uniqid(), $content5); 
    }
    public static function renderCard55($card, $value ,$title,$headtitle,$bodytitle,$list) 
    { 
                $result = array();
                $str = ''; $str_2 = '';
                foreach(\repair\Home\Model::createCategory('type_id') as $value_name){
                      for($i=1;$i<30;$i++){
                          if($value_name[$i] <> null ){
                            $result[0][$i] = $value_name[$i];
                            $str =  $str.'<th>'. $result[0][$i].'</th>';
                          }
                      } 
                } 
        if( $value[0]['count'] !=0)  {       
    
                for($i=0;$i<30;$i++){
                    if($value[$i] <> null){
                      $str_2 =  $str_2.'<td>'. $value[$i]['count'].'</td>';
                    }
                } 
         }else{  $str_2 = '<td> 0 </td>';    }

            $content55 = '<br><section class=clear>
            <h4>'.$title.' '.$list.'</h4>
            <div id="table55" class="graphcs">
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
              new GGraphs("table55", {
                type: "pie",
                    centerX: 50 + Math.round($G("table55").getHeight() / 2),
                    labelOffset: 35,
                    centerOffset: 30,
                    strokeColor: null,
                    colors: [
                      "#660000",
                      "#d940ff",
                      "#E65100",
                      "#FF992A",
                      "#06d628",
                      "#304FFE",
                    ]
              });
            </script>
          </section>';


        $card->set(\Kotchasan\Password::uniqid(), $content55); 
    }

       /**
     * ฟังก์ชั่นสร้าง เมนูด่วน ในหน้า Home
     *
     * @param Collection $menu
     * @param string     $icon
     * @param string     $title
     * @param string     $url
     * @param string     $target
     */
    public static function renderQuickMenu($menu, $icon, $title, $url, $target = '')
    {
        $menu->set($title, '<a class="cuttext" href="'.$url.'"'.(empty($target) ? '' : ' target="'.$target.'"').'><span class="'.$icon.'">'.$title.'</span></a>');
    }
}
