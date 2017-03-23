<?php

/**
 * 后台模块公共方法
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class PublicAction extends AdminbaseAction {

    function _initialize() {
        parent::_initialize();

        $ip = get_client_ip();
       
    }

    //后台登陆界面
    public function login() {

        if(self::$Cache['User']['userid']){
                $this->redirect('Admin/Index/index');
        }else{
            $this->display();
        }
        
    }

    //后台登陆验证
    public function tologin() {
        //$blacklist = F("Blacklist_ip");
        //记录登陆失败者IP
        $ip = get_client_ip();
        $username = I("post.username", "", "trim");
        $password = I("post.password", "", "trim");
        $code = I("post.code", "", "trim");
     /*   echo $_SESSION['verify'].'_1<br>';
        echo md5(strtolower($code)).'<br>';
        echo $_SESSION['xx'].'<br>';
        echo md5($code).'<br>';
        echo md5(strtoupper($code)).'<br>';
        return false;*/
        if (empty($username) || empty($password)) {
            $this->error("用户名或者密码不能为空，请重新输入！", U("Public/login"));
        }
        if (empty($code)) {
            $this->error("请输入验证码！", U("Public/login"));
        }
        //验证码开始验证
        if (!$this->verify($code)) {
            $this->error("验证码错误，请重新输入！", U("Public/login"));
        }

        if (service("PassportAdmin")->loginAdmin($username, $password)) {
            /*$forward = cookie("forward");
            if (!$forward) {
                $forward = U("Admin/Index/index");
            } else {
                cookie("forward", NULL);
            }*/

           /* try {
                unset($blacklist[$ip]);
                F("Blacklist_ip", $blacklist);
            } catch (Exception $exc) {
                
            }*/
            
            
            
            D('Log')->record('登入成功！', 1);
            $forward = U("Admin/Index/index");
            $this->redirect("Admin/Index/index");
            //$this->success('登入成功！', $forward);
        } else {
            /*if (!$blacklist) {
                $blacklist = array();
            }
            $numbe = 1;
            $blacklist[$ip] = array(
                "time" => time(),
                "numbe" => (int) $blacklist[$ip]['numbe'] + 1,
            );
            F("Blacklist_ip", $blacklist);*/
            $this->error("用户名或者密码错误，登陆失败！", U("Public/login"));
        }
    }

    //退出登陆
    public function logout() {
        $userInfo['username'] = self::$Cache['username'];
        $userInfo['userid'] = self::$Cache['uid'];
        
        if (service("PassportAdmin")->logoutLocalAdmin()) {
            //手动登出时，清空forward
            cookie("forward", NULL);
            $message = '登出成功！';
            D('Log')->record($message, 1,$userInfo);
            $this->returnJump($message,1, U("Admin/Public/login"));
            //$this->success('登出成功！', U("Admin/Public/login"));
        }
    }

    //维持在线
    public function online() {
        
    }

    //检查
    public final function public_notice() {
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $url = base64_decode('aHR0cDovL3d3dy5zaHVpcGZjbXMuY29tL2FwaV91cGRhdGUucGhw');
        $url .= "?version=" . SHUIPF_VERSION . "&build=" . SHUIPF_BUILD . "&domain={$host}";
        try {
            if (function_exists("curl_init")) {
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $contents = curl_exec($ch);
                curl_close($ch);
            } else {
                $contents = file_get_contents($url);
            }
            $contents = json_decode($contents, true);
        } catch (Exception $exc) {
            $contents = array("notice" => "", "url" => "");
        }
        $data = array();
        $data['data'] = array(
            "notice" => $contents['notice'],
            "url" => $contents['url'],
        );
        $this->ajaxReturn($data);
    }

}
