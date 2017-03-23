<?php

/* * 
 * 后台通行证服务
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class PassportAdminService {


    //当前登录会员详细信息
    private static $userInfo = array();
    /**
     * 检验用户是否已经登陆
     * @return boolean 失败返回false，成功返回当前登陆用户基本信息
     */
    public function isLogged() {
        if (defined("IN_ADMIN") && IN_ADMIN) {
            return self::isLoggedAdmin();
        } else {
            return false;
        }
    }

    /**
     * 获取后台登陆信息
     * @return type  已经登陆返回用户基本信息，否则返回false
     */
    public function isLoggedAdmin() {

        $userid = cookie('userid'); 
        cookie('debug_userid',$userid);
        cookie('debug_s',C("USER_AUTH_KEY").':'.session(C("USER_AUTH_KEY")).";".'username:'.session("username").';adminverify:'.session("adminverify"));
        if (session(C("USER_AUTH_KEY")) && session("username") &&  session("adminverify") && $userid && $userid == session(C("USER_AUTH_KEY"))) {
            $User = self::getUserInfo();
            //var_dump($User);
            if ($User && md5($User['password']) . $User['verify'] == session("adminverify")) {
                return $User;
            }
            cookie('debug_logout',':1'. date("Y-m-d h:i:sa"));
        }else{
             cookie('debug_logout',':2'. date("Y-m-d h:i:sa"));
        }
        
        self::logoutLocalAdmin();
        return false;
    }



    /**
     * 获取后台登陆信息
     * @return type  已经登陆返回用户基本信息，否则返回false
     */
    public function  checkPriv(){

        $rbac_status = array();

        if (!RBAC::AccessDecision(GROUP_NAME)) {
            //检查认证识别号
            if (!RBAC::checkLogin()) {

                $rbac_status['status'] = false;
                $rbac_status['error'] = "请登陆后操作！";
                $rbac_status['url'] = C('USER_AUTH_GATEWAY');
     
                //记录当前页面地址到cookie中，用于登陆成功后跳转到该地址。
                cookie("forward", get_url());                
                return $rbac_status;
            }

            // 没有权限 抛出错误
            if (C('RBAC_ERROR_PAGE')) {
                // 定义权限错误页面
                redirect(C('RBAC_ERROR_PAGE'));
            } else {
                $rbac_status['status'] = false;
                $rbac_status['error'] = "您没有操作此项的权限！";
            }
        } else {
            $rbac_status['status'] = true;
        }
         
        return $rbac_status;
    }


    /**
     * 登陆后台
     * @param type $identifier 用户ID,或者用户名
     * @param type $password 用户密码，不能为空
     * @return type 成功返回true，否则返回false
     */
    public function loginAdmin($identifier, $password) {
        if (empty($identifier) || empty($password)) {
            return false;
        }
        $user = $this->getLocalAdminUser($identifier, $password);
            
        if (!$user) {
            $this->recordLoginAdmin($identifier, $password, 0, "帐号密码错误");
            return false;
        }
        //判断帐号状态
        if ($user['status'] == 0) {
            //记录登陆日志
            $this->recordLoginAdmin($identifier, $password, 0, "帐号被禁止");
            return false;
        }

        cookie('userid',$user['id'],86400);
        //设置标记
        session(C('USER_AUTH_KEY'), $user['id']);
        //设置用户名
        session("username", $user['username']);
        //标记为后台登陆
        session("isadmin", true);
        //角色
        session("roleid", $user['role_id']);
        //验证码
        session("adminverify", md5($user['password']) . $user['verify']);
        //特权。创始人
        if ((int) $user['role_id'] === 1) {
            session(C('ADMIN_AUTH_KEY'), true);
        }
        
        //缓存访问权限
        RBAC::saveAccessList();
        //记录登陆日志
        $this->recordLoginAdmin($identifier, $password, 1);
        M("User")->where(array("id" => $user['id']))->save(array(
            "last_login_time" => time(),
            "last_login_ip" => get_client_ip()
        ));
        return true;
    }

    /**
     *  注销后台登陆
     * @return boolean 成功返回true，失败返回false
     */
    public function logoutLocalAdmin() {
        cookie('userid',null);
        // 注销session
        //设置标记
        session(C('USER_AUTH_KEY'), NULL);
        //设置用户名
        session("username", NULL);
        //标记为后台登陆
        session("isadmin", NULL);
        //角色
        session("roleid", NULL);
        //特权。创始人
        session(C('ADMIN_AUTH_KEY'), NULL);
        //删除权限缓存
        session("_ACCESS_LIST", NULL);
        //清空验证码
        session("adminverify", NULL);
        return true;
    }

    /**
     * 记录后台登陆信息
     * @param type $uid 用户ID
     */
    public function recordLoginAdmin($identifier, $password, $status, $info = "") {
        // M("Loginlog")->add(array(
        //     "username" => $identifier,
        //     "logintime" => date("Y-m-d H:i:s"),
        //     "loginip" => get_client_ip(),
        //     "status" => $status,
        //     "password" => "***" . substr($password, 3, 4) . "***",
        //     "info" => $info
        // ));
    }

    /**
     * 根据提示符(username)和未加密的密码(密码为空时不参与验证)获取本地用户信息
     * @param type $identifier 为数字时，表示uid，其他为用户名
     * @param type $password 
     * @return 成功返回用户信息array()，否则返回布尔值false
     */
    public function getLocalAdminUser($identifier, $password = null) {
        if (empty($identifier)) {
            return false;
        }
        $map = array();
        if (is_int($identifier)) {
            $map['id'] = $identifier;
        } else {
            $map['username'] = $identifier;
        }
        $UserMode = D("User/User");
        
        $user = $UserMode->where($map)->find();
        if (!$user) {
            return false;
        }
        if ($password) {
            //验证本地密码是否正确
            if ($UserMode->encryption($identifier, $password, $user['verify']) != $user['password']) {
                return false;
            }
        }
        return $user;
    }

    /**
     * 获取当前登录用户资料
     * @return array 
     */
    final public static function getUserInfo() {
        if (empty(self::$userInfo)) {
            $uinfo = self::getLocalAdminUser((int) session(C("USER_AUTH_KEY")));
            $uinfo['userid'] = $uinfo['id'];
            unset($uinfo['id']);
            self::$userInfo = $uinfo;
        }
        return !empty(self::$userInfo) ? self::$userInfo : false;
    }

}

?>
