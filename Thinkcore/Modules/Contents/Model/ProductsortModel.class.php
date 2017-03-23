<?php

/**
 * 產品分类管理模型
 */
class ProductsortModel extends CommonModel {
	//当前模型id


	protected $_map = array(
        'id' =>'ID',        
    );

    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
       
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(       
       
       array('linktype', 1,1),
       
    ); 
   




   /**
     * 取得所有分类
     * @return array
     */
    public function getListArray($unCache=0) {        
        $cacheName = 'Productsort';
        $web_productsort_array = F($cacheName);
        if(!$web_productsort_array||$unCache){
            $result = $this->field('SortDescCH,SortDescEN,SortPic',true)->order(array("orders" => "ASC","ID"=>"ASC"))->select();
            if($result){
                F($cacheName,$result);
            }            
        }else{
            $result = $web_productsort_array ;
        }
        return  $result;
    }

   

    //刪緩存
    public function deleteListCache($rebuild=0){
        $this->categorys = NULL;        
        $cacheName = 'Productsort';
        $status_1 = F($cacheName,NULL);
        $cacheName = 'Productsort_keyID';
        $status_2 =F($cacheName,NULL); 
        F('Productsort_tree_CH',NULL); 
        F('Productsort_tree_EN',NULL);         
        return $rebuild ? $this->getProductsortIds(1) : "";
    }


    /**
     * 获取父栏目ID列表
     * @param integer $id 栏目ID
     * @param array $arrparentid 父目录ID
     * @param integer $n 查找的层次
     */
    public function buildSortPath($id, $sortpath = '', $n = 1) {
        if (empty($this->categorys)) {
            $this->categorys =  $this->getProductsortIds();
        }

        if ($n > 10 || !is_array($this->categorys) ) {
            return false;
        }
        //获取当前栏目的上级栏目ID
        $parentid = $this->categorys[$id]['ParentID'];
        $parentid = $parentid ? $parentid : $this->where(array('ID'=>$id))->getField('ParentID');
        //所有父ID
        $sortpath =  $sortpath==''?  $parentid.','.$id.',' : $parentid . ',' . $sortpath ;
        if ($parentid) {
            $sortpath = $this->buildSortPath($parentid, $sortpath, ++$n);
        } else {
            $this->categorys[$id]['SortPath'] = $sortpath;
        }
        return $sortpath;
    }


    /**
     * 获取子栏目ID列表
     * @staticvar type $categorys 静态变量 栏目数据
     * @param integer $id 栏目id
     * @return string 返回栏目子列表，以逗号隔开
     */
    public function getArrchildid($id,$isPublic=0) {
        if (!$this->categorys) {
            $this->categorys =  $this->getProductsortIds();
        }

        
        $arrchildid = $id;
        if (is_array($this->categorys)) {
            foreach ($this->categorys as $key => $cat) {
                if ($cat['ParentID'] && $key != $id && $cat['ParentID'] == $id) {
                    if($isPublic && in_array($isPublic,array('CH','EN'))){                        
                        if($cat['ViewFlag'.$isPublic]){
                            $arrchildid .= ',' . $this->getArrchildid($key,$isPublic); 
                        }
                    }else{
                        $arrchildid .= ',' . $this->getArrchildid($key);    
                    }
                }
            }
        }
        return $arrchildid;
    }


    //刷新栏目索引缓存
    public function getProductsortIds($unCache=0) {        
        $cacheName = 'Productsort_keyID';
        if(F($cacheName) && !$unCache){
            return F($cacheName);
        }else{
            $data = $this->getListArray($unCache);
            $CategoryIds = array();
            foreach ($data as $r) {
                $CategoryIds[$r['ID']] = $r ; 
            }
            F($cacheName, $CategoryIds);
            return $CategoryIds;
        }
        
    } 

    /**
     * 取得所有分类
     * @return array
     */
    public function getPublicListArray($lang='CH',$idkey=0) { 
        if(in_array($lang,array('CH','EN'))){
            $datas  = $idkey ? $this->getProductsortIds() : $this->getListArray();   
            $publicArray =  array();
            foreach ($datas as $key => $value) {
                if($value['ViewFlag'.$lang]!=1){
                    unset($datas[$key]);                    
                }                
            }
            if($idkey){
                $publicArray = $datas;
            }else{
                foreach ($datas as $key => $value) {
                    $publicArray[] = $value;
                }
            }
            return  $publicArray;
        }else{
            return false;            
        }       
    }

    /**
     * 以树结构 取得所有分类
     * @return array
     */
    public function getPublicCateTree($lang='CH'){
        $do = 0 ;
        if(in_array($lang,array('CH','EN'))){
            $do = 1 ;
            $cacheKey = 'Productsort_tree_'.$lang;
            if(F($cacheKey)){                
                return F($cacheKey);
            }
        }else{
            return false;            
        }
        $datas  = $this->getPublicListArray();
        if(empty($datas)){            
            return false;
        }
        $tree = new Tree();
        $tree->init($datas);
        $catTree = $tree->get_tree_array(0,'ID');
        F($cacheKey,$catTree);
    }

    /**
     * 取得栏目信息并缓存
     * @return array
     */
    public function getCateInfo($catid){
        $cacheKey = 'Productsort_catid_'.$catid;
        if(S($cacheKey)){
            return S($cacheKey);
        }
        $datas = $this->where(array('ID'=>$catid))->find();
        S($cacheKey,$datas,3600);
        return $datas;

    }

    /**
     * 取得大分类信息
     * @return array
     */
    public function getBigClass($catedata){
        $cates = $this -> getProductsortIds();
        if(is_array($catedata)){
            $catInfo = $catedata;
        }elseif(is_numeric($catedata)){
            $catInfo = $cates[$catedata];    
        }else{
            return false;
        }

        if(!$catInfo){return false;}
        if(isset($catInfo['ParentID']) && $catInfo['ParentID']>0){
            $sortpath = $catInfo['SortPath'];
            $sortpathArray = explode(',', $sortpath);
            $bigClassID = $sortpathArray[1];        
            return $cates[$bigClassID];    
        }else{
            return $catInfo;
        }
        
    }



}

