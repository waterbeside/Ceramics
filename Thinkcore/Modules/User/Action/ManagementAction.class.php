<?php

/**
 * 管理员配置管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ManagementAction extends AdminbaseAction {

    protected $UserMod;

    function _initialize() {
        parent::_initialize();
        $this->UserMod = D("User");
    }

    //管理员列表
    public function index() {
        //角色id
        $role_id = I('get.role_id');
        $UserView = D("User");
        if (empty($role_id)) {
            $count = $this->UserMod->count();
            $page = $this->page($count, 20);
            $User = $this->UserMod->limit($page->firstRow . ',' . $page->listRows)->select();
        } else {
            $count = $this->UserMod->where(array("role_id" => $role_id))->count();
            $page = $this->page($count, 20);
            $User = $this->UserMod->limit($page->firstRow . ',' . $page->listRows)->where(array("role_id" => $role_id))->select();
        }
        foreach ($User as $key => $value) {
            $User[$key]['role_name'] = M('Role')->where(array('id'=>$value['role_id']))->getField('name');
        }
        $this->assign("Userlist", $User);
        $this->assign("Page", $page->show('Admin'));
        $this->display();
    }

    //编辑信息
    public function edit() {
        $id = I('request.id', 0, 'intval');
        if (empty($id)) {
            $this->error("请选择需要编辑的信息！");
        }
        if ($id == 1) {
            $this->error("该帐号不支持非本人修改！");
        }
        //判断是否修改本人，在此方法，不能修改本人相关信息
        if (AppframeAction::$Cache['uid'] == $id) {
            $this->error("不能修改本人信息！");
        }
        if (IS_POST) {
            if (false !== $this->UserMod->editUser($_POST)) {
                $this->success("更新成功！", "",array('reload'=>1));
            } else {
                $this->error($this->UserMod->getError());
            }
        } else {
            $data = $this->UserMod->where(array("id" => $id))->find();
            $role = M("Role")->select();
            $this->assign("role", $role);
            $this->assign("data", $data);
            $this->display();
        }
    }

    //添加管理员
    public function add() {
        if (IS_POST) {
            if ($this->UserMod->addUser($_POST)) {
                $this->success("添加管理员成功！", U('Management/index'));
            } else {
                $this->error($this->UserMod->getError());
            }
        } else {
            $data = M("Role")->select();
            $this->assign("role", $data);
            $this->display();
        }
    }

    //管理员删除
    public function delete() {
        $id = I('get.id');
        if (empty($id)) {
            $this->error("没有指定删除对象！");
        }
        if ((int) $id == AppframeAction::$Cache["uid"]) {
            $this->error("你不能删除你自己！");
        }
        //执行删除
        if ($this->UserMod->delUser($id)) {
            $this->success("删除成功！");
        } else {
            $this->error($this->UserMod->getError());
        }
    }

//相择管理员
    public function public_selectuser() {
        if (IS_POST) {
            $_POST['json'] = 1;
            $this->redirect('public_selectuser', $_POST);
        }
        
        $json = I('get.json', 0, 'intval');

        if($json){
            $roleid = I('get.role_id', 0, 'intval');
            $status = I('get.status', 2, 'intval');
            $onlydept = I('get.onlydept');
            $deptid = I('get.deptid',0, 'intval');
            $db_User = M('User');
            $db_Role = M("Role");
            $where = array();
            $keywords = I('get.keywords','','trim');
            if ($keywords!="") {
                $field = $_GET['searchtype'];
                if (in_array($field, array('id','role_id','status','username', 'realname', 'remark'))) {
                    if (in_array($field, array('id', 'role_id','status'))) {
                        $where[$field] = array('eq', $keywords);
                    } else {
                        $where[$field] = array('like', '%' . $keywords . '%');
                    }
                }
            }
            if($roleid){
                $where['role_id'] = $role_id;
            }
            if($status<2){
                $where['status'] = $status;
            }
            $db_Dept = D('Oa/Dept');
            if($onlydept){
                if(strpos($onlydept, '|')){
                    $deptidArray = explode('|', $onlydept);
                    $userids = D('Oa/DeptUser')->getUsersIdByArray($deptidArray);
                    $where['id'] = array('in',$userids);
                }elseif(intval($onlydept)){
                    $where['id'] = intval($onlydept);
                }
                
            }elseif($deptid){

                $deptChildrenIds = $db_Dept->getChildrenIds($deptid,1);

                $deptidArray = $deptChildrenIds;
                array_push($deptidArray, $deptid);
                $userids = D('Oa/DeptUser')->getUsersIdByArray($deptidArray);
                $where['id'] = array('in',$userids);

            }
            $count = $db_User->where($where)->count();
            $page = $this->page($count, 10);
            $rows = $db_User->field('id,role_id,username,status,realname')->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array('id' => "ASC"))->select();
            //echo($db_User->getLastSql());
            foreach ($rows as $key => $value) {
                $roledata = $db_Role->field("name")->where(array('id' => $value['role_id']))->find();                
                $rows[$key]['rolename'] = $roledata['name'];
            }
            $datas['rows'] = $rows;
            $datas['total'] = $count;
            $datas['role_id'] = $role_id;
            $datas['status'] = 1;
            $datas['Page'] = $page->show();
            $datas['filter'] = array('searchtype'=>$field,'keywords'=>$keywords,'catid'=>$catid,);
            $this->ajaxReturn($datas);
      
        }else{
            $multiple = I('get.multiple', 0, 'intval');
            $deptid = I('get.deptid', 0, 'intval');
            $onlydept = I('get.onlydept');
            $showpriv = I('get.showpriv');
            $basefilter = I('get.basefilter',0,'intval');
            
            $loadsuccess = I('get.loadsuccess');
            $param = I('get.param','','stripslashes');

            
            $url_param['json'] = 1;
            if($deptid){
                $url_param['deptid'] = $deptid; 
            }
            if($onlydept){
                $url_param['onlydept'] = $onlydept; 
            }
            $url = U('User/Management/public_selectuser',$url_param);
             

            $roles = D("Role")->getRoles('id,name');
            
            $this->assign('roles', $roles);
            $this->assign('showpriv', $showpriv);
            $this->assign('onlydept', $onlydept);
            $this->assign('deptid', $deptid);
            $this->assign('basefilter', $basefilter);
            $this->assign('loadsuccess', $loadsuccess);
            $this->assign('param', $param);
            $this->assign('url', $url);
            $this->display('selectuser');
        } 

    }

}