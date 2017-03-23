<?php

// +----------------------------------------------------------------------
// | 產品管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class ProductsAction extends AdminbaseAction {


    //任务单列表
    public function index() {
        
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }
        C('TOKEN_ON', false);
       
            $DB_Products = D('Contents/Products');
            $DB_Productsort = D('Contents/Productsort');  
            $DB_Effectpic = D('Contents/Effectpic'); 

            $productsortArray = $DB_Productsort->getProductsortIds();
            

            $where = array();
            $catid = I('get.catid',0,'intval');
            $filter['keywork'] = I('keywork','','trim');
            $filter['searchtype'] = I('searchtype','','strtolower');

            $filterStatus['ViewFlagCH'] = I('get.status_ch',0,'intval');
            $filterStatus['ViewFlagEN'] = I('get.status_en',0,'intval');
            $filterStatus['NewFlag']    = I('get.status_new',0,'intval');
            $filterStatus['CommendFlag'] = I('get.status_hot',0,'intval');

            if($catid>0){
                $childrenList = $DB_Productsort->getArrchildid($catid);
                $cateInfo = $productsortArray[$catid];
                if(is_numeric($childrenList)){
                    $where['SortID'] = $catid;
                }else{
                    $where['SortID']  = array('in',$childrenList);
                }        
            }

            foreach ($filterStatus as $field => $value) {
                $filter[$field] = $value;
                if($value){
                    $value = $value == 1 ? 1 : 0 ;
                    $where[$field] = $value;
                }
                
            }

            if ($filter['keywork']) {
                if(in_array($filter['searchtype'], array('productnamech','productnameen','productmodel','sizes'))){
                    if($filter['searchtype']==$sizes){
                        $size_1 = str_replace('*', 'X', $filter['keywork']) ;
                        $size_1 = str_replace('x', 'X', $size_1) ;
                        $size_2 = $DB_Products->reSize($size_1,'X',1);
                        $whereLike['sizes'] =array('like',array('%thinkphp%','%tp'),'OR');

                    }else{
                        $whereLike[$filter['searchtype']] = array('like','%'.$filter['keywork'].'%'); 
                    }
                    
                }else{
                    $whereLike['productnamech']  = array('like', '%'.$filter['keywork'].'%');
                    $whereLike['productnameen']  = array('like','%'.$filter['keywork'].'%');
                    $whereLike['productmodel']  = array('like','%'.$filter['keywork'].'%');

                    $whereLike['_logic'] = 'or';
                    
                }
                $where['_complex'] = $whereLike;
            }

      

            $count = $DB_Products->where($where)->count();
            $Page = $this->page($count, 20);
            
            $pk = $DB_Products->getPk();
            $datas = $DB_Products->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array($pk => "desc"))->select();
            //var_dump( $DB_Products->getLastSql());

            if($datas){
                foreach ($datas as $key => $value) {
                    $datas[$key]['effect_count'] = $DB_Effectpic->where(array('ProductID' => $value['ID'] ))->count();
                    $datas[$key]['cateInfo'] = $productsortArray[$value['SortID']];
                    $datas[$key]['cateInfo_2'] = $value['SortID2'] ? $productsortArray[$value['SortID2']] : array();
                    $datas[$key]['cateInfo_EN'] = $value['SortIDEN'] ? $productsortArray[$value['SortIDEN']] : array();
                }
            }

            $this->assign("Page", $Page->show()); 


        
        $this->assign("siteList", $siteList);
        $this->assign("catid", $catid);
        $this->assign("cateInfo", $cateInfo);

        $this->assign("nid", $nid);
        $this->assign("siteInfo", $siteInfo);
        $this->assign("datas", $datas);
        
        $this->assign("filter", $filter);
        $this->display();
    }


    //改變狀態
    public function up_status() {
        $nid = I('get.nid',"","strtoupper");
        $DB_Products = D('Contents/Products');    
        $json = $this->change_boolean($DB_Products);

        if($json["status"]){
            D('Log')->record($json["info"],$json["status"]); 
        }
        $this->ajaxReturn($json); 
    }



    /**
     * 添加站点
     */
    public function add() {
        if (IS_POST) {
            
            $add_multiple = I('post.add_multiple',0,'intval');

            
            C('TOKEN_ON', false);
            $data = $_POST ;
            
            $forPreCookie =array(
                'ProductNameCH' => trim($data['ProductNameCH']),
                'SeoKeywordsCH' => trim($data['SeoKeywordsCH']),
                'ProductNameEN' => trim($data['ProductNameEN']),
                'SeoKeywordsEN' => trim($data['SeoKeywordsEN']),
                'SortID'        => trim($data['SortID']),
                'SortPath'      => trim($data['SortPath']),
                'SortNameCH'    => trim($data['SortNameCH']),
            );
            if(isset($data['SortID2'])){
                $forPreCookie['SortID2']        = trim($data['SortID2']);
                $forPreCookie['SortPath2']      = trim($data['SortPath2']);
                $forPreCookie['SortNameCH2']    = trim($data['SortNameCH2']);
            }
            if(isset($data['SortIDEN'])){
                $forPreCookie['SortIDEN']       = trim($data['SortIDEN']);
                $forPreCookie['SortPathEN']     = trim($data['SortPathEN']);
                $forPreCookie['SortNameCHEN']   = trim($data['SortNameCHEN']);
            }
            cookie('webProductPrevAdd_'.$nid,$forPreCookie);
            
            $DB_Products = D('Contents/Products');    

            if($add_multiple){
                $multArray = array();
                foreach ($_POST['ProductModel'] as $key => $value) {
                    $itemData = $data ; 
                    $itemData['ProductModel']       = trim($value);
                    $itemData['Sizes']              = reBSsize(trim($_POST['ProductSizes'][$key]),1);
                    $itemData['SizeSmallpic']       = trim($_POST['SizeSmallpic'][$key]);
                    $itemData['SizeBigpic']         = trim($_POST['SizeBigpic'][$key]);
                    $itemData['SmallPic']       = trim($_POST['SizeSmallpic'][$key]);
                    $itemData['BigPic']         = trim($_POST['SizeBigpic'][$key]);

                    $multArray[$key]['data'] = $DB_Products->create($itemData);;
                    if(!$multArray[$key]['data']){
                        $this->error($DB_Products->getError());
                    }
                }
                $addidList = '';
          
                foreach ($multArray as $key => $value) {
                    $addid = $DB_Products->add($multArray[$key]['data']);
                    $addidList = $addid ? $addidList.','.$addid : $addidList;
                }

                if(empty($addid)){
                    $this->error("添加失败！");
                }else{
                    $this->success("产品添加成功(Site:".$nid."，id:".$addidList.")",U('index',array('nid'=>$nid)));
                }
            }else{
               
                $data['ProductModel']       = implode(",",array_map('trim', $_POST['ProductModel']));
                foreach ( $_POST['ProductSizes'] as $key => $value) {
                    $_POST['ProductSizes'][$key] = reBSsize($value,1);
                }
                $data['Sizes']              = implode(",",array_map('trim', $_POST['ProductSizes']));
                $data['SizeSmallpic']       = implode(", ",array_map('trim', $_POST['SizeSmallpic']));
                $data['SizeBigpic']         = implode(", ",array_map('trim', $_POST['SizeBigpic']));
            
                $data = $DB_Products->create($data);
                if ($data) {
                    if ($DB_Products->add($data)) {
                        $this->success("产品添加成功(Site:".$nid."，Model:".$data['ProductModel'].")",U('index',array('nid'=>$nid)));
                    } else {
                        $this->error("添加失败！");
                    }
                } else {
                    $this->error($DB_Products->getError());
                }
            }
        } else {
            C('TOKEN_ON', false);            
            
            
            $products_cate_field = explode(',',C('products_cate_field'));

            $DB_Memgroup = D('Contents/Memgroup');
            $memgroups = $DB_Memgroup->getListArray();

            $pvDatas = cookie('webProductPrevAdd');

            $pvDatas = $pvDatas ? json_encode($pvDatas) : json_encode(array('noDatas'=>1));

            $this->assign("pvDatas", $pvDatas);
            $this->assign("products_cate_field", $products_cate_field);
            $this->assign("memgroups", $memgroups);
            $this->assign("siteInfo", $siteInfo);
            $this->assign("nid", $nid);
            $this->display();
        }
    }

   
    /**
     * 编辑站点
     */
    public function edit() {
        if (IS_POST) {
           
            $id = I('post.ID',0,"intval");
            if(!$id ){
                $this->error('lost id');
            }

            C('TOKEN_ON', false);
            $data = $_POST ;
            $data['ProductModel']       = implode(",",array_map('trim', $_POST['ProductModel']));
            foreach ( $_POST['ProductSizes'] as $key => $value) {
                $_POST['ProductSizes'][$key] = reBSsize($value,1);
            }
            $data['Sizes']              = implode(",",array_map('trim', $_POST['ProductSizes']));
            $data['SizeSmallpic']       = implode(", ",array_map('trim', $_POST['SizeSmallpic']));
            $data['SizeBigpic']         = implode(", ",array_map('trim', $_POST['SizeBigpic']));
            $DB_Products = D('Contents/Products');    
            $data = $DB_Products->create($data);
            
           
            if ($data) {
                if ($DB_Products->where(array('ID'=>$id))->save($data)!==false) {
                    $this->success("产品修改成功(Site:".$nid."，ID:".$id.")",'',array('reload'=>1));
                } else {
                    $this->error("产品修改失败！");
                }
            } else {
                $this->error($DB_Products->getError());
            }
        } else {
            
            $id = I('get.id',0,"intval");
            if(!$id){
                $this->error('lost id');
            }
           
            $products_cate_field = explode(',',C('products_cate_field'));
            $DB_Products = D('Contents/Products');
            $datas = $DB_Products->where(array('ID'=>$id))->find();

            $model_array = explode(",",$datas["ProductModel"]);
            $sizes_array = explode(",",$datas["Sizes"]);
            $SizeBigpic_array = explode(",",$datas["SizeBigpic"]);
            $SizeSmallpic_array = explode(",",$datas["SizeSmallpic"]);
            $len_model = count($model_array);
            foreach ($model_array as $key => $value) {
                $datas["model_list"][$key]["model"]         =trim($model_array[$key]);
                $datas["model_list"][$key]["sizes"]         =reBSsize(trim($sizes_array[$key]),1);
                $datas["model_list"][$key]["SizeBigpic"]    =trim($SizeBigpic_array[$key]);
                $datas["model_list"][$key]["SizeSmallpic"]  =trim($SizeSmallpic_array[$key]);
            }

            $DB_Productsort = D('Contents/Productsort'); 
            $productsortArray = $DB_Productsort->getProductsortIds();

            $DB_Memgroup = D('Contents/Memgroup');
            $memgroups = $DB_Memgroup->getListArray();

            $this->assign("datas", $datas);
            $this->assign("productsortArray", $productsortArray);
            $this->assign("products_cate_field", $products_cate_field);
            $this->assign("memgroups", $memgroups);
            
            
            $this->display();
        }
    }

    
    /**
     * 删除站点
     */
    public function delete() {
       
        $id = I('request.id',0,"intval");
        if(!$id ){
            $this->error('lost id');
        }
        $DB_Products = D('Contents/Products'); 
        if(!$DB_Products){
            $this->error('無此站點');
        }
        $status = $DB_Products->delete($id);
        if ($status) {
            $this->success("删除产品成功！(Site:".$nid.",ID:".$id.")");
        } else {
            $this->error("删除产品失败！");
        }
    }


/**
     * 查找砖块
     */
    public function public_search() {
        
        $keyword = I('request.keyword','',"trim");
        $smode = I('request.smode',0,"intval");
        if(!$keyword){
            $this->error('请输入型号');
        }
        
        $siteConfig = self::$Cache['Config'];
 
        
        $path_products = str_replace('//', '/', $siteConfig['CFG_PATH_PRODUCTS']) ;
        $path_effect = str_replace('//', '/', $siteConfig['CFG_PATH_EFFECT']) ;

        $path_searching = str_replace('//', '/',$path_products);
        
        $dir_not = str_replace($path_products, '', $path_effect) ;
        $dir_not = str_replace('//', '/', $dir_not.'/') ;


        
        $pic=glob($path_searching."*/*/".$keyword.".jpg");
        $pic=ruleOutArrayByStr($pic,$dir_not);

        if(empty($pic)){
            $pic=glob($path_searching."*/*/*/".$keyword.".jpg");        
            $pic=ruleOutArrayByStr($pic,$dir_not);
        }
        if(empty($pic)){
            $pic=glob($path_searching."*/*/*/*/".$keyword.".jpg");            
            $pic=ruleOutArrayByStr($pic,$dir_not);            
        }
        if(empty($pic)){
            $pic=glob($path_searching."*/*/*/*/*/".$keyword.".jpg");            
            $pic=ruleOutArrayByStr($pic,$dir_not);            
        }



        if(empty($pic)){
            $this->error('找不到图片'.$keyword.'.jpg \n 请确保图片已上传到对应的文件夹');
            
        }else{


            $Coutentbig=str_replace($webSitePath,"",$pic[0]);

            if($smode){
                $cutHead = str_replace($path_products,"",$Coutentbig);
                $cutHeadArray =  explode('/', $cutHead);

                $Coutentsmall=str_replace($cutHeadArray[0],$cutHeadArray[0].'/small',$Coutentbig); 
            }else{
                $Coutentsmall=str_replace(".jpg","_s.jpg",$Coutentbig); 
                
            }
            
            
            $json["status"] =1;
            $json["bigPic"] = $Coutentbig;
            $json["smallPic"] = $Coutentsmall;
            $this->ajaxReturn($json); 

        }
    }



    //重命名，中
    public function rename() {
       if (IS_POST) {
            C('TOKEN_ON', false);
            $id = I('request.id',0,"intval");
            
            $txt = I('post.txt','','trim');
            $field = I('post.name','','trim');
            
            if(!$id){
                $this->error('lost id');
            }
            if(!in_array($field, array('ProductNameCH','ProductNameEN'))){
                $this->error("参数错误");
            }
            if($field=="ProductNameCH"&&$txt==""){
                $this->error('不能为空');            
            }
            $DB_Products = D('Contents/Products') ;
            if(!$DB_Products){
                $this->error('無此站點');
            }
            //var_dump($DB_Products->getDbFields());
            
            $data[$field]      = $txt;
            
            if ($DB_Products->where(array('ID'=>$id))->save($data)!==false) {
                $this->success("产品重命名成功(Site:".$nid."，ID:".$id.")");
            } else {
                $this->error("产品重命名失败！");
            }
            
        }
    }

}
