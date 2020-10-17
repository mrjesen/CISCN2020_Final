<?php

/**
 * 站点配置
 * 注意：不建议修改该文件！需要配置可以加到 addition.php中！
 * @author 水平凡 <admin@abc3210.com>
 */
return array(
    /* 项目设定 */
    'APP_STATUS' => 'debug', // 应用调试模式状态 调试模式开启后有效 默认为debug 可扩展 并自动加载对应的配置文件
    'APP_FILE_CASE' => true, // 是否检查文件的大小写 对Windows平台有效
    'APP_AUTOLOAD_PATH' => '@.TagLib', // 自动加载机制的自动搜索路径,注意搜索顺序
    'APP_TAGS_ON' => true, // 系统标签扩展开关
    /**
     * 提示（严重）：
     * 请不要修改此处，需要开启子域名部署，请在后台安装相应的域名绑定模块进行后台设置。
     */
    'APP_SUB_DOMAIN_DEPLOY' => false, // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES' => array(),
    'APP_SUB_DOMAIN_DENY' => array(), //  子域名禁用列表
    'APP_GROUP_LIST' => 'Contents,Admin,Member', // 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
    'APP_GROUP_MODE' => 1, // 分组模式 0 普通分组 1 独立分组，本项目不允许使用普通分组
    'APP_GROUP_PATH' => 'Modules', // 分组目录 独立分组模式下面有效
    /* 默认设定 */
    'DEFAULT_APP' => '@', // 默认项目名称，@表示当前项目
    'DEFAULT_MODULE' => 'Index', // 默认模块名称
    'DEFAULT_ACTION' => 'index', // 默认操作名称
    'DEFAULT_CHARSET' => 'utf-8', // 默认输出编码
    /* 数据库设置 */
    'DB_FIELDTYPE_CHECK' => true, // 是否进行字段类型检查
    'DB_FIELDS_CACHE' => true, // 启用字段缓存
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM' => 1, // 读写分离后 主服务器数量
    'DB_SQL_BUILD_CACHE' => false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE' => 'file', // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度
    /* SESSION相关设置 */
    'SESSION_AUTO_START' => true,
    'SESSION_TYPE' => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
    'VAR_SESSION_ID' => 'session_id', //sessionID的提交变量
    /* 表单令牌相关设置 */
    'TOKEN_RESET' => true, //令牌验证出错后是否重置令牌 默认为true
    "UPLOAD_FILE_RULE" => "uniqid", //上传文件名命名规则 例如可以是 time uniqid com_create_guid 等 必须是一个无需任何参数的函数名 可以使用自定义函数
    /* 数据缓存设置 */
    'DATA_CACHE_TIME' => 0, // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS' => false, // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK' => false, // 数据缓存是否校验缓存
    'DATA_CACHE_PATH' => TEMP_PATH, // 缓存路径设置 (仅对File方式缓存有效)
    /* 日志设置 */
    'LOG_RECORD' => true, // 默认不记录日志
    'LOG_TYPE' => 3, // 日志记录类型 0 系统 1 邮件 3 文件 4 SAPI 默认为文件方式
    'LOG_DEST' => '', // 日志记录目标
    'LOG_EXTRA' => '', // 日志记录额外信息
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR', // 允许记录的日志级别
    'LOG_FILE_SIZE' => 2097152, // 日志文件大小限制
    'LOG_EXCEPTION_RECORD' => false, // 是否记录异常信息日志
    /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE' => 'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR' => APP_PATH . 'Modules/Admin/Tpl/error.php', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => APP_PATH . 'Modules/Admin/Tpl/success.php', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => THINK_PATH . 'Tpl/think_exception.tpl', // 异常页面的模板文件
    'TMPL_FILE_DEPR' => '/',
    "DEFAULT_THEME" => "", //默认的模板主题名
    "TMPL_STRIP_SPACE" => false, //是否去除模板文件里面的html空格与换行
    'TMPL_TEMPLATE_SUFFIX' => '.php', //模板后缀
    /* 路由规则配置 */
    'URL_ROUTER_ON' => false, //是否开启路由
    'URL_ROUTE_RULES' => array(),
    /* 系统变量名称设置  提示：请不要修改，否则出现未知问题 */
    'VAR_GROUP' => 'g', // 默认分组获取变量
    'VAR_MODULE' => 'm', // 默认模块获取变量
    'VAR_ACTION' => 'a', // 默认操作获取变量
    'VAR_AJAX_SUBMIT' => 'ajax', // 默认的AJAX提交变量
    'VAR_PATHINFO' => 's', // PATHINFO 兼容模式获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
    'VAR_URL_PARAMS' => '_URL_', // PATHINFO URL参数变量
    /* RBAC 提示：请不要修改。 */
    "USER_AUTH_ON" => true, //是否开启权限认证
    "USER_AUTH_TYPE" => 1, //默认认证类型 1 登录认证 2 实时认证
    "USER_AUTH_KEY" => "UserID", //用户认证SESSION标记，用于保存登陆后用户ID
    'ADMIN_AUTH_KEY' => 'administrator', //高级管理员无需进行权限认证$_SESSION['administrator']=true;
    "REQUIRE_AUTH_MODULE" => "", //需要认证模块
    "NOT_AUTH_MODULE" => "Public", //无需认证模块
    "USER_AUTH_GATEWAY" => "", //认证网关
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'USER_AUTH_MODEL' => 'User', //用户信息表
    /* 自定义配置 */
    "SHUIPF_FIELDS_PATH" => LIB_PATH . "Fields/", //字段地址
    "UPLOADFILEPATH" => SITE_PATH . "/d/file/", //上传附件路径
    /* 标签库 */
    'TAGLIB_BUILD_IN' => 'cx,shuipf',
    'DATA_PATH_LEVEL' => 0,//子目录缓存级别
);

