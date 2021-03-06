<?php

/**
 * 后台Action
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
//定义是后台
define('IN_ADMIN', true);
class AdminbaseAction extends AppframeAction {

    protected function _initialize() {

        parent::_initialize();
        $rbac_status = array(
            "status" => false,
            "url" => "",
            "error" => "",
        );
        C(array(
            "USER_AUTH_GATEWAY" => U("Admin/Public/login"), //登录地址
        ));
        $rbac_status = service("PassportAdmin")->checkPriv();        

        if($rbac_status['status']===false){
            $this->error($rbac_status['error'],$rbac_status['url']);
        }
        
        //初始化当前登录用户信息
        $this->initAdminUser();
        $this->initMenu();        
    }

    /**
     *  初始化当前登录用户信息
     * @return Boolean 没有登陆返回false，有登陆信息返回User Info
     */
    final protected function initAdminUser() {
        //当然登陆用户ID
        if(empty(self::$Cache['User'])){
            $usDb = service("PassportAdmin")->isLoggedAdmin();
            if ($usDb == false) {
                return false;
            }
            $User = $usDb;
            
            self::$Cache['User'] = $User;
        }else{
            $usDb = self::$Cache['User'];
        }
        
        self::$Cache['uid'] = (int) $usDb['userid'];
        self::$Cache['username'] = $usDb['username'];
        $this->assign("uid", self::$Cache['uid']);
        $this->assign("username", self::$Cache['username']);
        
        $this->assign("User", self::$Cache['User']);
        unset($usDb);
        return $User;
    }



/**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function error($message = '', $jumpUrl = '', $ajax = false) {
        //D('Log')->record($message, 0);
        parent::error($message, $jumpUrl, $ajax);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function success($message = '', $jumpUrl = '', $ajax = false) {
        D('Log')->record($message, 1);
        parent::success($message, $jumpUrl, $ajax);
    }


/**
     * 跳转的快捷方法
     */
    final public function returnJump($message,$status=0,$jumpUrl='',$ajax=false){
        if($status){
            parent::success($message, $jumpUrl, $ajax);
        }else{
            parent::error($message, $jumpUrl, $ajax);
        }
    }

    /**
     * 模板显示
     * @param type $templateFile 指定要调用的模板文件
     * @param type $charset 输出编码
     * @param type $contentType 输出类型
     * @param string $content 输出内容
     * 此方法作用在于实现后台模板直接存放在各自项目目录下。例如Admin项目的后台模板，直接存放在Admin/Tpl/目录下
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '') {
        parent::display($templateFile, $charset, $contentType, $content);
    }

    /**
     * 初始化后台菜单
     */
    private function initMenu() {
        $Menu = F("Menu");
        if (!$Menu) {
            D("Menu")->menu_cache();
        }
    }

    /**
     *  排序 排序字段为listorders数组 POST 排序字段为：listorder
     *  弃用
     */
    protected function listorders($model) {
        if (!is_object($model)) {
            return false;
        }
        $pk = $model->getPk(); //获取主键名称
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $model->where(array($pk => $key))->save($data);
        }
        return true;
    }

    /**
     * 分页输出
     * @staticvar array $_pageCache
     * @param type $Total_Size 信息总数
     * @param type $Page_Size 每页显示信息数量
     * @param type $Current_Page 当前分页号
     * @param type $List_Page 每次显示几个分页导航链接
     * @param type $PageParam 接收分页号参数的标识符
     * @param type $PageLink 分页规则 
     *                          array(
                                    "index"=>"http://www.abc3210.com/192.html",//这种是表示当前是首页，无需加分页1
                                    "list"=>"http://www.abc3210.com/192-{page}.html",//这种表示分页非首页时启用
                                 )
     * @param type $static 是否开启静态
     * @param string $TP 模板
     * @param array $Tp_Config 模板配置
     * @return array|\Page
     */
    protected function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 0, $List_Page = 6, $PageParam = '', $PageLink = '', $static = FALSE, $TP = "", $Tp_Config = "") {
        $Page = page($Total_Size, $Page_Size, $Current_Page, $List_Page, $PageParam, $PageLink, $static, $TP, $Tp_Config);
        $Page->SetPager('Admin', '{first}{prev}{liststart}{list}{listend}{next}{last}', array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
        return $Page;
    }

    /**
     * 获取菜单导航
     * @param type $app
     * @param type $model
     * @param type $action
     */
    public static function getMenu() {

        $menuid = (int) $_GET['menuid'];
        $menuid = $menuid ? $menuid : cookie("menuid", "", array("prefix" => ""));
        //cookie("menuid",$menuid);

        $db = D("Menu");
        $info = $db->cache(true, 60)->where(array("id" => $menuid))->getField("id,action,app,model,parentid,data,type,name,function");
        $find = $db->cache(true, 60)->where(array("parentid" => $menuid, "status" => 1))->getField("id,action,app,model,parentid,data,type,name,function");

        if ($find) {
            array_unshift($find, $info[$menuid]);
        } else {
            $find = $info;
        }
        foreach ($find as $k => $v) {
            $find[$k]['data'] = "menuid=$menuid&" . $find[$k]['data'];
        }

        return $find;
    }

    /**
     * 当前位置
     * @param $id 菜单id
     */
    final public static function current_pos($id) {
        $menudb = M("Menu");
        $r = $menudb->where(array('id' => $id))->find();
        $str = '';
        if ($r['parentid']) {
            $str = self::current_pos($r['parentid']);
        }
        return $str . $r['name'] . ' > ';
    }


    /**
     * 更改狀態
     * @access protected
     * @param string M
     * @return void
     */
    final public function change_boolean($M,$idfield="") {
        if (empty($M)) {
            $json['info'] = "Miss Model";
            $json['status'] = 0;            
        }else{
            if(is_string($M)){
                $MD = M($M);    
            }elseif(is_object($M)){
                 $MD = $M; 
            }else{
                $json["status"] = 0 ; 
                $json["info"] = "系统出错";
                return $json;
            }
            
            
            $value = I('post.value',0,"boolean");
            $id = I('post.id',0,"int");
            $field = I('post.field',"status");
            $idfield = $idfield=="" ? $MD->getPk() : $idfield; //默認為主鍵
            $reValue = $value==1 ? 0 :1;
            if($MD->where(array($idfield => $id))->save(array($field => $reValue ))){
                $json["result"] = $reValue;
                $json["info"] = "修改“".$field."=".$json["result"]."”成功(".$idfield."=".$id.")";
                $json["status"] = 1 ;
            }else{
                $json["result"] = $value;
                $json["info"] = "修改“".$field."=".$json["result"]."”失敗(".$idfield."=".$id.")";
                $json["status"] = 0 ; 
            }
        }    
        //$json['sql'] = $MD->getLastSql();
        return $json;
    }


}