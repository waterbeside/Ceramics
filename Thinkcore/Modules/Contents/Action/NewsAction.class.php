<?php

// +----------------------------------------------------------------------
// | 新闻管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------
class NewsAction extends AdminbaseAction {
    //任务单列表
    public function index() {       
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }        

        $datas = array();    
        $DB_News = D('Contents/News');    
        $where = array();
        $filter['keywork'] = I('keywork','','trim');
        $filter['searchtype'] = I('searchtype','','strtolower');

        if ($filter['keywork']) {
            if($filter['searchtype']=='content'){
                $whereLike['ContentCH']  = array('like', '%'.$filter['keywork'].'%');
                $whereLike['ContentEN']  = array('like','%'.$filter['keywork'].'%');   
                $whereLike['_logic'] = 'or';
            }else{
                $whereLike['NewsNameCH']  = array('like', '%'.$filter['keywork'].'%');
                $whereLike['NewsNameEN']  = array('like','%'.$filter['keywork'].'%');
                
                $whereLike['_logic'] = 'or'; 
            }
            $where['_complex'] = $whereLike;
        }


        $count = $DB_News->where($where)->count();
        $Page = $this->page($count, 20);
        
        $pk = $DB_News->getPk();
        $datas = $DB_News->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array($pk => "desc"))->select();
        $catelist = D('Contents/Newssort')->getCateList();
        foreach ($datas as $key => $value) {
           
        }
        
        $this->assign("Page", $Page->show()); 
        $this->assign("datas", $datas);
        $this->assign("catelist", $catelist);
        $this->assign("filter", $filter);
        $this->display();
    }


    //改變狀態
    public function up_status() {
        $DB_News = D('Contents/News');    
        $json = $this->change_boolean($DB_News);

        if($json["status"]){
            D('Log')->record($json["info"],$json["status"]); 
        }
        $this->ajaxReturn($json); 
    }



    /**
     * 添加
     */
    public function add() {
        if (IS_POST) {
            

            C('TOKEN_ON', false);
            $NewsNameCH = I('post.NewsNameCH','','trim');
            $NewsNameEN = I('post.NewsNameEN','','trim');
            $auto_thumb = I('post.auto_thumb',0,'intval');
            $isjump = I('post.isjump',0,'intval');

            if(empty($NewsNameCH)){
                if(empty($NewsNameEN)){
                    $this->error('请输入标题');
                }else{
                    $_POST['NewsNameCH'] = $NewsNameEN;
                }
            }


            $data = $DB_News->create(); 
            if($isjump){
                $data['SourceEN'] = '跳转';
                $data['SourceCH'] = I('post.url','','trim');
            }

            $SortPath = D('Contents/Newssort')->getSortPath(I('post.SortID',0,'intval'));
            if($SortPath){
               $data['SortPath'] = $SortPath; 
            }
            if(empty($data['thumb']) && $auto_thumb==1){
                $data['thumb'] = getFristPicUrl($data['ContentCH']);
                $data['thumb'] = empty($data['thumb']) && !empty($data['ContentEN']) ? getFristPicUrl($data['ContentEN']) : $data['thumb'];
            }
            if (!$data) {
                $this->error($DB_News->getError());
            }


            $insertId = $DB_News->add($data);
            if ($insertId) {
                $this->success("添加成功(ID:".$insertId.")");
            } else {
                $this->error("添加失败！");
            }

        } else {
            $catelist = D('Contents/Newssort')->getCateList();
            $this->assign("catelist", $catelist);
            
            $this->display();
        }
    }

   
    /**
     * 编辑
     */
    public function edit() {
        //C('TOKEN_ON', false);
        
        $id = I('request.id',0,'intval');
        $id = $id ? $id : I('request.ID',0,'intval');
        if(!$id){
            $this->error('lost id');
        }
        $DB_News = D('Contents/News'); 
        
  
        if (IS_POST) {
            
            C('TOKEN_ON', false);
            $NewsNameCH = I('post.NewsNameCH','','trim');
            $NewsNameEN = I('post.NewsNameEN','','trim');
            $auto_thumb = I('post.auto_thumb',0,'intval');
            $isjump = I('post.isjump',0,'intval');

            if(empty($NewsNameCH)){
                if(empty($NewsNameEN)){
                    $this->error('请输入标题');
                }else{
                    $_POST['NewsNameCH'] = $NewsNameEN;
                }
            }


            $data = $DB_News->create(); 
            if($isjump){
                $data['SourceEN'] = '跳转';
                $data['SourceCH'] = I('post.url','','trim');
            }

            $SortPath = D('Contents/Newssort')->getSortPath(I('post.SortID',0,'intval'));
            if($SortPath){
               $data['SortPath'] = $SortPath; 
            }
            if(empty($data['thumb']) && $auto_thumb==1){
                $data['thumb'] = getFristPicUrl($data['ContentCH']);
                $data['thumb'] = empty($data['thumb']) && !empty($data['ContentEN']) ? getFristPicUrl($data['ContentEN']) : $data['thumb'];
            }

    
            if ($data) {
                if ($DB_News->where(array('ID'=>$id))->save($data)!==false) {
                    $this->success("編輯成功(ID:".$id.")");
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $this->error($DB_News->getError());
            }
        } else {

            $datas = $DB_News->where(array("ID" => $id))->find();
            $catelist = D('Contents/Newssort')->getCateList();
            
            $this->assign("datas", $datas);
            $this->assign("catelist", $catelist);
            $this->display();

           
        }
    }

    /**
     * 删除
     */
    public function delete() {

        $id = I('request.id',0,"intval");
        if(!$id ){
            $this->error('lost id');
        }
        $DB_News = D('Contents/News'); 

        $status = $DB_News->delete($id);
        if ($status) {
            $this->success("删除文章成功( ID:".$id.")", '',array("reload"=>1));
        } else {
            $this->error("删除失败！");
        }
    }

/**
     * 批量取得缩略图
     */
    public function create_thumb() {
        $dosubmit = I('request.dosubmit',0,'intval');
        if ($dosubmit) {
            if (IS_POST) {
                $this->redirect('create_thumb', $_POST);
            }       
            $pagesize = I('get.pagesize',50,'intval');
            $total = I('get.total',0,'intval');
            $page = I('get.'.C('VAR_PAGE'),1,'intval');
            $startid = I('get.startid',1,'intval');
            $endid = I('get.endid',1,'intval');

            $DB_News = D('Contents/News');
            $pk = $DB_News->getPk();
            $where[$pk]  = array('between',$startid.','.$endid);

            $count = $total>0 ? $total : $DB_News->where($where)->count();
            $Page = $this->page($count, $pagesize); 


            
            $datas = $DB_News->field('ID,NewsNameCH,NewsNameEN,thumb,ContentCH,ContentEN')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array($pk => "desc"))->select();
             
            if($Page->Total_Pages < $page){
                 $this->success("更新完成！ ...", U("create_thumb"));
                exit;
            }

            foreach ($datas as $key => $value) {
                if(empty($value['thumb'])){
                    $firstPic = getFristPicUrl($value['ContentCH']);
                    $firstPic = empty($firstPic) && !empty($value['ContentEN']) ? getFristPicUrl($value['ContentEN']) : $firstPic;
                    if(!empty($firstPic)){
                        $DB_News->where(array($pk=>$value[$pk]))->save(array('thumb'=>$firstPic));
                        $this->show('√');
                    }else{
                        $this->show('×');    
                    }
                }else{
                    $this->show('⊙');
                }
                

               // echo "id-".$value['ID'].'--'.$firstPic.'<br>';
            }
            $page_new = $page+1;
            $this->show('<br>每页'.$pagesize.'条数据<br>');
            $this->show('已完成第<b>'.$page.'</b>页<br> ');
            $this->redirect('create_thumb', array('dosubmit' => 1,'startid'=>$startid,'endid'=>$endid,C('VAR_PAGE')=>$page_new,'pagesize'=>$pagesize,'total'=>$count), 1,'准备下一页...');
            //$forward =   U("create_thumb",array('dosubmit' => 1,'page'=>$page_new,'pagesize'=>$pagesize,'total'=>$count));
            // $this->assign("waitSecond", 200);
            // $this->success($message, $forward);
            exit();
        

        }else{
            $DB_News = D('Contents/News');
            $count = $DB_News->where(array('thumb'=>''))->count();
            $end = $DB_News->field('ID')->limit(1)->order(array('ID'=>'desc'))->find();

            $this->assign('count',$count);
            $this->assign('endid',$end['ID']);
            $this->assign('startid',$end['ID']-49<1 ? 1: $end['ID']-49 );
            $this->display();
        }

   
    }


}
