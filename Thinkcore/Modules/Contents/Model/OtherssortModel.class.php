<?php
/**
 * 其他分类模型
 */
class OtherssortModel extends CommonModel {

	

    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
        array('SortNameCH', 'require', '请输入标题'),
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
        if(S('Otherssort_list')){
            $cates = S('Otherssort_list');
        }else{
            $data = $this->select();
            foreach ($data as $r) {
                $cates[$r['ID']] = $r;
            }    
        }
        
        S('Otherssort_list',$cates);
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
        S('Otherssort_list',NULL);
    }


    /**
     * 取得对应频道的树式目录
     * @return array
     */
    public function getChannelTree($catid=0,$public=0,$lang='CH'){ 
        $channelTreeKey = 'OthersChannel_'.$catid;
        if(S($channelTreeKey)){
            $cates = S($channelTreeKey);
        }else{
            $data = $this->getCateList();
            foreach ($data as $r) {
                $r['parentid'] = $r['ParentID'];
                $cates[] = $r;
            }    
        }        
        $tree = new Tree();
        $tree->init($cates);
        $datas = $tree->get_tree_array($catid,'ID');
        S($channelTreeKey,$datas,3600);
        return $datas;
        // if($public){
        //     foreach ($cates as $key => $r) {
        //         if($r['ViewFlag'.$lang]==1){
        //             $publicCates[$key] = $r;    
        //         }
        //     }            
        //     return $publicCates;
        // }else{
        //     return $cates;        
        // }        
    }



}