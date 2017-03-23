<?php

// +----------------------------------------------------------------------
// | 新闻分类管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class NewscatesAction extends AdminbaseAction {


    //任务单列表
    public function index() {
        
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }

        $datas = D('Contents/Newssort')->getCateList();
        $this->assign("datas", $datas);
        $this->display();
    }


    //改變狀態
    public function up_status() {
       
        $DB_Newssort = D('Contents/Newssort');
        $json = $this->change_boolean($DB_Newssort);

        if($json["status"]){
            $DB_Newssort->clearCateCache(); //刪除緩存
            D('Log')->record($json["info"],$json["status"]); 
        }
        $this->ajaxReturn($json); 
    }



    /**
     * 添加分類
     */
    public function add() {
        if (IS_POST) {
            $DB_Newssort =  D('Contents/Newssort');
            $data = $DB_Newssort->create();
            
            if ($data) {
                $insertId = $DB_Newssort->add($data);
                if ($insertId) {
                   
                    $this->success("添加成功");
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($DB_Newssort->getError());
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
            if(!$id  ){
                $this->error("lost id");
            }
            $DB_Newssort = D('Contents/Newssort');

            C('TOKEN_ON', false);
            $data = $DB_Newssort->create();
            //$SortPath = $DB_Newssort->buildSortPath($data['ID']);
 
            if ($data) {
                if ($DB_Newssort->save($data)!==false){
                   
                    $DB_Newssort->clearCateCache(); //刪除緩存
                    $this->success("編輯成功,ID:".$id.")",'',$json);
                } else {
                    $this->error("編輯失败！");
                }
            } else {
                $this->error($DB_Newssort->getError());
            }
        } else {
          
            $id = I('get.ID',0,'intval');
            if(!$id){
                $this->show('lost id');
                exit();
            }
            $parentid = I('get.parentid',0,'intval');
            
            $DB_Newssort = D('Contents/Newssort');

            $datas = $DB_Newssort->where(array('ID'=>$id))->find();

          
            $this->assign("id", $id);
            $this->assign("datas", $datas);
            $this->display();
        }
    }

    //删除
    public function delete() {
        
      
        $id = I('ID',0,'intval');
        if(!$id ){
            $this->error("lost id");
        }
        $DB_Newssort = D('Contents/Newssort');
        
        if(IS_POST && $id>0){
            $SortNameCH = I('post.SortNameCH','');
            $count = $DB_Newssort->where(array("ParentID" => $id))->count();
            if ($count > 0) {
                $json["status"] = 0 ;
                $json["info"] = "包含子分類，無法刪除" ;
                $this->ajaxReturn($json);
            }
            if ($DB_Newssort->delete($id) !== false) {
                $DB_Newssort->clearCateCache(); //刪除緩存
                $this->success("菜單“".$SortNameCH."”刪除成功");
            } else {
               // $json["status"] = 0 ;
                //$json["info"] = "菜單“".$name."刪除失敗" ;

                $this->error("菜單“".$SortNameCH."刪除失敗");
            }
        }
    }


    public function public_selectsort(){

                $DB_Newssort = D('Contents/Newssort');
                $result = $DB_Newssort->getCateList();
                
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
