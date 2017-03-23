<?php
/**
 * 效果图模型
 */
class NewsModel extends CommonModel {

	

    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
        array('NewsNameCH', 'require', '请输入标题'),
        //array('ContentCH', 'require', '请输入内容'),
        array('SortID', 'require', '请选择分类'),
        
        array('BigPic', 'require', '默认大图不能为空'),
       
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
       array('AddTime', 'formatAddtime', 1, 'callback'),
       array('UpdateTime', 'formatAddtime', 3, 'callback'),
       array('ViewFlagCH', 'formatStatus', 2, 'callback'),
       array('ViewFlagEN', 'formatStatus', 2, 'callback'),
    ); 
  

    public function formatAddtime() {
        return date("Y-m-d H:i:s");
    }

    public function formatStatus($str){
        $str = empty($str) ? 0 : $str ;
        return $str;
    }

}