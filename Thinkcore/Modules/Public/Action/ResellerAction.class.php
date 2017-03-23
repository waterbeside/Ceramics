<?php

// +----------------------------------------------------------------------
// | 经销商相关页
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------
class ResellerAction extends BaseAction {

    public $lang = 'CH';


    //初始化
    protected function _initialize() {
        parent::_initialize();
        $this->lang = defined('LANGUAGE') && in_array(LANGUAGE, array('CH','EN')) ?  LANGUAGE :  (I('get.lang','','strtolower') == 'en' ? 'EN' : 'CH');        
        $this->siteInfo = AppframeAction::$Cache['Config'];
        //不用险证的方法
        $unCheckAction = array('login','logout','register','check_unique','lists');
        if(!in_array(ACTION_NAME, $unCheckAction)){
            $islogin = service("PassportDealer")->isLogged();
            if(!$islogin){
                if(IS_AJAX){
                    $this->error('未登入');
                }
                $this->error('请先登入',U('login'));
                //$this->redirect('login');
            }    
        }
        
    }



    //用户首页
    public function index() {
        $userInfo = service("PassportDealer")->getUserInfo();
        unset($userInfo['Password']);
        unset($userInfo['verify']);

        $catList = C('menu_reseller');

        $subCatList = D('Contents/Downsort')->getCateList(1,$this->lang);
        $datas= M('Download')->where(array('ViewFlag'.$this->lang,'CommendFlag'=>1))->order(array("ID" => "desc"))->limit(5)->select();
        foreach ($datas as $key => $value) {
            $datas[$key]['cate'] = $subCatList[$value['SortID']];
        }

        $SEO = seo('','', "用户中心 - 经销商专区", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);
        //$SEO = array('site_title'=>'#lazyme#陶瓷','keyword'=>'','description'=>'','title'=>'')
        $this->assign("SEO", $SEO); 
        $this->assign("catList", $catList); 
        $this->assign("subCatList", $subCatList); 
        $this->assign("userInfo", $userInfo);         
        $this->assign("datas", $datas);         
        $this->display($this->parseTpl('reseller_index')); 
         
       
    }

    //用户首页
    public function baseinfo() {
        $sss = service("PassportDealer")->getSession();
        if($sss){
            $sss['status'] = 1;
            echo  json_encode($sss);
            exit();
        }else{
            $this->error('未登入');
            exit();
        }
        
    }

    //登入
    public function login() {
        if(IS_POST){
            $type = I('post.type',1,'intval');
            $username = I('post.username','','trim');
            $password = I('post.password','','trim');
            $verify = I('post.verify','','strtolower');
            if(!$username || !$password){
                 $this->error("请填写用户名或密码！",'',array('input'=>'username'));
            }
            //验证码开始验证
            if (!$this->verify($verify)) {
                 $this->error("验证码错误，请重新输入！",'',array('input'=>'verify'));
            }

            if (service("PassportDealer")->login($username, $password,$type)) {
                
                //D('Log')->record('登入成功！', 1);
                $forward = U("index");
                $this->success('登入成功',$forward);

            } else {
                
                $this->error("用户名或者密码错误，登陆失败！");
            }

        }else{


            if(service("PassportDealer")->isLogged()){
                $this->redirect('index');
            }
            $catList = C('menu_reseller');

            $SEO = seo('','', "登入 - 经销商专区", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);
            //$SEO = array('site_title'=>'#lazyme#陶瓷','keyword'=>'','description'=>'','title'=>'')
            $this->assign("SEO", $SEO); 
            $this->assign("catList", $catList); 
            $this->assign("datas", $datas);         
            $this->display($this->parseTpl('reseller_login'));    
        }
        
    }


    //登出
    public function logout() {
        service("PassportDealer")->logout();
        $this->success('登出成功！', U("login"));
    }

     //注册
    public function register() {
        if(IS_POST){

            $DB_Dealer = D('Contents/Dealer'); 
            $verify = I('post.verify','','strtolower');
            //验证码开始验证
            if (!$this->verify($verify)) {
                 $this->error("验证码错误，请重新输入！",'',array('input'=>'verify'));
            }
            $data = $DB_Dealer->create();
            
            if ($data) {
                // $this->success('经销商注册成功'); 
                if ($DB_Dealer->add($data)) {
                    $forward = U("login");
                    $this->success('经销商注册成功',$forward);                    
                } else {
                    $this->error("注册失败！");
                }
            } else {
                $error = $DB_Dealer->getError();
                if(strpos($error,'||')>0){
                    $errorArray = explode('||', $error);
                    $this->error($errorArray[1],'',array('input'=> $errorArray[0]));
                }else{
                    $this->error($error);
                }
               
            }

        }
        if(service("PassportDealer")->isLogged()){
            $this->redirect('index');
        }
        $catList = C('menu_reseller');

        $SEO = seo('','', "注册 - 经销商专区", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);
        //$SEO = array('site_title'=>'#lazyme#陶瓷','keyword'=>'','description'=>'','title'=>'')
        $this->assign("SEO", $SEO); 
        $this->assign("catList", $catList); 
        $this->assign("datas", $datas);
        $this->display($this->parseTpl('reseller_register'));
    }



    //检查唯一
    public function check_unique() {
        $field = I('post.f','','trim');
        $val = I('post.val');
        $isedit = I('get.isedit',0,'intval');
        $data = array();
        $DB_Dealer = D('Contents/Dealer');
        switch ($field) {
            case 'code':
                if(!is_numeric($val)){
                    $this->error('客户号只能是数字');
                }
                $data['WintoName'] = $val;
                break;
            case 'name':
                if(!$DB_Dealer->check($val,'4,10','length') ){
                    $this->error('用户名请控制在4~10个字内');
                }
                
                $data['MemName'] = $val;
                break;
            
            default:
               $this->error('参数错误');
                break;
        }

        if($isedit){
            $data['id'] = service("PassportDealer")->getUserid();
        }
                
        if($DB_Dealer->checkUnique($data)){
            $this->success('未被注册');
        }else{
            $this->error('已被注册');
        }
    }

    /**
     * 修改资料
     */
    public function edit() {
        //C('TOKEN_ON', false);
        
        $id = service("PassportDealer")->getUserid();
        $resetpwd = I('request.resetpwd',0,'intval');

        $DB_Dealer = D('Contents/Dealer'); 

        if (IS_POST) {
            
            C('TOKEN_ON', false);
            if($resetpwd){
                $password = I('post.password','','trim');
                if(!$password){
                    $this->error('请输入密码','',array('input'=>'password'));
                }
                if(!$DB_Dealer->check($password,'6,16','length') ){
                    $this->error('密码请控制在6~16个字符内','',array('input'=>'password'));
                }
                $pwdconfirm = I('post.pwdconfirm','','trim');
                if($password!=$pwdconfirm){
                    $this->error('两次密码不一致','',array('input'=>'pwdconfirm'));   
                }
                
                $data['Password'] = $DB_Dealer->formatPassword($password);
                $successMsg = '密码修改成功';
            }else{
                $userInfo = service("PassportDealer")->getUserInfo();

                if($userInfo['Working']){
                    if(isset($_POST['name'])){unset($_POST['name']);}
                    if(isset($_POST['code'])){unset($_POST['code']);}
                }
                if(isset($_POST['Working'])){unset($_POST['Working']);}
                $_POST['id'] = $id ;
                $data = $DB_Dealer->create(); 
                //var_dump($data);
                $successMsg = "用户资料修改成功";
            }
            //var_dump($data);
            if ($data) {
                if ($DB_Dealer->where(array('ID'=>$id))->save($data)!==false) {
                    $this->success($successMsg, '',array("reload"=>1));
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $error = $DB_Dealer->getError();
                if(strpos($error,'||')>0){
                    $errorArray = explode('||', $error);
                    $this->error($errorArray[1],'',array('input'=> $errorArray[0]));
                }else{
                    $this->error($error);
                }
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
                $this->display($this->parseTpl('reseller_edit_pw'));
            }else{
                $this->display($this->parseTpl('reseller_edit'));
            }
           
        }
    }

    //列表
    public function lists() {
                
        $catid = isset($_GET['catid']) ? I('get.catid',0,'intval') : I('get.SortID',0,'intval') ;
        $catList = C('menu_reseller');
        $islogged = service("PassportDealer")->isLogged();

        $subCatList = D('Contents/Downsort')->getCateList(1,$this->lang);

        $where =array('ViewFlag'.$this->lang=>1);

        if($catid){
            $catInfo = $subCatList[$catid]; 
            $where['SortID'] = $catid;
        }else{
            $catInfo = array();
        }

        $SEO = seo('down',$catid, "资料下载 - 经销商专区", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);

        $count = M("Download")->where($where)->count();
        $Page = $this->page($count, 20);        
        $datas = M("Download")->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "desc"))->select();

        $this->assign("showPages", $Page->show());
        $this->assign("islogged", $islogged); 
        $this->assign("catList", $catList); 
        $this->assign("catid", $catid); 
        $this->assign("subCatList", $subCatList); 
        $this->assign("catInfo", $catInfo); 
        $this->assign("datas", $datas); 
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('list_download'));
    }

    //下载细览
    public function shows() {
          
        $id = I('get.id',0,'intval');
        if(!$id){
            $this->error('内容不存在或已删除');
        }

        $catList = C('menu_reseller');
        $DB_Download = D('Contents/Download');
        
        $datas = $DB_Download->where(array('ID'=>$id,'ViewFlag'.$this->lang=>1))->find();        
        if($datas==false){
            $this->error('内容不存在或已删除');
        }
        $datas['yun'] = string2array($datas['yun']);

        

        $catid = $datas['SortID'];
        $subCatList = D('Contents/Downsort')->getCateList(1,$this->lang);
        $catInfo = $subCatList[$catid];
        
        $SEO = seo("down",$catid, $datas['DownName'.$this->lang], $datas['SeoDescription'.$this->lang], $datas['SeoKeywords'.$this->lang]);  

        $islogged = service("PassportDealer")->isLogged();

        $this->assign("catid", $catid); 
        $this->assign("catList", $catList); 
        $this->assign("subCatList", $subCatList);
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas); 
        $this->assign("islogged", $islogged); 
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('show_download'));
    }

    //进入下载

    public function download(){
        $id = I('get.id',0,'intval');
        $isyun = I('get.yun',0,'intval');
        $islogin = service("PassportDealer")->isLogged();
        if(!$id){
            $this->error('内容不存在或已删除',U('lists'));
        }
        
        if(!$isyun && !$islogin){
            $this->error('您还未登录，请登录后再试',U('login'));
        }
        if($islogin){
            $userInfo = service("PassportDealer")->getUserInfo();
            if(!$isyun && !$userInfo['Working']){
                $this->error('该帐号未通过审核，通过审核后方可下载文件',U('index'));
            }
        }
        $fileDatas= M('Download')->where(array('ID'=>$id,'ViewFlag'.$this->lang))->find();
        if(!$fileDatas){
            $this->error('文件不存在或已取消',U('lists'));            
        }

        $downUrl = "";
        if($isyun){
            $yun =  array();
            if($fileDatas['yun']!=''){
                $yun = json_decode($fileDatas['yun'],true) ;
            }else{
                $this->error('内容不存在或已删除',U('lists'));
            }

            if($yun['status'] =='2'){
                if(!$islogin){  
                    $this->error('请先登入再下载',U('index'));                
                }elseif(!$row['working']){
                     $this->error('该帐号未通过审核，通过审核后方可下载文件',U('index'));      
                }
            }

            $fileDatas['yun'] =$yun; 

            $downUrl = isset($fileDatas['yun']['url']) && $yun['status'] !='0'? $fileDatas['yun']['url'] : "";    
        }else{
            $downUrl = $fileDatas['FileUrl'] ; 
        }
        if(empty($downUrl)){
            $this->error('内容不存在或已删除',U('lists'));
        }else{
            $data['DownNumber'] = array('exp','DownNumber+1');
            M('Download')->where(array('ID'=>$id))->save($data);
        }
        redirect($downUrl);
    }


}