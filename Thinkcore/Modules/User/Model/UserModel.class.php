<?php

/**
 * 后台管理用户模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class UserModel extends RelationModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('username', 'require', '用户名不能为空！'),
        array('realname', 'require', '真实姓名不能为空！'),
        array('role_id', 'require', '帐号所属角色不能为空！', 0, 'regex', 1),
        array('password', 'require', '密码不能为空！', 0, 'regex', 1),
        //array('password', array(6,28), '密码长度太短！',1,'length'),
        //array('email', 'email', '邮箱地址有误！'),
        array('username', '', '帐号名称已经存在！', 1, 'unique', 1),
        array('username,id', 'checkUsernameUnique', '帐号名称已经存在！', 1, 'callback', 2),
        array('pwdconfirm', 'password', '两次输入的密码不一样！', 0, 'confirm'),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
    );
    //关联定义
    protected $_link = array(
        //和角色吧关联，一对一
        'User_Role' => array(
            "mapping_type" => HAS_ONE,
            //关联表名
            "class_name" => "Role_user",
            "foreign_key" => "user_id",
        ),
    );

    /**
     * 对明文密码，进行加密，返回加密后的密码
     * @param $identifier 为数字时，表示uid，其他为用户名
     * @param type $pass 明文密码，不能为空
     * @return type 返回加密后的密码
     */
    public function encryption($identifier, $pass, $verify = "") {
        $v = array();
        if (is_numeric($identifier)) {
            $v["id"] = $identifier;
        } else {
            $v["username"] = $identifier;
        }
        $pass = md5( md5($pass) . $verify);
        return $pass;
    }

    /**
     * 检查用戶名是否唯一
     * @param type $data 數組也
     * @return boolean
     */
    public function checkUsernameUnique($data) {         
        $map['id'] = array('neq',$data['id']);
        $map['username'] = $data['username'];
        $count = $this->where($map)->count();        
        if($count>0){
            return false;
        }else{
            return true;
        }
        
    }



    /**
     * 根据标识修改对应用户密码
     * @param type $identifier
     * @param type $password
     * @return type 
     */
    public function ChangePassword($identifier, $password) {
        if (empty($identifier) || empty($password)) {
            return false;
        }
        $term = array();
        if (is_int($identifier)) {
            $term['id'] = $identifier;
        } else {
            $term['username'] = $identifier;
        }
        $verify = genRandomString();
        $data['verify'] = $verify;
        $data['password'] = $this->encryption($identifier, $password, $verify);
        $up = $this->where($term)->save($data);
        if ($up !== false) {
            return true;
        }
        return false;
    }

    /**
     * 编辑用户信息
     * @param type $data
     * @return boolean
     */
    public function editUser($data) {
        if (empty($data) || !isset($data['id'])) {
            $this->error = '数据不能为空！';
            return false;
        }
        //角色Id
        $id = (int) $data['id'];
        
        //取得原本用户信息
        $userInfo = $this->where(array("id" => $id))->getField('id,verify');
        if (empty($userInfo)) {
            $this->error = '该用户不存在！';
            return false;
        }
        C('TOKEN_ON',false);
        $data = $this->create($data, 2);
        //角色id
        $role_id = $data['role_id'];
        if ($data) {
            //密码
            $password = $data['password'];
            if (!empty($password)) {
                //生成随机认证码
                $data['verify'] = genRandomString(6);
                //进行加密
                $pass = $this->encryption($id, $password, $data['verify']);
                $data['password'] = $pass;
            } else {
                unset($data['password']);
            }
            if ($this->where(array('id' => $id))->save($data) !== false) {
                if (!empty($role_id)) {
                    M("Role_user")->where(array("user_id" => $id))->save(array("role_id" => $role_id));
                }
                return true;
            } else {
                //echo($this->getLastSql());
                $this->error = '更新失败！';
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 添加管理员
     * @param type $data
     * @return boolean
     */
    public function addUser($data) {
        if (empty($data)) {
            $this->error = '数据不能为空！';
            return false;
        }
        //检验数据
        $data = $this->create($data, 1);
        if ($data) {
            //生成随机认证码
            $data['verify'] = genRandomString(6);
            //利用认证码和明文密码加密得到加密后的
            $data['password'] = $this->encryption(0, $data['password'], $data['verify']);
            $id = $this->add($data);
            if ($id) {
                return $id;
            } else {
                $this->error = '添加用户失败！';
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除管理员
     * @param int $userId
     * @return boolean
     */
    public function delUser($userId) {
        $userId = (int) $userId;
        if (empty($userId)) {
            $this->error = '请指定需要删除的用户ID！';
            return false;
        }
        if ($userId == 1) {
            $this->error = '该管理员不能被删除！';
            return false;
        }
        if (false !== $this->where(array('id' => $userId))->delete()) {
            return true;
        } else {
            $this->error = '删除失败！';
            return false;
        }
    }

   /**
     * 取得用户名
     * @param int $userId
     * @return string
     */
    public function getUserName($userId,$field="realname"){
        $userId = (int) $userId;
        if (empty($userId)) {
            $this->error = 'Lost userid';
            return false;
        }
        if(!in_array($field,array('realname','username','nickname'))){
            $field = "realname";
        }
        return $this->where(array('id'=>$userId))->getField($field);
    }

    /**
     * 取得用户信息
     * @param int $userId
     * @return array
     */
    public function getUserInfo($userId){
        $userId = (int) $userId;
        if (empty($userId)) {
            $this->error = 'Lost userid';
            return false;
        }
        return $this->where(array('id'=>$userId))->find();
    }

    /**
     * 插入成功后的回调方法
     * @param type $data
     * @param type $options
     */
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        //添加一条记录到 Role_user 表
        M("Role_user")->add(array("role_id" => $data['role_id'], "user_id" => $data['id']));
    }

    /**
     * 删除成功后的回调方法
     * @param type $data
     * @param type $options
     */
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        M("Role_user")->where(array("user_id" => $data['id']))->delete();
    }

}