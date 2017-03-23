<?php


class LogModel extends CommonModel {

    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('time', 'time', 1, 'function'),
        array('ip', 'get_client_ip', 3, 'function'),
    );

    /**
     * 記錄日誌
     * @param type $message 说明
     */
    public function record($message, $status = 0,$userInfo="") {
        $fangs = 'GET';
        if (IS_AJAX) {
            if(IS_POST){
                $fangs = 'POST(Ajax)';
            }else{
                $fangs = 'GET(Ajax)';
            }
            
        } else if (IS_POST) {
            $fangs = 'POST';
        }
        if(isset($userInfo['username'])){
            $uid = $userInfo['userid']? $userInfo['userid'] : 0;
            $username = $userInfo['username'] ;
        }else{
            if(MODULE_NAME=="Public" && ACTION_NAME =="login" ){
                 $uid = 0 ;
                 $username = I("post.username","",'trim') ;
            }else{
                
                //获取用户信息
                $userInfo = service("PassportAdmin")->isLogged();
                //当然登陆用户ID
                $uid =  $userInfo['userid']? $userInfo['userid'] : 0;
                $username = $userInfo['username']? $userInfo['username']: "" ;
            }
        }
            
        
        $data = array(
            'uid' => $uid,
            'username' => $username,
            'status' => $status,
            'm' => MODULE_NAME,
            'g' => GROUP_NAME,
            'a' => ACTION_NAME,
            'info' => $message,
            'request_type' =>$fangs,
            'query_string' => $_SERVER['QUERY_STRING'],
        );
        C('TOKEN_ON',false);        
        $data = $this->create($data);
        return $this->add() !== false ? true : false;
    }

    /**
     * 刪除多少天前的日誌
     * @param int $days 天數，默認30。
     * @return boolean
     */
    public function deleteDaysago($days=60){
        if((int)$days>0){
            $status = $this->where(array("time" => array("lt", time() - (86400 * $days))))->delete();
            return $status !== false ? true : false;    
        }else{
            return false;    
        }
    }

    /**
     * 删除一个月前的日志
     * @return boolean
     */
    public function deleteAMonthago() {
        return $this->deleteDaysago(30);
    }

}
