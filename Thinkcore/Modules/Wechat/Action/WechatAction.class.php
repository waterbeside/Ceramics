<?php

// +----------------------------------------------------------------------
// | 微信公众平台管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class WechatAction extends AdminbaseAction {
    
    protected $wid;
    function _initialize() {
        parent::_initialize();
        if(!in_array(ACTION_NAME, array('index','listorders','add','edit'))&&!IS_POST){
            $wid = I('get.wid',"","strtoupper");
            if(!$wid){
                $wid = cookie('Wechat_wid_current');
                if($wid){
                    $_GET['wid'] = $wid;
                    $this->redirect('', $_GET);
                }
            }else{
                cookie('Wechat_wid_current',$wid);
            }
            $this->wid = $wid ; 
        }
    }

    //站点列表
    public function index() {
        
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }

        
        $filter['keywork'] = I('keywork','','trim');
        $where = array();

        if ($filter['keywork']) {
            $where['name'] = array('like','%'.$filter['keywork'].'%');
        }


        
        $DB_Wechat = D('Wechat');

        if(!empty($where)){
            $datas = $DB_Wechat->where($where)->order(array("listorder" => "asc","id"=>"asc"))->select();
            foreach ($datas as $key => $value) {
               $datas[$key]['setting'] = string2array($value['setting']);
            }
        }else{
            $datas = $DB_Wechat->getWechatWids();
        }
        

        
        $this->assign("datas", $datas);        
        $this->assign("filter", $filter);
        $this->display();
    }


   //改變排序
    public function listorders() {
       if(IS_POST){
            $listorders = I("post.listorders");
            $str = "";
            $DB_Wechat = D('Wechat');
            foreach($listorders as $id => $listorder) {
                if($DB_Wechat->where(array('id'=>$id))->save(array('listorder'=>$listorder))){
                    $str.=$id.":".$listorder.",";   
                }
            }
            $msg = "更新排序成功！(".$str."）";
            //$this->show($msg)  ;
            F('Wechats',NULL);
            $this->success($msg);
       }
    }


    /**
     * 添加站点
     */
    public function add() {
        C('TOKEN_ON', false);
        if (IS_POST) {
            $DB_Wechat = D('Wechat');
            
            $data = $DB_Wechat->create();
            
            if ($data) {
                if ($DB_Wechat->add($data)) {
                    F('Wechats',NULL);
                    $this->success("公众号【".$data['name']."】添加成功",'',array('reload'=>1));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($DB_Wechat->getError());
            }
        } else {
            $this->display();
        }
    }


/**
     * 删除站点
     */
    public function delete() {
        $id = I('get.id', 0, 'intval');
        $DB_Wechat = D('Wechat');
        $status = $DB_Wechat->delete($id);
        if ($status) {
            F('Wechats',NULL);
            $this->success("删除成功！", '',array("reload"=>1));
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 编辑站点
     */
    public function edit() {
        //C('TOKEN_ON', false);
        $id = I('request.id', 0, 'intval');
        if (empty($id)) {
            $this->error('请选择需要编辑的站点！');
        }
  
        if (IS_POST) {
            $DB_Wechat = D('Wechat');
            $data = $DB_Wechat->create();
            if ($data) {
                if ($DB_Wechat->save($data)!==false) {
                    F('Wechats',NULL);
                    $this->success("站点【".$data['name']."】修改成功！", '',array("reload"=>1));
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $this->error($DB_Wechat->getError());
            }
        } else {
            $DB_Wechat = D('Wechat');
            $datas = $DB_Wechat->where(array("id" => $id))->find();
            $datas['setting'] = string2array($datas['setting']);
            if (!$datas) {
                $this->error("该站点不存在！");
            }
            $this->assign("datas", $datas);
            $this->display();
        }
    }



    //设置 页
    public function setting() {
        C('TOKEN_ON', false);
        
        $wid = $this->wid ;
        $DB_Wechat = D('Wechat');
        $wechatList  = $DB_Wechat->getWechatWids();
        $wechatInfo = $wechatList[$wid]; 

        $act = I('request.act','','strtolower');
        switch ($act) {
            case 'welcome':
                
                break;
            
            default:
                # code...
                break;
        }
        $this->assign("wechatList", $wechatList);
        $this->assign("wechatInfo", $wechatInfo);
        $this->display();
    }

    //菜单管理。
    public function menu() {
        C('TOKEN_ON', false);
        $wid = $this->wid ; 

        $DB_Wechat = D('Wechat');
        $wechatList  = $DB_Wechat->getWechatWids();
        $wechatInfo = $wechatList[$wid]; 
        $this->assign("wechatList", $wechatList);
        $this->assign("wechatInfo", $wechatInfo);
        $this->display();
    }



    //任务设置首页
    public function get_access_token() {
        

        $wid = I('get.wid',"","strtoupper");
        $cacheName = 'Wechat_access_token_'.$wid ;
        if(S($cacheName)){
            return S($cacheName);
        }
        
        $DB_Wechat = D('Wechat');
        
        $wechatList  = $DB_Wechat->getWechatWids();
        $wechatInfo = $wechatlist[$wid]; 

        $appid = $wechatInfo['setting']['wechat_appid'];
        $appsecret = $wechatInfo['setting']['wechat_appsecret'];        
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $output = https_request($url);
        $jsoninfo = json_decode($output, true);
        $access_token = $jsoninfo["access_token"];
        S($cacheName,$access_token,7190);
        return $access_token;
    }



}
