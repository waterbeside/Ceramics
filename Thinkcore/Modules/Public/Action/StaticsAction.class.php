<?php

// +----------------------------------------------------------------------
// | 网站
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class StaticsAction extends BaseAction {

    public $lang = 'CH';
    //初始化
    protected function _initialize() {
        parent::_initialize();
        $this->lang = I('get.lang','','strtolower') == 'en' ? 'EN' : 'CH';
    }

   


    protected function parseTpl($tpl,$isCtr=0){
        $tpl = $this->lang == 'EN' ? 'EN/'.$tpl : $tpl ;
        $str = $isCtr ? $tpl : '!/'.$tpl ;
        $template = parseTemplateFile($str);        
        return $template;
    }

    //首页视频
    public function index_video() {
         
        $this->display($this->parseTpl('index_video'));
    }


    public function ft_wechat(){
        $html = "<div class=\"qr_pic\"><img src=\"/m/images/wintosqr_wechat.png\"></div>";  
        $html .= "<div class=\"qr_txt\"><b>微信公众帐号：#lazyme#陶瓷</b><br />欢迎扫描二维码并关注我们</div>"; 
        $this->show($html);

    } 

    public function fixtool(){
        $action = I('act');
       switch($action){
        case "wechat" :
            $html = "<div class=\"qr_pic\"><img src=\"/m/images/wintosqr_wechat.png\"></div>";  
            $html .= "<div class=\"qr_txt\"><b>微信公众帐号：#lazyme#陶瓷</b><br />欢迎扫描二维码并关注我们</div>";    
            echo $html;
        break;
        case "weibo" :
            $html = "<div class=\"qr_pic\"><img src=\"/m/images/wintosqr_sina.png\"></div>";    
            $html .= "<div class=\"qr_txt\">欢迎关注<b>#lazyme#陶瓷官方微博</b><br />点击<a href=\"http://weibo.com/winto100\">【这里】</a>查看我们的微博主页</div>";  
            echo $html;
        break;
        case "notice" :
            $id = I('get.id',0,'intval');
            $DB_Notice = D('Contents/Notice');
            if(!$id){
                //$notice_row    = $DB_Notice->query('SELECT * FROM  __TABLE__  where ViewFlagCH = 1 ORDER BY RAND() LIMIT 1');
                $notice_row    = M('notice')->where(array('ViewFlagCH'=>1))->order('rand()')->limit(1)->find();
            }else{
                $notice_row    = $DB_Notice->where(array('ViewFlagCH'=>1,'id'=>$id))->find();
            }
            //$linkurl = $notice_row["linkurl"]==""||   $notice_row["linkurl"]==NULL?"javascript:void(0);": $notice_row["linkurl"];
            if($notice_row){
                if($notice_row["linkurl"]==""|| $notice_row["linkurl"]==NULL){
                    $html = "<h4 style=\"text-align:center\">".$notice_row['title']."</h4>";        
                }else{
                    $html = "<h4 style=\"text-align:center\"><a href=\"".$notice_row["linkurl"]."\">".$notice_row['title']."</a></h4>"; 
                }
                $html .= "<div class=\"txt\">".$notice_row['content']."</div>";
                $id =   $notice_row["id"];
                
                $html .="<div class=\"btnbar\">";
                
                //上一个
                $prevRows =  $DB_Notice->getNext($id,-1,$this->lang);
                if($prevRows){          
                    $html .="<a href=\"javascript:JS_wtool.ntID(".$prevRows['id'].")\" class=\"prev \"><i class=\"fa fa-chevron-left\"></i> 上一条</a>";
                }
                //下一个
                $nextRows =  $DB_Notice->getNext($id,1,$this->lang);
            
                if($nextRows){          
                $html .="<a href=\"javascript:JS_wtool.ntID(".$nextRows['id'].")\" class=\"next\">下一条 <i class=\"fa fa-chevron-right\"></i></a>";
                }
                $html .="</div>";
            }else{
                $html="暂无通知";
            }
            $this->show($html);
        break;
        exit();
        
       }

    } 


}
