<?php
/**
 * 企业信息模型
 */
class AboutModel extends CommonModel {

	

    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
        array('AboutNameCH', 'require', '请输入标题'),
        //array('ContentCH', 'require', '请输入内容'),        
       
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
    ); 
  

    public function formatAddtime() {
        return date("Y-m-d H:i:s");
    }

    public function formatStatus($str){
        $str = empty($str) ? 0 : $str ;
        return $str;
    }

    /**
     * 取得欄目數據，并緩存         
     * @return array
     */
    public function getCateList($public=0,$lang='CH'){ 
        if(S('About_list')){
            $cates = S('About_list');
        }else{
            $data = $this->field('ContentCH,ContentEN',true)->order('orders ASC, ID ASC')->select();
            foreach ($data as $r) {
                $cates[$r['ID']] = $r;
            }    
        }
        
        S('About_list',$cates);
        if($public){
            foreach ($cates as $key => $r) {
                if($r['ViewFlag'.$lang]==1){
                    $publicCates[$key] = $r;    
                }
            }            
            return $publicCates;
        }else{
            return $cates;        
        }
        
    }

    public function clearCateCache(){
        S('About_list',NULL);
    }


}