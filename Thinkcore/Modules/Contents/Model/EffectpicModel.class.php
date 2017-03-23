<?php
/**
 * 效果图模型
 */
class EffectpicModel extends CommonModel {


    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
       array('EffectNameCH', 'require', '请输入中文名'),
       array('roomtype', 'require', '请选择分类'),
       array('ProductModel', 'require', '请填写型号'),
       array('Sizes', 'require', '请填写规格'),
       array('SmallPic', 'require', '默认小图不能为空'),
       array('BigPic', 'require', '默认大图不能为空'),
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
       array('AddTime', 'formatAddtime', 1, 'callback'),
       array('UpdateTime', 'formatAddtime', 3, 'callback'),
       array('ViewFlagCH', 'formatStatus', 2, 'callback'),
       array('ViewFlagEN', 'formatStatus', 2, 'callback'),
    ); 
    
    public $roomtype_list =  array('卫浴','厨房','商业空间','客厅','餐厅','阳台','书房','卧室','玄关','走廊',);

    public function formatAddtime() {
        return date("Y-m-d H:i:s");
    }

    public function formatStatus($str){
        $str = empty($str) ? 0 : $str ;
        return $str;
    }

    public function getRoomtypes(){
      return $this->roomtype_list;
    }

}