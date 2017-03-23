<?php

// +----------------------------------------------------------------------
// | 产品分类管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class ProductsortAction extends AdminbaseAction {


    //任务单列表
    public function index() {
        
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }

       
        if(IS_AJAX){

            set_time_limit(0);

            
            $DB_Productsort = D('Contents/Productsort');
            $result = $DB_Productsort->getListArray();

            
            $array =array();
            foreach ($result as $key=>$r) {
                $array[$key] = $r;
                $array[$key]['parentid'] = $array[$key]['ParentID'] ;
                unset($array[$key]['ParentID']);
            }
    
            
            //echo json_encode($array);
            //import("Lib.Util.Tree",APP_PATH);
            $tree = new Tree();
            $tree->init($array);
            $datas = $tree->get_tree_array(0,'ID');
            //var_dump($datas);
            //$isSimpleTree = I('get.ist',0,"boolean");
            //if($isSimpleTree){
            //   $json_str = str_replace(",\"SortNameCH\":",",\"text\":",json_encode($datas));
            //}else{
                $treemenu[0]['ID'] = 0 ;
                $treemenu[0]['SortNameCH'] = 'root' ;
                $treemenu[0]['children'] = $datas ;
                $json_str = json_encode($treemenu);
            //}
        
            
            exit($json_str);
            
   
        }

        $this->assign("filter", $filter);
        $this->display();
    }


    //改變狀態
    public function up_status() {
       
        $DB_Productsort = D('Contents/Productsort');
        $json = $this->change_boolean($DB_Productsort);

        if($json["status"]){
            $DB_Productsort->deleteListCache(); //刪除緩存
            D('Log')->record($json["info"],$json["status"]); 
        }
        $this->ajaxReturn($json); 
    }



    /**
     * 添加分類
     */
    public function add() {
        if (IS_POST) {
           

            $DB_Productsort = D('Contents/Productsort'); 

            C('TOKEN_ON', false);
            $data = $DB_Productsort->create();
            
            if ($data) {
                $insertId = $DB_Productsort->add($data);
                if ($insertId) {
                    $SortPath = $DB_Productsort->buildSortPath($insertId);          
                    if($SortPath){
                        $DB_Productsort->where(array('ID'=>$insertId))->save(array('SortPath'=>$SortPath)) ; 
                    }
                    $json["action"] =ACTION_NAME ;
                    $json["datas"] = $DB_Productsort->where(array('ID'=>$insertId))->find() ;

                    $DB_Productsort->deleteListCache(); //刪除緩存
                    $this->success("產品分類添加成功(ID:".$insertId.")",'',$json);
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($DB_Productsort->getError());
            }
        } else {
        
            $parentid = I('get.parentid',0,'intval');


            $this->assign("parentid", $parentid);
            $this->display();
        }
    }

   
    /**
     * 编辑分類
     */
    public function edit() {
        if (IS_POST) {
            
            $id = I('ID',0,'intval');
            if(!$id ){
                $this->error("lost id");
            }
            $DB_Productsort = D('Contents/Productsort'); 

            C('TOKEN_ON', false);
            $data = $DB_Productsort->create();
            //$SortPath = $DB_Productsort->buildSortPath($data['ID']);
 
            if ($data) {
                if ($DB_Productsort->save($data)!==false){
                    $json["action"] =ACTION_NAME ;
                    $json["datas"] = $data ;
                    $DB_Productsort->deleteListCache(); //刪除緩存
                    $this->success("產品分類編輯成功(ID:".$id.")",'',$json);
                } else {
                    $this->error("編輯失败！");
                }
            } else {
                $this->error($DB_Productsort->getError());
            }
        } else {
            $nid = I('get.nid','','strtoupper');
            $id = I('get.ID',0,'intval');
            if(!$id || !$nid){
                $this->show('lost id');
                exit();
            }
            $parentid = I('get.parentid',0,'intval');

            $DB_Productsort = D('Contents/Productsort'); 
            $datas = $DB_Productsort->where(array('ID'=>$id))->find();


            $this->assign("id", $id);
            $this->assign("datas", $datas);
            $this->display();
        }
    }

    //删除
    public function delete() {
        

        $id = I('ID',0,'intval');
        if(!$id){
            $this->error("lost id");
        }
        $DB_Productsort = D('Contents/Productsort'); 
  
        if(IS_POST && $id>0){
            $SortNameCH = I('post.SortNameCH','');
            $count = $DB_Productsort->where(array("ParentID" => $id))->count();
            if ($count > 0) {
                $json["status"] = 0 ;
                $json["info"] = "包含子分類，無法刪除" ;
                $this->ajaxReturn($json);
            }
            if ($DB_Productsort->delete($id) !== false) {
                $DB_Productsort->deleteListCache(); //刪除緩存
                $this->success("菜單“".$SortNameCH."”刪除成功");
            } else {
               // $json["status"] = 0 ;
                //$json["info"] = "菜單“".$name."刪除失敗" ;
                $this->error("菜單“".$SortNameCH."刪除失敗");
            }
        }
    }


//移动
    public function move() {

        $id = I('post.ID', 0, 'intval');
        $parentid = I('post.parentid', 0, 'intval');

        
        if(!$id || !$parentid){
            $this->error("lost id");
        }
        


        if(IS_POST){
            $DB_Productsort = D('Contents/Productsort'); 

            
            $Productsorts = $DB_Productsort->getProductsortIds();
            $parentid_o = $Productsorts[$id]['ParentID'];
            $map = array('ID' => $id, );
            $data['ParentID'] = $parentid;
            
            if($DB_Productsort->where($map)->save($data)!==false){
                $DB_Productsort->deleteListCache(1);//重置緩存   
                
                $childrenList  = explode(',', $DB_Productsort->getArrchildid($id));
                
                foreach ($childrenList as $key => $childrenID) {
                    $SortPath = $DB_Productsort->buildSortPath($childrenID);
                    if($SortPath){
                        //var_dump($SortPath);
                        $DB_Productsort->where(array('ID'=>$childrenID))->save(array('SortPath'=>$SortPath));
                    }
                }    
            
                $DB_Productsort->deleteListCache();//刪除緩存   
                

                $json["status"] = 1 ;
                $json["info"] = "菜單移動成功(".$catid."->".$parentid.")" ;
            }else{
                $json["status"] = 0 ;
                $json["info"] = "菜單移動失敗(".$catid."->".$parentid.")" ;
            }
            $this->ajaxReturn($json); 
        }
    }

    public function public_selectsort(){
        
        
        
   

                $DB_Productsort = D('Contents/Productsort');
                $result = $DB_Productsort->getListArray();
                
                $array =array();
                foreach ($result as $key=>$r) {
                    
                    $array[$key]['parentid'] = $r['ParentID'] ;
                    $array[$key]['ID'] = $r['ID'] ;
                    $array[$key]['SortNameCH'] = $r['SortNameCH'] ;
                    $array[$key]['SortNameEN'] = $r['SortNameEN'] ;
                    $array[$key]['SortPath'] = $r['SortPath'] ;
                    $array[$key]['orders'] = $r['orders'] ;
                    $array[$key]['linktype'] = $r['linktype'] ;
                    $array[$key]['ViewFlagCH'] = $r['ViewFlagCH'] ;
                    $array[$key]['ViewFlagEN'] = $r['ViewFlagEN'] ;
                    $array[$key]['children'] = $r['children'] ;
               
                }
                //echo json_encode($array);
                //import("Lib.Util.Tree",APP_PATH);
                $tree = new Tree();
                $tree->init($array);
                $datas = $tree->get_tree_array(0,'ID');
                $isSimpleTree = I('get.ist',0,"boolean");
                if($isSimpleTree){
                   $json_str = str_replace(",\"SortNameCH\":",",\"text\":",json_encode($datas));
                }else{
                    $treemenu[0]['ID'] = 0 ;
                    $treemenu[0]['SortNameCH'] = 'root' ;
                    $treemenu[0]['children'] = $datas ;
                    $json_str = json_encode($treemenu);
                }
            
                
                echo $json_str ;
                exit();
        
    }

}
