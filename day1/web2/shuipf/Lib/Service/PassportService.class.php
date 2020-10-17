<?php

/**
 * 通行证服务
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class PassportService {

    //操作句柄
    protected $handler;
    //参数
    protected $options = array();
    //网站配置参数
    protected $config = array();
    //错误信息
    public $error = null;

    /**
     * 连接
     * @access public
     * @param array $options  配置数组
     * @return object
     */
    public static function connect($options = array()) {
        //判断模块是否安装
        if (false == isModuleInstall('Member')) {
            return get_instance_of('PassportService');
        }
        //网站配置
        $config = F("Member_Config");
        if ($config['interface']) {
            $type = $config['interface'];
        } else {
            $type = 'Local';
        }
        //附件存储方案
        $type = trim($type);
        $class = 'Passport' . ucwords($type);
        import("Driver.Passport.{$class}", LIB_PATH);
        if (class_exists($class))
            $Atta = new $class($options);
        else
            throw_exception('无法加载通行证:' . $type);
        return $Atta;
    }

    /**
     * 用户积分变更
     * @param type $uid 数字为用户ID，其他为用户名
     * @param type $integral 正数增加积分，负数扣除积分
     * @return int 成功返回当前积分数，失败返回false，-1 表示当前积分不够扣除
     */
    public function user_integral($uid, $integral) {
        $map = array();
        if (is_numeric($uid)) {
            $map['userid'] = $uid;
        } else {
            $map['username'] = $uid;
        }
        if (empty($map)) {
            return false;
        }
        $member = D("Member");
        $info = $member->where($map)->find();
        if (empty($info)) {
            return false;
        }
        $point = $info['point'] + $integral;
        if ($point < 0) {
            return -1;
        }
        //计算会员组
        $groupid = $member->get_usergroup_bypoint((int) $point);
        //更新
        if (false !== $member->where($map)->save(array("point" => (int) $point, "groupid" => $groupid))) {
            return $point;
        }
        return false;
    }

    /**
     * 检验用户是否已经登陆
     */
    public function isLogged() {
        //获取cookie中的用户id
        $uid = $this->getCookieUid();
        if (empty($uid) || $uid < 1) {
            return false;
        }
        return $uid;
    }

    /**
     * 注册用户的登陆状态 (即: 注册cookie + 注册session + 记录登陆信息)
     * @param array $user 用户相信信息 uid , username
     * @param type $is_remeber_me 有效期
     * @return type 成功返回布尔值
     */
    public function registerLogin(array $user, $is_remeber_me = 604800) {
        $key = 'shuipfcms@' . $user['userid'];
        SiteCookie('shuipfuser', $key, (int) $is_remeber_me);
        return true;
    }

    /**
     * 注销登陆
     */
    public function logoutLocal() {
        // 注销cookie
        cookie("shuipfuser", null);
        return true;
    }

    /**
     * 获取cookie中记录的用户ID
     * @return type 成功返回用户ID，失败返回false
     */
    public function getCookieUid() {
        static $cookie_userid = null;
        if (isset($cookie_userid) && $cookie_userid) {
            return $cookie_userid;
        }
        $cookie = SiteCookie("shuipfuser");
        if (empty($cookie)) {
            return false;
        }
        $cookie = explode('@', $cookie);
        $cookie_userid = ($cookie[0] !== 'shuipfcms') ? false : $cookie[1];
        return $cookie_userid;
    }

    /**
     * 前台会员信息
     * 根据提示符(username)和未加密的密码(密码为空时不参与验证)获取本地用户信息，前后台公用方法
     * @param type $identifier 为数字时，表示uid，其他为用户名
     * @param type $password 
     * @return 成功返回用户信息array()，否则返回布尔值false
     */
    public function getLocalUser($identifier, $password = null) {
        return false;
    }

    /**
     * 使用本地账号登陆 (密码为null时不参与验证)
     * @param type $identifier 用户标识，用户uid或者用户名
     * @param type $password 用户密码，未加密，如果为空，不参与验证
     * @param type $is_remember_me cookie有效期
     * return 返回状态，大于 0:返回用户 ID，表示用户登录成功
     *                                     -1:用户不存在，或者被删除
     *                                     -2:密码错
     *                                     -3会员注册登陆状态失败
     */
    public function loginLocal($identifier, $password = null, $is_remember_me = 3600) {
        return false;
    }

    /**
     * 用户注册
     * @param type $username 用户名
     * @param type $password 明文密码
     * @param type $email
     * @param type $_data 附加数据
     * @return int 大于 0:返回用户 ID，表示用户注册成功
     *                              -1:用户名不合法
     *                              -2:包含不允许注册的词语
     *                              -3:用户名已经存在
     *                              -4:Email 格式有误
     *                              -5:Email 不允许注册
     *                              -6:该 Email 已经被注册
     */
    public function user_register($username, $password, $email, $_data = array()) {
        return false;
    }

    /**
     * 更新用户基本资料
     * @param type $username 用户名
     * @param type $oldpw 旧密码
     * @param type $newpw 新密码，如不修改为空
     * @param type $email Email，如不修改为空
     * @param type $ignoreoldpw 是否忽略旧密码
     * @param type $_data 附加数据
     * @return int 1:更新成功
     *                      0:没有做任何修改
     *                     -1:旧密码不正确
     *                     -4:Email 格式有误
     *                     -5:Email 不允许注册
     *                     -6:该 Email 已经被注册
     *                     -7:没有做任何修改
     *                     -8:该用户受保护无权限更改
     */
    public function user_edit($username, $oldpw, $newpw, $email, $ignoreoldpw = 0, $_data = array()) {
        return false;
    }

    /**
     *  删除用户
     * @param type $uid 用户名
     * @return int 1:成功
     *                      0:失败
     */
    public function user_delete($uid) {
        return false;
    }

    /**
     * 删除用户头像
     * @param type $uid 用户名
     * @return int 1:成功
     *                      0:失败
     */
    public function user_deleteavatar($uid) {
        return false;
    }

    /**
     * 检查 Email 地址
     * @param type $email 邮箱地址
     * @return int 1:成功
     *                      -4:Email 格式有误
     *                      -5:Email 不允许注册
     *                      -6:该 Email 已经被注册
     */
    public function user_checkemail($email) {
        return false;
    }

    /**
     * 检查用户名
     * @param type $username 用户名
     * @return int 1:成功
     *                      -1:用户名不合法
     *                      -2:包含要允许注册的词语
     *                      -3:用户名已经存在
     */
    public function user_checkname($username) {
        return false;
    }

    /**
     * 修改头像
     * @param type $uid 用户 ID
     * @param type $type 头像类型
     *                                       real:真实头像
     *                                       virtual:(默认值) 虚拟头像
     * @param type $returnhtml 是否返回 HTML 代码
     *                                                     1:(默认值) 是，返回设置头像的 HTML 代码
     *                                                     0:否，返回设置头像的 Flash 调用数组
     * @return string:返回设置头像的 HTML 代码
     *                array:返回设置头像的 Flash 调用数组
     */
    public function user_avatar($uid, $type = 'virtual', $returnhtml = 1) {
        return false;
    }

    /**
     * 获取用户头像 
     * @param type $uid 用户ID
     * @param int $format 头像规格，默认参数90，支持 180,90,45,30
     * @param type $dbs 该参数为true时，表示使用查询数据库的方式，取得完整的头像地址。默认false
     * @return type 返回头像地址
     */
    public function user_getavatar($uid, $format = 90, $dbs = false) {
        return false;
    }

    /**
     * 记录登陆信息
     * @param type $uid 用户ID
     */
    public function recordLogin($uid) {
        return true;
    }

}