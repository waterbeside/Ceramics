<?php

/**
 * 經銷商管理模型
 */
class DealerModel extends CommonModel {
	
	protected $tableName = 'Members'; 

	protected $_map = array(
        'id' =>'ID',
        'name'  =>'MemName',
        'realname'  =>'RealName',
        'password'  =>'Password',
        'phone' => 'Telephone',
        'address' => 'Address',
        'addtime' => 'AddTime',
        'sex' => 'Sex',
        'email' => 'Email',
        'lasttime' => 'LastLoginTime',
        'code' => 'WintoName',

    );

    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
        

       
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
       array('AddTime', 'formatAddtime', 1, 'callback'),
       array('listorder', 0,1),
       array('LoginTimes', 0,1),
       array('GroupID', '20051131014553209',1),
       array('GroupName', '#lazyme#经销商',1),
       array('Password', 'formatPassword', 1, 'callback'),
       array('flag', 1, 1),
    ); 
   

    protected function _initialize() {
        $this->_validate = array(
            array('MemName', 'require', 'name||用户名不能为空！'),
            array('MemName', '4,10', 'name||用户名请控制在4~10个字内',0,'length'),
            array('RealName', 'require', 'realname||真实姓名不能为空！'),
            array('WintoName', 'is_numeric', 'code||客戶號只許數字',0,'function', 3),
            array('WintoName', '', 'code||客戶號已存在', 1, 'unique', 3),
            array('Telephone', 'require', 'phone||电话不能为空！'),
            array('Password', 'require', 'password||密码不能为空！', 0, 'regex', 1),
            array('Password', '6,16', 'password||密码不得少于6位，不得大于16位',0,'length'),   
            array('MemName', '', 'name||帐号名称已经存在！', 1, 'unique', 3),
            array('pwdconfirm', 'Password', 'pwdconfirm||两次输入的密码不一样！', 0, 'confirm'),
            array('Sex', array('先生', '女士'), 'sex||姓别不正确', 2, 'in'),
        );

       
        if(defined('IN_ADMIN') && IN_ADMIN ){
           
        }else{
            array_push($this->_validate
                ,array('Address', 'require', 'address||请填写联系地址')
                ,array('Address', '8,110', 'address||地址字数不合要求',0,'length')
            );
          
        }


        parent::_initialize();
    }

    //dir
    public function formatAddtime() {
        return date("Y-m-d H:i:s");
    }

    public function formatPassword($str) {
        return $this->encryption($str,genRandomString(6));
    }

    /**
     * 根据提示符(MemName)和未加密的密码(密码为空时不参与验证)获取本地用户信息
     * @param type $identifier 为数字时，表示uid，其他为用户名
     * @param type $password 
     * @return 成功返回用户信息array()，否则返回布尔值false
     */
    public function getUser($identifier, $password = null ,$type = 0) {
        if (empty($identifier)) {
            return false;
        }
        $map = array();
        switch ($type) {
            case 1:
                $map['MemName'] = $identifier;
                break;
            case 2:
                $map['WintoName'] = $identifier;
                break;    
             case 0:
                $map['ID'] = $identifier;
                break; 
            default:
                if (is_int($identifier)) {
                    $map['ID'] = $identifier;                   
                } else {
                    $map['MemName'] = $identifier;
                }
                break;
        }
        

        $user = $this->where($map)->find();
        
        if (!$user) {
            return false;
        }
        $user['code'] = $user['WintoName'];
        unset($user['WintoName']);
        if ($password) {
            //验证本地密码是否正确
            //var_dump($this->encryption($password, $user['verify']));
            if ($this->encryption($password, $user['verify']) != $user['Password']) {
                return false;
            }
        }
        return $user;
    }

    /**
     * 对明文密码，进行加密，返回加密后的密码
     * @param $identifier 为数字时，表示uid，其他为用户名
     * @param type $pass 明文密码，不能为空
     * @return type 返回加密后的密码
     */
    public function encryption($pass, $verify = "") {
        //$pass = md5( md5($pass) . $verify);
        $md5password = md5(trim($pass));
        $md5password = substr($md5password,8,16);
        return $md5password;
    }

    /**
     * 检查用戶名是否唯一
     * @param type $data 數組也
     * @return boolean
     */
    public function checkUnique($data) { 
        if(isset($data['id'])&&intval($data['id'])>0){
            $map['ID'] = array('neq',$data['id']);
        }  
        if(isset($data['MemName']) && !empty($data['MemName'])){
            $map['MemName'] = $data['MemName'];
        } 
        if(isset($data['WintoName']) && !empty($data['WintoName'])){
            $map['WintoName'] = $data['WintoName'];
        } 
        if(!isset($map['MemName']) && !isset($map['WintoName'])){
            return false;
        }

        $count = $this->where($map)->count();        
        if($count>0){
            return 0;
        }else{
            return 1;
        }   
    }
    

    /*记录登入的相关IP及时间，关添加次数*/
    public function onLogined($id){
        if(intval($id)>0){
            $data['LastLoginTime'] =  date("Y-m-d H:i:s");
            $data['LastLoginIP'] =  get_client_ip();
            $data['LoginTimes'] = array('exp','LoginTimes+1');
            return $this->where(array("ID" => $id))->save($data);    
        }
        return false;
    }

}

