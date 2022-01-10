<?php
/* database.php */
return array (
  'mysql' => 
          array (
            'dbdriver' => 'mysql',
            'username' => 'root',
            'password' => '', //31M@ssw0rd
            'dbname' => 'all_db_it', //it_management
            'prefix' => 'pit',
            'hostname' => 'localhost',
          ),
        /* ค่ากำหนดการเชื่อมต่อ ชุดที่ 2 */
    /*      'connection2' => array(
            'dbdriver' => 'mysql',
            'username' => 'root',
            'password' => '31M@ssw0rd',
            'dbname' => 'booking',
            'prefix' => 'booking',
            'hostname' => 'localhost',
            'port' => 3306,
            'prefix' => 'prefix2'
        ),*/
        'tables' => 
        array (
          'line' => 'line',
          'user' => 'user',
          'category' => 'technical_category',
          'language' => 'technical_language',
          'repair' => 'technical_repair',
          'repair_status' => 'technical_repair_status',
          'inventory' => 'technical_inventory',
          'inventory_meta' => 'technical_inventory_meta',
          'inventory_items' => 'technical_inventory_items',
          'contact' => 'technical_contact',
          'customer' => 'technical_customer',
          'customer_type' => 'technical_customer_type',
          'country' => 'technical_country',
          'amphur' => 'technical_amphur',
          'district' => 'technical_district',
          'province' => 'technical_province',
          'number' => 'technical_number',
         
        ),
  
    
);