<?php
/**
 * 新闻模型
 */
class NewsModel extends CommonModel {

	

    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
        array('NewsNameCH', 'require', '请输入标题'),
        //array('ContentCH', 'require', '请输入内容'),
        array('SortID', 'require', '请选择分类'),
        
        
       
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
       array('AddTime', 'formatAddtime', 1, 'callback'),
       array('UpdateTime', 'formatAddtime', 3, 'callback'),
       array('ViewFlagCH', 'formatStatus', 1, 'callback'),
       array('ViewFlagEN', 'formatStatus', 1, 'callback'),
       array('GroupID', '20051131014553209',1),
       array('Exclusive', '>=',1),
    ); 
  

    public function formatAddtime() {
        return date("Y-m-d H:i:s");
    }

    public function formatStatus($str){
        $str = empty($str) ? 0 : $str ;
        return $str;
    }


    public function getNext($id=0,$isNext=1,$catid=0,$lang=''){ 
        if(!$id){
            return false;
        }
        $order = 'ID desc';
        if($isNext>0){
            $map['ID'] = array('gt',$id);
            $order = 'ID ASC';
        }
        if($isNext<0){
            $map['ID'] = array('lt',$id);
        }
        if(is_numeric($catid) && $catid>0){
            $map['SortID'] = $catid;   
        }
        if(in_array($lang, array('CH','EN'))){
            $map['ViewFlag'.$lang] = 1;
        }
        $datas=$this->field('NewsNameCH,NewsNameEN,ID,SortID')->where($map)->order($order)->limit('1')->find();           
        return $datas;
    }

}