<?php

// +----------------------------------------------------------------------
// | 经销商管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------
class DealerAction extends AdminbaseAction {
    //任务单列表
    public function index() {       
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }        
        $codeField = "WintoName" ;
        $datas = array();    
        $DB_Dealer = D('Contents/Dealer');    
        $where = array();
        $filter['keywork'] = I('keywork','','trim');
        $filter['searchtype'] = I('searchtype','','strtolower');

        if ($filter['keywork']) {
            if(in_array($filter['searchtype'], array('memname','realname','telephone','address','code'))){
                if($filter['searchtype']=='code'){
                    $whereLike[$codeField] = array('like','%'.$filter['keywork'].'%'); 
                }else{
                    $whereLike[$filter['searchtype']] = array('like','%'.$filter['keywork'].'%'); 
                }

            }else{
                $whereLike['memname']  = array('like', '%'.$filter['keywork'].'%');
                $whereLike['realname']  = array('like','%'.$filter['keywork'].'%');
                $whereLike['telephone']  = array('like','%'.$filter['keywork'].'%');
                $whereLike['address']  = array('like','%'.$filter['keywork'].'%');
                $whereLike['_logic'] = 'or';
                
            }
            $where['_complex'] = $whereLike;
        }

    //$datas = $DB_Dealer->select();

        $count = $DB_Dealer->where($where)->count();
        $Page = $this->page($count, 20);
        
        $pk = $DB_Dealer->getPk();
        $datas = $DB_Dealer->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array($pk => "desc"))->select();
        
        foreach ($datas as $key => $value) {
            $datas[$key] =  array_change_key_case($value);
            $datas[$key]['name'] = $value['MemName'];
            $datas[$key]['phone'] = $value['Telephone'];
            $datas[$key]['lasttime'] = $value['LastLoginTime'];
            $datas[$key]['code'] = $value[$codeField] ;
            
        }
        
        $this->assign("Page", $Page->show()); 
        $this->assign("datas", $datas);
        $this->assign("filter", $filter);
        $this->display();
    }


    //改變狀態
    public function up_status() {
        $nid = I('get.nid',"","strtoupper");
        $DB_Dealer = D('Contents/Dealer');    
        $json = $this->change_boolean($DB_Dealer);

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
            

            $DB_Dealer = D('Contents/Dealer'); 
            
            $data = $DB_Dealer->create();
            
            if ($data) {
                if ($DB_Dealer->add($data)) {
                    $this->success("經銷商【".$data['MemName']."】添加成功(Site:".$nid.")",U('index',array('nid'=>$nid)));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($DB_Dealer->getError());
            }
        } else {
            
            $this->assign("siteInfo", $siteInfo);
            $this->assign("nid", $nid);
            $this->display();
        }
    }

   
    /**
     * 编辑站点
     */
    public function edit() {
        //C('TOKEN_ON', false);
        
        $id = I('request.id',0,'intval');
        $resetpwd = I('request.resetpwd',0,'intval');

        if(!$id){
            $this->error('lost id');
        }
        $DB_Dealer = D('Contents/Dealer'); 
        
  
        if (IS_POST) {
            
            C('TOKEN_ON', false);
            if($resetpwd){
                $password = I('post.password','','trim');
                if(!$password){
                    $this->error('請輸入密碼');
                }
                $pwdconfirm = I('post.pwdconfirm','','trim');
                if($password!=$pwdconfirm){
                    $this->error('兩次密碼不一致');   
                }
                
                $data['Password'] = $DB_Dealer->formatPassword($password);
                $successMsg = '經銷商修改密碼成功(Site:'.$nid.',ID:'.$id.')';
            }else{

                $data = $DB_Dealer->create(); 
                $successMsg = "經銷商編輯成功(Site:".$nid.",ID:".$id.")";
            }
            //var_dump($data);
            if ($data) {
                if ($DB_Dealer->where(array('ID'=>$id))->save($data)!==false) {
                    $this->success($successMsg, '',array("reload"=>1));
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $this->error($DB_Dealer->getError());
            }
        } else {

            $codeField =   'WintoName' ;
            
            $data = $DB_Dealer->where(array("ID" => $id))->find();
            $data['code'] = $data[$codeField] ;
            
            $data['name'] = $data['MemName'];
            $data['phone'] = $data['Telephone'];
            $data =  array_change_key_case($data);
           
            
            $this->assign("data", $data);
            if($resetpwd){
                 $this->display('resetpwd');
            }else{
                 $this->display();
            }
           
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
        $DB_Dealer = D('Contents/Dealer'); 

        $status = $DB_Dealer->delete($id);
        if ($status) {
            $this->success("經銷商删除成功！(Site:".$nid.",ID:".$id.")", '',array("reload"=>1));
        } else {
            $this->error("删除失败！");
        }
    }


}
