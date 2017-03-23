<?php

/**
 * 產品管理模型
 */
class ProductsModel extends CommonModel {


    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
        array('ProductNameCH', 'require', '请输入中文名'),
        array('SortID', 'require', '请选择分类'),
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
       array('NewFlag', 'formatStatus', 2, 'callback'),
       array('CommendFlag', 'formatStatus', 2, 'callback'),

       array('ProductNo', 'formatProductNo',1,'callback'),
       array('ClickNumber', 0,1),
       array('N_Price', 0,1),
       array('P_Price', 0,1),
       array('Stock', 10000,1),
       array('UnitCH', '块',1),
       array('MakerCH', '广东#lazyme#陶瓷有限公司',1),
       array('MakerEN', 'Winto Ceramics Group',1),       
    ); 
  
    public function formatAddtime() {
        return date("Y-m-d H:i:s");
    }

    public function formatProductNo() {
        $now = date('Y-m-d H:i:s',time());
        $nowArr1 = explode(" ",$now);
        $nowArr2 = explode(":",$nowArr1[1]);
        $ProductNo=$nowArr2[0].$nowArr2[1].$nowArr2[2]."-".rand(100,900);
        return  $ProductNo;
    }

    public function formatStatus($str){
        $str = empty($str) ? 0 : $str ;
        return $str;
    }

    //解析 一套产品所含之破列表
    public function parseItems($data){
        if(!$data['ProductModel']){
            return false;
        }
        $pro_models= explode(',', $data['ProductModel']) ;
        $pro_sizes= explode(',', $data['Sizes']);
        $pro_thumb= explode(',', $data['SizeSmallpic']);
        $pro_pic= explode(',', $data['SizeBigpic']);

    }

   
     //替换，尺寸排大小，$reSizeType: 1大在前，-1小在前，0不变,2反转
    public function reSize($sizeStr,$delimiter='X',$reSizeType=0){
        if(strpos($sizeStr,$delimiter)==-1){
            return $sizeStr;
        }else{
            $strArr = explode($delimiter,$sizeStr);
            $strArr[0]=intval($strArr[0]);
            $strArr[1]=intval($strArr[1]);
            if($reSizeType=="1"){
                if($strArr[0]-$strArr[1]<0){
                    $sizeStr =  $strArr[1].$delimiter.$strArr[0];
                }else{
                    $sizeStr =  $sizeStr;
                }
            }elseif($reSizeType=="-1"){
                if($strArr[0]-$strArr[1]>0){
                    $sizeStr =  $strArr[1].$delimiter.$strArr[0];
                }else{
                    $sizeStr =  $sizeStr;
                }
            }elseif($reSizeType=="2"){
                $sizeStr =  $strArr[1].$delimiter.$strArr[0];
            }else{
                $sizeStr =  $sizeStr;
            }
            return $sizeStr ;
        }
    }


    //取得列表数据
    public function getListsPage($putInDatas){
        $filter = $putInDatas['filter'];
        if(!$filter){return false;}

        $putInDatas['page'] = isset($putInDatas['page']) && intval($putInDatas['page']) > 0 ?   intval($putInDatas['page']) : 1;
        $putInDatas['LANG'] = isset($putInDatas['LANG']) && in_array($putInDatas['LANG'], array('CH','EN')) ? $putInDatas['LANG'] :'CH';
        $putInDatas['pageSize'] = isset($putInDatas['pageSize']) && $putInDatas['pageSize'] > 0 ? $putInDatas['pageSize'] : 24 ;


        $cacheID = 'productlist_'.to_guid_string($putInDatas);


        if ($return = S($cacheID)) {
            return $return ;
           
           
        }else{
            $SortID         = $filter['catid']   ;  
            $LANG           = $putInDatas['LANG'];
            $pageSize       = $putInDatas['pageSize'];
            $DB_Productsort = D('Contents/Productsort'); 
            

            $catInfo = $filter['catid'] ? $DB_Productsort->getCateInfo($filter['catid'])  : array('SortNameCH'=>'产品中心','SortNameEN'=>'Products Center','SortDescCN'=>'产品中心','SortDescEN'=>'Products Center') ;         
            $bigCatInfo =$DB_Productsort->getBigClass($catInfo);

            $SEO = seo("product",$filter['catid'], "产品中心", strip_tags($catInfo['SortDesc'.$LANG]), '');

            if($catInfo['linktype']==3){
               $rule =     $catInfo['linkurl'];
               $ruleArry = explode("&",$rule);
               for($j=0;$j<count($ruleArry);$j++){
                   $metaRuleArry  = explode("=",$ruleArry[$j]);
                   if(in_array($metaRuleArry[0],array("reSortID","SortID","Catid"))){
                       $SortID =     $metaRuleArry[1];
                   }else if(in_array($metaRuleArry[0],array("flagType","keyword","sizes","filterType","notID"))){
                       $$metaRuleArry[0]=  $metaRuleArry[1];
                       //echo $metaRuleArry[0] ."=". $metaRuleArry[1] ;
                   }
               }
            }

            $where['ViewFlag'.$LANG] = 1;         

            if($filter['flagType']=="new"){
               $where['NewFlag'] = 1 ;            
            }
            if($filter['flagType']=="hot"){
               $where['CommendFlag'] = 1 ;            
            }

            if($SortID > 0){
               $catChildrenIds = $DB_Productsort->getArrchildid($SortID,$LANG);
               $where['SortID'] = is_numeric($catChildrenIds) ? $catChildrenIds : array('in',$catChildrenIds);        
            }
            if($filter['sizes']){                
               if($bigCatInfo['ID']==4){
                   $ymSizeCat  = $DB_Productsort->where(array('ParentID'=>141,'SortNameCH'=>$filter['sizes']))->find();
                   if($ymSizeCat){
                       $ymSizeID = $ymSizeCat['ID'];                    
                       $where['SortID2'] = $ymSizeID;
                   }else{
                       $where['_string'] = 'FIND_IN_SET("'.$filter['sizes'].'",Sizes)'; 
                   }
                   
               }else{
                   $where['_string'] = 'FIND_IN_SET("'.$filter['sizes'].'",Sizes)'; 
               }
            }
            if($filter['notID']!=""){
               $notArry =  is_numeric($filter['notID']) ?  $notID : explode(",",$filter['notID']); 
               $where['SortID'] = is_numeric($filter['notID']) ? array('neq',$filter['notID']) : array('not in',$notArry);   
            }



            //$where['_string'] = 'FIND_IN_SET('.$catid.',SortPath)';
            $count = $this->where($where)->count();
            $PageObj = page($count, $pageSize); 
            $datas = $this->where($where)->limit($PageObj->firstRow.','.$PageObj->listRows)->order(array("NewFlag"=>'DESC',"ID" => "DESC"))->select();
            //var_dump(M("products")->getLastSql());
            $i = 0 ;
            foreach($datas as $key=>$value){
                $datas[$key]['timeline']  =date( strtotime($value['AddTime']));
                $datas[$key]['sortUrl'] = U('lists',array('catid'=>$value['SortID']));
             
                $datas[$key]['url'] = U('shows',array('id'=>$value['ID'])); 
             
                //效果图
                $efficePic = M('effectpic')->where(array('ViewFlag'.$LANG=>1,'ProductID'=>$value['ID']))->order(array("iscover"=>'DESC',"ID" => "DESC"))->find();
               
                $datas[$key]['effectpic'] = $efficePic;
                $datas[$key]['haveEffice'] = $efficePic?1:0; ;
               
               //分割尺寸
               //$sizeArry = split(",",$value['Sizes']);
               $sizeArry = explode(",",$value['Sizes']);
               
               
               //分割型号
               $modelArray =explode(",",$value['ProductModel']);
               //分割大图
               $bigImgArray =explode(",",$value['SizeBigpic']);
               //分割小图
               $smallImgArray =explode(",",$value['SizeSmallpic']);


               //重置尺寸
               $sizeArryLen = count($sizeArry);
               $datas[$key]['sizeArry'] = $sizeArry;
               $datas[$key]['resetSizes']  = "" ;
               $datas[$key]['resetSizesMM']  = "" ;
               for($j=0;$j<$sizeArryLen;$j++){
                   if(trim($sizeArry[$j])=="可订制尺寸"){
                       $datas[$key]['resetSizes'] = trim($sizeArry[$j]);   
                   }else{
                       $size[$j]=trim($sizeArry[$j]);
                       $size[$j]=reBSsize($size[$j],1);
                       $Separated=$j+1==$sizeArryLen?"":",";
                       $Separated2=$j+1==$sizeArryLen?"":"，";
                       $datas[$key]['resetSizes']  .=$size[$j].$Separated ;
                       $datas[$key]['resetSizesMM']  .=$size[$j]."(mm)".$Separated ;
                   }
                   $datas[$key]['perArrary'][$j]['sizes'] = $size[$j];
                   $datas[$key]['perArrary'][$j]['model'] = $modelArray[$j];
               }
               $sizeArray=explode(",",$datas[$key]['resetSizes']);


               
               //釉面砖分组
               if($SortID>0 && strpos($value['SortPath'],",4,")==1){

                   $datas[$key]['is_ym']=1;

               }
               
               $datas[$key]['i'] = $i;
               $i = $i++;
            }

            $showPages = $PageObj->show();
            $pageCache['datas'] = $datas;
            $pageCache['count'] = $count;
            $pageCache['showPages'] =  $showPages;
            $pageCache['catInfo'] =  $catInfo;
            $pageCache['bigCatInfo'] =  $bigCatInfo;
            $pageCache['SEO'] =  $SEO;
            $pageCache['filter'] =  $filter;



            S($cacheID,$pageCache);
            return $pageCache;

        }
    }


    //取得列表数据
    public function getShowDatas($inputDatas){
      $id = isset($inputDatas['id']) ? intval($inputDatas['id']) : 0 ;
      $LANG = isset($inputDatas['LANG']) ? intval($inputDatas['LANG']) : 'CH' ;

      if(!$id){return false;}

      $cacheID = 'productshow_'.$id;
      if ($return = S($cacheID)) {
        return $return ;       
      }else{
        
        $datas = $this->where(array('ID'=>$id,'ViewFlag'.$LANG=>1))->find();
        if(!$datas){ return false; }


        $DB_Productsort   = D('Contents/Productsort'); 
        $datas['catInfo'] = $DB_Productsort->getCateInfo($datas['SortID']);
        $datas['bigCate'] = $DB_Productsort->getBigClass($datas['catInfo']);

        $model_array = explode(",",$datas["ProductModel"]);
        $sizes_array = explode(",",$datas["Sizes"]);
        $SizeBigpic_array = explode(",",$datas["SizeBigpic"]);
        $SizeSmallpic_array = explode(",",$datas["SizeSmallpic"]);
        $len_model = count($model_array);
        foreach ($model_array as $key => $value) {
            $datas["model_list"][$key]["model"]         =trim($model_array[$key]);
            $datas["model_list"][$key]["size"]         =reBSsize(trim($sizes_array[$key]),1);
            $datas["model_list"][$key]["pic"]    =trim($SizeBigpic_array[$key]);
            $datas["model_list"][$key]["thumb"]  =trim($SizeSmallpic_array[$key]);
        }

        $DB_effectpic = M('Effectpic');
        $datas['effects'] = M('Effectpic')->where(array('ProductID'=>$datas['ID'],'ViewFlag'.$LANG=>1))->select();

        S($cacheID,$datas);
        return $datas;
      }

    }



}

