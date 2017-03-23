<?php

/**
 * 
 * @author Author: 紫蘇醬(Loch Kan) <454831746@qq.com>
 */
return array(
    "DEFAULT_THEME" => "Default",

    //营销中心栏目菜单
    "menu_marketing" =>  array(
        array('ID' => 51,'SortNameCH' => '总部展厅','a'=>'room'),
        array('ID' => 52,'SortNameCH' => '终端展厅','a'=>'stores'),
        array('ID' => 29,'SortNameCH' => '工程案例','a'=>'cases'),            
    ),


    //产品常规尺寸表
    "sizes_list" =>  array(
        array('1200X1200','1000X1000','900X900','800X800','600X600','500X500','400X400','330X330','300X300'),
        array('1200X600','900X600','800X400','660X240','600X330','600X300','450X300'),        
    ),

    //经销专区主菜单
    "menu_reseller" =>  array(
        array('ID' => 1,'name' => '资料下载','a'=>'lists'),
        array('ID' => 2,'name' => '用户中心','a'=>'index','children'=>array(array('name'=>'用户主页','a'=>'index'),array('name'=>'资料修改','a'=>'edit'),array('name'=>'密码修改','a'=>'editpw'))),
        array('ID' => 3,'name' => '退出','a'=>'logout'),            
    ),
    

    
 
);
