<?php
/* config.php */
return array (
  'version' => '1.0.3',
  'web_title' => 'TKS - Technical Service (ใบขอเทคนิคบริการ)',
  'web_description' => 'ระบบบันทึกข้อมูลใบขอเทคนิคบริการ',
  'timezone' => 'Asia/Bangkok',
  'type_job_number' => 
  array (
    0 => 'ID',
    1 => 'HW',
    2 => 'OEM',
    3 => 'PJ',
  ),
  'member_status' => 
  array (
    0 => 'IT Support',
    1 => 'ผู้ดูแลระบบ (IT)',
    2 => 'บุคคล ธุรการ',
    3 => 'บัญชี การเงิน',
    4 => 'จัดซื้อ',
    5 => 'ซ่อมบำรุง',
    6 => 'วางแผนการตลาด',
    7 => 'คลังวัตถุดิบ',
    8 => 'คลังสินค้าฯจัดส่ง',
    9 => 'ขายฮาร์ดแวร์',
    10 => 'ขายอุตสาหกรรม',
    11 => 'เทคนิคQC Lab3',
    12 => 'ทคนิคQC Lab6',
    13 => 'ส่วนผลิต',
    14 => 'ผลิต บรรจุ G1',
    15 => 'ผลิต G2-4',
    16 => 'บรรจุ G2-4',
    17 => 'ผลิต บรรจุ G5-6',
    18 => 'บริหาร',
  ),
  'color_status' => 
  array (
    0 => '#259B24',
    1 => '#FF0000',
    2 => '#0E0EDA',
    3 => '#4A148C',
    4 => '#880E4F',
    5 => '#FFED7E',
    6 => '#FF992A',
    7 => '#8DD092',
    8 => '#85BFFF',
    9 => '#FF86C7',
    10 => '#7D67DE',
    11 => '#311B92',
    12 => '#FF8B8B',
    13 => '#01579B',
    14 => '#A8A8A8',
    15 => '#B2BBFF',
    16 => '#5741B8',
    17 => '#880E4F',
    18 => '#B71C1C',
  ),
  'default_icon' => 'icon-tools',
  'inventory_w' => 600,
  'repair_first_status' => 8,
  'repair_job_no' => 'TKS/%03d/%D/%M/%Y',
  'OEM' => 'TKS/OEM%03d/%D/%M/%Y',
  'ID' => 'TKS/ID%03d/%D/%M/%Y',
  'HW' => 'TKS/HW%03d/%D/%M/%Y',
  'PJ' => 'TKS/PJ%03d/%D/%M/%Y',
  'password_key' => '6108a7198e99c',
  'user_forgot' => 0,
  'user_register' => 0,
  'welcome_email' => 0,
  'member_only' => 1,
  'demo_mode' => 0,
  'login_fields' => 
  array (
    0 => 'username',
  ),
  'facebook_appId' => '',
  'google_client_id' => '',
  'bg_color' => '#263238',
  'color' => '#FFFFFF',
  'line_api_key' => '6H2mzBjpMXAZNM2O7uzIwiEmAHmdYM3LmVnEubVN58B',
  'api_url' => 'http://localhost/inventory-main/api.php',
  'api_token' => 'RQVFlC5fO1mRxl0lvam35VuGjkJtqQU1UmrDAfzAKTs',
  'api_secret' => 'fb01728df6564',
  'api_ips' => 
  array (
  ),
  'modules' => 
  array (
    'inventory' => 1,
    'repair' => 1,
  ),
  'noreply_email' => '',
  'email_charset' => 'utf-8',
  'email_Host' => 'localhost',
  'email_Port' => 25,
  'email_SMTPSecure' => '',
  'email_Username' => '',
  'email_use_phpMailer' => 1,
  'email_SMTPAuth' => 1,
  'email_Password' => '',
);