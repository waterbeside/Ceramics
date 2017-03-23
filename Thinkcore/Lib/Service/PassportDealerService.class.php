<?php

/* * 
 * 经销商通行证服务
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class PassportDealerService {


    //当前登录会员详细信息
    private static $userInfo = array();
    /**
     * 检验用户是否已经登陆
     * @return boolean 失败返回false，成功返回当前登陆用户基本信息
     */
    public function isLogged($verify=0) {

        $dealerid = cookie('dealerid');        
        if (session(C("DEALER_SESSION_KEY")) && session("dealername") &&  session("dealerverify") && $dealerid && $dealerid == session(C("DEALER_SESSION_KEY"))) {
            if($verify){
                $User = self::getUserInfo();
                //var_dump($User);
                if ($User && md5($user['Password']) . $User['verify'] == session("dealerverify")) {
                    return $User;
                }
            }else{
                return true;
            }
            
        }
        self::logout();
        return false;
    }


    /**
     * 登陆
     * @param type $identifier 用户ID,或者用户名
     * @param type $password 用户密码，不能为空
     * @return type 成功返回true，否则返回false
     */
    public function login($identifier, $password,$type=1) {
        if (empty($identifier) || empty($password)) {
            return false;
        }
        $user = $this->getUser($identifier, $password,$type);
            
        if (!$user) {
           // $this->recordLogin($identifier, $password, 0, "帐号密码错误");
            return false;
        }


        cookie('dealerid',$user['ID'],86400);
        cookie('dealerLastTime',$user['LastLoginTime'],86400);
        //设置标记
        session(C('DEALER_SESSION_KEY'), $user['ID']);
        //设置用户名
        session("dealername", $user['MemName']);
        //标记为登陆
        session("isdealer", true);

        //角色
        session("dealer_roleid",$user['GroupID']);
        //客户号
        session("dealer_code", $user['code']);
        //验证码
        session("dealerverify", md5($user['Password']) . $user['verify']);



        //记录登陆日志
        //$this->recordLogin($identifier, $password, 1);
        D("Contents/Dealer")->onLogined($user['ID']);

        $user['dealerid'] = $user['ID'];
        unset($user['ID']);
        self::$userInfo = $user;
        return true;
    }


    public function getSession(){
        return array(
            'dealerid' =>session(C('DEALER_SESSION_KEY')),
            'dealername' => session('dealername'),
            'isdealer' => session('isdealer'),
            'dealer_roleid'=>session("dealer_roleid"),
            'dealer_code'=>session("dealer_code"),
        );
    }

    public function getUserid(){
        return session(C('DEALER_SESSION_KEY'));
    }

    /**
     *  注销后台登陆
     * @return boolean 成功返回true，失败返回false
     */
    public function logout() {
        cookie('dealerid',null);
        cookie('dealerLastTime',null);
        
        // 注销session
        //设置标记
        session(C('DEALER_SESSION_KEY'), NULL);
        //设置用户名
        session("username", NULL);
        //标记为后台登陆
        session("isdealer", NULL);
        //角色
        session("dealer_roleid", NULL);

        session("dealer_code", NULL);


        //清空验证码
        session("dealerverify", NULL);
        return true;
    }

   

    /**
     * 根据提示符(MemName)和未加密的密码(密码为空时不参与验证)获取本地用户信息
     * @param type $identifier 为数字时，表示uid，其他为用户名
     * @param type $password 
     * @return 成功返回用户信息array()，否则返回布尔值false
     */
    public function getUser($identifier, $password = null,$type=0) {
        if (empty($identifier)) {
            return false;
        }
        return $UserMode = D("Contents/Dealer")->getUser($identifier, $password, $type);
    }
    

    /**
     * 获取当前登录用户资料
     * @return array 
     */
    final public static function getUserInfo() {
        if (empty(self::$userInfo)) {
            $uinfo = self::getUser((int) session(C("DEALER_SESSION_KEY")));
            $uinfo['dealerid'] = $uinfo['ID'];
            unset($uinfo['ID']);
            self::$userInfo = $uinfo;
        }
        return !empty(self::$userInfo) ? self::$userInfo : false;
    }

}

?>
