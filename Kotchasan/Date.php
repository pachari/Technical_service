<?php
/**
 * @filesource Kotchasan/Date.php
 *
 
 */

namespace Kotchasan;

/**
 * คลาสจัดการเกี่ยวกับวันที่และเวลา
 
 */
class Date
{
    /**
     * @var mixed
     */
    private static $lang;

    /**
     * class constructer
     */
    public function __construct()
    {
        self::$lang = Language::getItems(array(
            'DATE_SHORT',
            'DATE_LONG',
            'MONTH_SHORT',
            'MONTH_LONG',
            'YEAR_OFFSET',
        ));
    }

    /**
     * ฟังก์ชั่น คำนวนความแตกต่างของวัน (เช่น อายุ)
     * คืนค่า จำนวนวัน(ติดลบได้) ปี เดือน วัน [days, year, month, day] ที่แตกต่าง
     *
     * @assert (mktime(0, 0, 0, 2, 1, 2016), mktime(0, 0, 0, 3, 1, 2016)) [==]  array('days' => 29, 'year' => 0,'month' => 1, 'day' => 0)
     * @assert ('2016-3-1', '2016-2-1') [==]  array('days' => -29, 'year' => 0,'month' => 1, 'day' => 0)
     *
     * @param string|int  $begin_date วันที่เริ่มต้นหรือวันเกิด (Unix timestamp หรือ วันที่ รูปแบบ YYYY-m-d)
     * @param istring|int $end_date   วันที่สิ้นสุดหรือวันนี้ (Unix timestamp หรือ วันที่ รูปแบบ YYYY-m-d)
     *
     * @return array
     */
    public static function compare($begin_date, $end_date)
    {
        if (is_string($begin_date) && preg_match('/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})(\s([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?/', $begin_date, $match)) {
            $begin_date = mktime(0, 0, 0, (int) $match[2], (int) $match[3], (int) $match[1]);
        }
        if (is_string($end_date) && preg_match('/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})(\s([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?/', $end_date, $match)) {
            $end_date = mktime(0, 0, 0, (int) $match[2], (int) $match[3], (int) $match[1]);
        }
        if ($end_date == $begin_date) {
            // เท่ากัน
            return array(
                'days' => 0,
                'year' => 0,
                'month' => 0,
                'day' => 0,
            );
        } else {
            // จำนวนวันที่แตกต่าง
            $days = floor(($end_date - $begin_date) / 86400);
            if ($end_date < $begin_date) {
                $tmp = $begin_date;
                $begin_date = $end_date;
                $end_date = $tmp;
            }
        }
        $Year1 = (int) date('Y', $begin_date);
        $Month1 = (int) date('m', $begin_date);
        $Day1 = (int) date('d', $begin_date);
        $Year2 = (int) date('Y', $end_date);
        $Month2 = (int) date('m', $end_date);
        $Day2 = (int) date('d', $end_date);
        // วันแต่ละเดือน
        $months = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        // ปีอธิกสุรทิน
        if (($Year2 % 4) == 0) {
            $months[2] = 29;
        }
        // ปีอธิกสุรทิน
        if ((($Year2 % 100) == 0) & (($Year2 % 400) != 0)) {
            $months[2] = 28;
        }
        if (abs($days) < $months[$Month1]) {
            // ไม่เกิน 1 เดือน
            return array(
                'days' => $days,
                'year' => 0,
                'month' => 0,
                'day' => abs($days),
            );
        } else {
            // ห่างกันเกิน 1 เดือน
            $YearDiff = $Year2 - $Year1;
            if ($Month2 >= $Month1) {
                $MonthDiff = $Month2 - $Month1;
            } else {
                --$YearDiff;
                $MonthDiff = 12 + $Month2 - $Month1;
            }
            if ($Day1 > $months[$Month2]) {
                $Day1 = 0;
            } elseif ($Day1 > $Day2) {
                $Month2 = $Month2 == 1 ? 13 : $Month2;
                $Day2 += $months[$Month2 - 1];
                --$MonthDiff;
            }
            return array(
                'days' => $days,
                'year' => $YearDiff,
                'month' => $MonthDiff,
                'day' => $Day2 - $Day1,
            );
        }
    }
    /**
     * คืนค่าวันเวลาที่แตกต่าง โดยตัดช่วงเวลาทำงาน ตัดเริ่ม 08:00 ถึง 17:00 และตัดเวลาพัก 12:00-13:00  format "yyyy-mm-dd hh:ii:ss"
     *
     * @assert ("2021-08-16 17:36:36", "2021-08-16 12:30:00") [==] "2021-08-16 17:00:00","2021-08-16 13:00:00"
     *
     * @param  $firstTime
     * @param  $lastTime
     *
     * @return int
     */
    public static function DATEDiff($first, $last)
    {
          
        $firstdate = date("Y-m-d",strtotime($first));
        $lastdate = date("Y-m-d",strtotime($last));

        $starttime = intval(str_replace(array(':'),'',date("H:i:s",strtotime($first)))) ;
        $endtime =intval(str_replace(array(':'),'',date("H:i:s",strtotime($last)))) ;

        $a = intval(str_replace(array('-',' ',':'),'', $first));
        $b = intval(str_replace(array('-',' ',':'),'',$last));

        $a_datetime = intval(str_replace(array('-',' ',':'),'', $firstdate));
        $b_endtime = intval(str_replace(array('-',' ',':'),'',  $lastdate));

        //เทียบค่าวันเวลาเป็น int
        if( $a < $b ){
            //เช็ควันเริ่มและวันจบเป็น int และ เวลาเริ่มและเวลาจบเป็น int ต้องเท่ากัน
            if( $a_datetime ==  $b_endtime && $starttime == $endtime){
                $result = DATE::_date_diff_same($first,$last); ("1");
            }else if( $a_datetime ==  $b_endtime){
               
                    $S_date = DATE::_timedate($first);
                    $E_date = DATE::_timedate($last);

                    if($starttime  > intval('170000') && $endtime  > intval('170000')){
                      $result = DATE::_date_diff_same($S_date, $E_date); 
                    }else{
                        $result = DATE::_date_diff_same( $first,$last);    
                    }
            }else if($starttime == $endtime){
                $result = DATE::_date_diff_same($first,$last); 
                
            }else{
                $S_date = DATE::_timedate($first);
                $E_date = DATE::_timedate($last);
               if(intval(str_replace(array('-',' ',':'),'', $S_date)) == intval(str_replace(array('-',' ',':'),'', $E_date))){
                    $result = DATE::_date_diff_same($S_date,$E_date); 
                }else{
                    $result = DATE::_date_diff_time($first,$last,$S_date,$E_date); 
                }   
            }

        }else{
            $result = DATE::_date_diff_same($first,$last); 
        }
           $s = DATE::_date_diff($first,$last); 
           return DATE::_date_normalize($s,$result);  
    }
    //หาเวลา
    public static function _timedate($date){

        $firstdate = date("Y-m-d",strtotime($date));
        $starttime = intval(str_replace(array(':'),'',date("H:i:s",strtotime($date)))) ;
        $S_date = '';
        if( $starttime < intval('080000') ){$S_date = $firstdate.' 08:00:00'; 
        }else if(  $starttime < intval('120000') || $starttime  < intval('130000')){$S_date = $firstdate.' 12:00:00'; 
        }else if($starttime  > intval('130000') && $starttime < intval('170000')){$S_date = $firstdate.' 13:00:00';
        }else if($starttime  > intval('170000') ){$S_date = $firstdate.' 17:00:00';  } 
       
        return $S_date;
    }

   // วันและเวลาแปลงเป็น int แล้วเท่ากัน
    public static function _date_diff_same($one, $two)
    {
        
        $s = DATE::_date_diff($one,$two);
        $result = array();

        IF(DATE::getWorkdays($one,$two) >= 1 )$result["d"] = $s["d"];
        else{$result["d"] = DATE::getWorkdays($one,$two);}

        $result["y"] = $s["y"];
        $result["m"] = $s["m"];
        
        $result["h"] = $s["h"];
        $result["i"] = $s["i"];
        $result["s"] = $s["s"];
        return  $result;
    }
    // วันและเวลาแปลงเป็น int แล้วเท่ากัน
    public static function _date_diff_time($one, $two,$tree,$four)
    {
        $stime = DATE::_date_diff($one,$tree);
        $etime = DATE::_date_diff($two,$four);
        $s = DATE::_date_diff($one,$two);

        $result = array();

        IF(DATE::getWorkdays($one,$two) == 1 )$result["d"] = $s["d"];
        else{$result["d"] = DATE::getWorkdays($one,$two);}

        $result["y"] = $s["y"];
        $result["m"] = $s["m"];
       // $result["d"] = $s["d"];
        $result["h"] = $etime["h"] - $stime["h"];
        $result["i"] = $etime["i"] - $stime["i"];
        $result["s"] = $etime["s"] - $stime["s"];

        return  $result;
    }
    // เวลาแปลงเป็น int แล้วเท่ากัน
    public static function _date_diff_datetime($one, $two,$tree,$four)
    {
        
        $stime = DATE::_date_diff($one,$tree);
        $etime = DATE::_date_diff($two,$four);
        $s = DATE::_date_diff($one,$two);

        $result = array();

        IF(DATE::getWorkdays($one,$two) == 1 )$result["d"] = $s["d"];
        else{$result["d"] = DATE::getWorkdays($one,$two);}

        $result["y"] = $s["y"];
        $result["m"] = $s["m"];
       // $result["d"] = $s["d"];
        $result["h"] = $etime["h"] - $stime["h"];
        $result["i"] = $etime["i"] - $stime["i"];
        $result["s"] = $etime["s"] - $stime["s"];

        return  $result;
    }
    /**
     * Accepts two unix timestamps.
     */
    public static function _date_diff($one, $two)
    {
    
        $invert = false;
        if ($one > $two) {
            list($one, $two) = array($two, $one);
            $invert = true;
        }

        $a = DATE::parse($one);
        $b = DATE::parse($two);

        $result = array();
        $result["y"] = $b["y"] - $a["y"];
        $result["m"] = $b["m"] - $a["m"];
        $result["d"] = $b["d"] - $a["d"];
        $result["h"] = $b["h"] - $a["h"];
        $result["i"] = $b["i"] - $a["i"];
        $result["s"] = $b["s"] - $a["s"];
    
        if ($invert) {
            DATE::_date_normalize($a, $result);
        } else {
            DATE::_date_normalize($b, $result);
        }

        return $result;
    }
    public static function _date_normalize($base, $result)
    {
        $result = DATE::_date_range_limit(0, 60, 60, "s", "i", $result);
        $result = DATE::_date_range_limit(0, 60, 60, "i", "h", $result);
        $result = DATE::_date_range_limit(0, 24, 24, "h", "d", $result);
        $result = DATE::_date_range_limit(0, 12, 12, "m", "y", $result);

        $result = DATE::_date_range_limit_days($base, $result);

        $result = DATE::_date_range_limit(0, 12, 12, "m", "y", $result);
        
        return $result;
    }
    public static function _date_range_limit($start, $end, $adj, $a, $b, $result)
    {
        if ($result[$a] < $start) {
            $result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
            $result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
        }

        if ($result[$a] >= $end) {
            $result[$b] += intval($result[$a] / $adj);
            $result[$a] -= $adj * intval($result[$a] / $adj);
        }

        return $result;
    }

    public static function _date_range_limit_days($base, $result)
    {
        $days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        DATE::_date_range_limit(1, 13, 12, "m", "y", $base);

        $year = $base["y"];
        $month = $base["m"];

        if (!empty($result["invert"])) {
            while ($result["d"] < 0) {
                $month--;
                if ($month < 1) {
                    $month += 12;
                    $year--;
                }

                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;
            }
        } else {
            while ($result["d"] < 0) {
                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;

                $month++;
                if ($month > 12) {
                    $month -= 12;
                    $year++;
                }
            }
        }

        return $result;
    }
    /**
     * Count the number of working days between two dates.
     *
     * This function calculate the number of working days between two given dates,
     * taking account of the Public festivities, Easter and Easter Morning days,
     * the day of the Patron Saint (if any) and the working Saturday.
     *
     * @param   string  $date1    Start date ('YYYY-MM-DD' format)
     * @param   string  $date2    Ending date ('YYYY-MM-DD' format)
     * @param   boolean $workSat  TRUE if Saturday is a working day
     * @param   string  $patron   Day of the Patron Saint ('MM-DD' format)
     * @return  integer           Number of working days ('zero' on error)
     *
     * @author Massimo Simonini <massiws@gmail.com>
     */
    public static function getWorkdays($date1, $date2, $workSat = FALSE, $patron = NULL) {
        if (!defined('SATURDAY')) define('SATURDAY', 6);
        if (!defined('SUNDAY')) define('SUNDAY', 0);
    
        // Array of all public festivities
        $publicHolidays = array('01-01', '01-06', '04-25', '05-01', '06-02', '08-15', '11-01', '12-08', '12-25', '12-26');
        // The Patron day (if any) is added to public festivities
        if ($patron) {
        $publicHolidays[] = $patron;
        }
    
        /*
        * Array of all Easter Mondays in the given interval
        */
        $yearStart = date('Y', strtotime($date1));
        $yearEnd   = date('Y', strtotime($date2));
    
        for ($i = $yearStart; $i <= $yearEnd; $i++) {
        $easter = date('Y-m-d', easter_date($i));
        list($y, $m, $g) = explode("-", $easter);
        $monday = mktime(0,0,0, date($m), date($g)+1, date($y));
        $easterMondays[] = $monday;
        }
    
        $start = strtotime($date1);
        $end   = strtotime($date2);
        $workdays = 0;
        for ($i = $start; $i <= $end; $i = strtotime("+1 day", $i)) {
        $day = date("w", $i);  // 0=sun, 1=mon, ..., 6=sat
        $mmgg = date('m-d', $i);
        if ($day != SUNDAY &&
            !in_array($mmgg, $publicHolidays) &&
            !in_array($i, $easterMondays) &&
            !($day == SATURDAY && $workSat == FALSE)) {
            $workdays++;
        }
        }
    
        return intval($workdays); //intval($workdays)
    }

    /**
     * คืนค่าเวลาที่แตกต่าง หน่วย msec
     *
     * @assert ('08:00', '09:00') [==] 3600
     *
     * @param  $firstTime
     * @param  $lastTime
     *
     * @return int
     */
    public static function timeDiff($firstTime, $lastTime)
    {
        $firstTime = strtotime($firstTime);
        $lastTime = strtotime($lastTime);
        $timeDiff = $lastTime - $firstTime;
        return $timeDiff;
    }

    /**
     * แปลงตัวเลขเป็นชื่อวันตามภาษาที่ใช้งานอยู่
     * คืนค่า อาทิตย์...6 เสาร์
     *
     * @assert (0) [==] 'อา.'
     * @assert (0, false) [==] 'อาทิตย์'
     *
     * @param int  $date       0-6
     * @param bool $short_date true (default) วันที่แบบสั้น เช่น อ., false ชื่อเดือนแบบเต็ม เช่น อาทิตย์
     *
     * @return string
     */
    public static function dateName($date, $short_date = true)
    {
        // create class
        if (!isset(self::$lang)) {
            new static();
        }
        $var = $short_date ? self::$lang['DATE_SHORT'] : self::$lang['DATE_LONG'];
        return isset($var[$date]) ? $var[$date] : '';
    }

    /**
     * ฟังก์ชั่นแปลงเวลาเป็นวันที่ตามรูปแบบที่กำหนด สามารถคืนค่าวันเดือนปี พศ. ได้ ขึ้นกับไฟล์ภาษา
     * คืนค่า วันที่และเวลาตามรูปแบบที่กำหนดโดย $format
     *
     * @assert (0, 'y-m-d H:i:s') [==]  date('y-m-d H:i:s')
     * @assert (null) [==]  ''
     * @assert (1454259600, 'Y-m-d H:i:s') [==] '2559-02-01 00:00:00'
     *
     * @param int|string $time   int เวลารูปแบบ Unix timestamp, string เวลารูปแบบ Y-m-d หรือ Y-m-d H:i:s ถ้าไม่ระบุหรือระบุ หมายถึงวันนี้
     * @param string     $format รูปแบบของวันที่ที่ต้องการ (ถ้าไม่ระบุจะใช้รูปแบบที่มาจากระบบภาษา DATE_FORMAT)
     *
     * @return string
     */
    public static function format($time = 0, $format = null)
    {
        if ($time === 0) {
            $time = time();
        } elseif (is_string($time)) {
            if (preg_match('/^[0-9]+$/', $time)) {
                $time = (int) $time;
            } else {
                $time = strtotime($time);
            }
        } elseif (!is_int($time)) {
            return '';
        }
        // create class
        if (!isset(self::$lang)) {
            new static();
        }
        $format = empty($format) ? 'DATE_FORMAT' : $format;
        $format = Language::get($format);
        if (preg_match_all('/(.)/u', $format, $match)) {
            $ret = '';
            foreach ($match[0] as $item) {
                switch ($item) {
                    case ' ':
                    case ':':
                    case '/':
                    case '-':
                    case '.':
                    case ',':
                        $ret .= $item;
                        break;
                    case 'l':
                        $ret .= self::$lang['DATE_SHORT'][date('w', $time)];
                        break;
                    case 'L':
                        $ret .= self::$lang['DATE_LONG'][date('w', $time)];
                        break;
                    case 'M':
                        $ret .= self::$lang['MONTH_SHORT'][date('n', $time)];
                        break;
                    case 'F':
                        $ret .= self::$lang['MONTH_LONG'][date('n', $time)];
                        break;
                    case 'Y':
                        $ret .= (int) date('Y', $time) + (int) self::$lang['YEAR_OFFSET'];
                        break;
                    default:
                        $ret .= trim($item) == '' ? ' ' : date($item, $time);
                        break;
                }
            }
        } else {
            $ret = date($format, $time);
        }
        return $ret;
    }

    /**
     * แปลงตัวเลขเป็นชื่อเดือนตามภาษาที่ใช้งานอยู่
     * คืนค่า 1 มกราคม...12 ธันวาคม
     *
     * @assert (1) [==] 'ม.ค.'
     * @assert (1, false) [==] 'มกราคม'
     *
     * @param int  $month       1-12
     * @param bool $short_month true (default) ชื่อเดือนแบบสั้น เช่น มค., false ชื่อเดือนแบบเต็ม เช่น มกราคม
     *
     * @return string
     */
    public static function monthName($month, $short_month = true)
    {
        // create class
        if (!isset(self::$lang)) {
            new static();
        }
        $var = $short_month ? self::$lang['MONTH_SHORT'] : self::$lang['MONTH_LONG'];
        return isset($var[$month]) ? $var[$month] : '';
    }

    /**
     * แยกวันที่ออกเป็น array
     * คืนค่า array(y, m, d, h, i, s) หรือ array(y, m, d) หากเป้นวันที่อย่างเดียว หรือ false หากไม่ใช่วันที่
     *
     * @param string $date
     *
     * @return array|bool
     */
    public static function parse($date)
    {
        if (preg_match('/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})(\s([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?/', $date, $match)) {
            if (isset($match[4])) {
                return array('y' => $match[1], 'm' => $match[2], 'd' => $match[3], 'h' => $match[5], 'i' => $match[6], 's' => $match[7]);
            } else {
                return array('y' => $match[1], 'm' => $match[2], 'd' => $match[3]);
            }
        }
        return false;
    }
}
