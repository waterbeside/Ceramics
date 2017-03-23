<?php

/**
 * 用户组模型
 */
class MemgroupModel extends CommonModel {
	//当前模型id
    public $nid = 0;

	protected $dbName = '';
	protected $tablePrefix = '';
	protected $tableName = ''; 

	

    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
       
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
   
    ); 
  


   
    //取得组列表
    public function getListArray(){

        $nid = $this->nid;
        $cacheName = 'Web_'.$nid.'_MemgroupIds';
        $web_MemgroupIds = F($cacheName);
        if(!$web_MemgroupIds){
            $datas = $this->field('SortDescCH,SortDescEN',true)->order(array("ID"=>"ASC"))->select();
            $result = array();
            if($datas){
                foreach ($datas as $r) {
                    $result[$r['ID']] = $r ; 
                }
                F($cacheName,$result);
            }            
        }else{
            $result = $web_MemgroupIds ;
        }
        return  $result;
    }

   

}

