<?php

/**
 * 微信公众号模型
 */
class WechatModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('wid', 'require', '英文名不能为空'),
        array('wid', "/^[A-Za-z0-9\_]*$/", '英文名格式不正确',1, 'regex', 3),
        array('wid', '', '英文名必须唯一', 0, 'unique', 1),
        //array('nid', 'require', 'nid不可为空'),
        array('name', 'require', '名称不可为空'),
        array('appid', 'require', 'appid不可为空'),
        array('appsecret', 'require', 'appsecret不能为空'),
        array('token', 'require', 'token不能为空'),        
        
        
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('listorder', 0,1),
        array('wid','strtoupper',3,'function') , 
        array('nid','strtoupper',3,'function') , 
        array('setting','array2string',3,'function') ,         
    );
 




    public function getWechatWids() {
        if(F('Wechats')){
            return F('Wechats');
        }
        $datas = $this->order(array("listorder" => "asc","id"=>"asc"))->select();
        $datas_n = array();
        foreach ($datas as $key => $value) {
            $setting = string2array($value['setting']);
            $datas_n[$value['wid']] = $value;
            $datas_n[$value['wid']]['setting'] = $setting;
        }
        F('Wechats',$datas_n);
        return $datas_n;
    }


   public function getWechatInfo($wid){
       $wechats = $this->getWechatWids();
       return $wechats[$wid];
   }
     

}