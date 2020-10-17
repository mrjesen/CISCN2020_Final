<?php

/**
 * 项目扩展函数库
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
// 实例化服务
function service($name, $params = array()) {
    $class = $name . 'Service';
    import("Service.{$class}", LIB_PATH);
    switch ($name) {
        case 'Attachment'://附件
            $params = empty($params) ? $params : array($params);
            return get_instance_of($class, 'connect', $params);
            break;
        case 'Passport'://通行证
            $params = empty($params) ? $params : array($params);
            return get_instance_of($class, 'connect', $params);
            break;
        default:
            return get_instance_of($class, '', $params);
            break;
    }
    return false;
}

//调用TagLib类
function TagLib($name, $params = array()) {
    $class = $name . 'TagLib';
    import("TagLib.{$class}", LIB_PATH);
    return get_instance_of($class, '', $params);
}

//Cookie 设置、获取、删除 
function SiteCookie($name, $value = '', $option = null) {
    // 默认设置
    $config = array(
        'prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
        'path' => C('COOKIE_PATH'), // cookie 保存路径
        'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        $value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; // 获取指定Cookie
        return authcode($value, "DECODE", C("AUTHCODE"));
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            //$value 加密
            $value = authcode($value, "", C("AUTHCODE"));
            // 设置cookie
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

/**
 * 检查模块是否已经安装
 * @param type $moduleName 模块名称
 * @return boolean
 */
function isModuleInstall($moduleName) {
    $appCache = F('App');
    if (isset($appCache[$moduleName])) {
        return true;
    }
    return false;
}

/**
 * 调试，用于保存数组到txt文件 正式生产删除
 * 用法：array2file($info, SITE_PATH.'/post.txt');
 * @param type $array
 * @param type $filename
 */
function array2file($array, $filename) {
    if (defined("APP_DEBUG") && APP_DEBUG) {
        //修改文件时间
        file_exists($filename) or touch($filename);
        if (is_array($array)) {
            $str = var_export($array, TRUE);
        } else {
            $str = $array;
        }
        return file_put_contents($filename, $str);
    }
    return false;
}

/**
 * 获取用户头像 
 * @param type $uid 用户ID
 * @param int $format 头像规格，默认参数90，支持 180,90,45,30
 * @param type $dbs 该参数为true时，表示使用查询数据库的方式，取得完整的头像地址。默认false
 * @return type 返回头像地址
 */
function getavatar($uid, $format = 90, $dbs = false) {
    return service("Passport")->user_getavatar($uid, $format, $dbs);
}

/**
 * 邮件发送
 * @param type $address 接收人 单个直接邮箱地址，多个可以使用数组
 * @param type $title 邮件标题
 * @param type $message 邮件内容
 */
function SendMail($address, $title, $message) {
    if (CONFIG_MAIL_PASSWORD == "") {
        return false;
    }
    import('PHPMailer');
    try {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet = C("DEFAULT_CHARSET");
        $mail->IsHTML(true);
        // 添加收件人地址，可以多次使用来添加多个收件人
        if (is_array($address)) {
            foreach ($address as $k => $v) {
                if (is_array($v)) {
                    $mail->AddAddress($v[0], $v[1]);
                } else {
                    $mail->AddAddress($v);
                }
            }
        } else {
            $mail->AddAddress($address);
        }
        // 设置邮件正文
        $mail->Body = $message;
        // 设置邮件头的From字段。
        $mail->From = CONFIG_MAIL_FROM;
        // 设置发件人名字
        $mail->FromName = CONFIG_MAIL_FNAME;
        // 设置邮件标题
        $mail->Subject = $title;
        // 设置SMTP服务器。
        $mail->Host = CONFIG_MAIL_SERVER;
        // 设置为“需要验证”
        if (CONFIG_MAIL_AUTH == '1') {
            $mail->SMTPAuth = true;
        } else {
            $mail->SMTPAuth = false;
        }
        // 设置用户名和密码。
        $mail->Username = CONFIG_MAIL_USER;
        $mail->Password = CONFIG_MAIL_PASSWORD;
        return $mail->Send();
    } catch (phpmailerException $e) {
        return $e->errorMessage();
    }
}

/**
 * 使用递归的方式删除
 * @param type $value
 * @return type
 */
function stripslashes_deep($value) {
    if (is_array($value)) {
        $value = array_map('stripslashes_deep', $value);
    } elseif (is_object($value)) {
        $vars = get_object_vars($value);
        foreach ($vars as $key => $data) {
            $value->{$key} = stripslashes_deep($data);
        }
    } else {
        $value = stripslashes($value);
    }

    return $value;
}

/**
 * 加密解密
 * @param type $string 明文 或 密文  
 * @param type $operation DECODE表示解密,其它表示加密  
 * @param type $key 密匙  
 * @param type $expiry 密文有效期  
 * @return string 
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;
    // 密匙
    $key = md5(($key ? $key : C("AUTHCODE")));
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确  
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性  
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性  
        // 验证数据有效性，请看未加密明文的格式  
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码  
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 生成上传附件验证
 * @param $args   参数
 */
function upload_key($args) {
    $auth_key = md5(C("AUTHCODE") . $_SERVER['HTTP_USER_AGENT']);
    $authkey = md5($args . $auth_key);
    return $authkey;
}

/**
 * 产生随机字符串 
 * 产生一个指定长度的随机字符串,并返回给用户 
 * @access public 
 * @param int $len 产生字符串的位数 
 * @return string 
 */
function genRandomString($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱 
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 字符截取
 * @param $string 需要截取的字符串
 * @param $length 长度
 * @param $dot
 */
function str_cut($sourcestr, $length, $dot = '...') {
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); //字符串的字节数 
    while (($n < $length) && ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码 
        if ($ascnum >= 224) {//如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符         
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符 
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } else {//其他情况下，包括小写字母和半角标点符号，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1;            //实际的Byte数计1个
            $n = $n + 0.5;        //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > strlen($returnstr)) {
        $returnstr = $returnstr . $dot; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 * flash上传初始化
 * 初始化swfupload上传中需要的参数
 * @param $module 模块名称
 * @param $catid 栏目id
 * @param $args 传递参数
 * @param $userid 用户id
 * @param $groupid 用户组id 默认游客
 * @param $isadmin 是否为管理员模式
 */
function initupload($module, $catid, $args, $userid, $groupid = 8, $isadmin = false) {
    if (empty($module)) {
        return false;
    }
    //检查用户是否有上传权限
    if ($isadmin) {
        //后台用户
        //上传大小
        $file_size_limit = intval(CONFIG_UPLOADMAXSIZE);
        //上传处理地址
        $upload_url = U('Attachment/Admin/swfupload');
    } else {
        //前台用户
        $Member_group = F("Member_group");
        if ((int) $Member_group[$groupid]['allowattachment'] < 1 || empty($Member_group)) {
            return false;
        }
        //上传大小
        $file_size_limit = intval(CONFIG_QTUPLOADMAXSIZE);
        //上传处理地址
        $upload_url = U('Attachment/Upload/swfupload');
    }
    //当前时间戳
    $sess_id = time();
    //生成验证md5
    $swf_auth_key = md5(C("AUTHCODE") . $sess_id . ($isadmin ? 1 : 0));

    //同时允许的上传个数, 允许上传的文件类型, 是否允许从已上传中选择, 图片高度, 图片宽度,是否添加水印1是
    if (!is_array($args)) {
        //如果不是数组传递，进行分割
        $args = explode(',', $args);
    }

    //参数补充完整
    if (empty($args[1])) {
        //如果允许上传的文件类型为空，启用网站配置的 uploadallowext
        if ($isadmin) {
            $args[1] = CONFIG_UPLOADALLOWEXT;
        } else {
            $args[1] = CONFIG_QTUPLOADALLOWEXT;
        }
    }
    //允许上传后缀处理
    $arr_allowext = explode('|', $args[1]);
    foreach ($arr_allowext as $k => $v) {
        $v = '*.' . $v;
        $array[$k] = $v;
    }
    $upload_allowext = implode(';', $array);

    //上传个数
    $file_upload_limit = (int) $args[0] ? (int) $args[0] : 8;
    //swfupload flash 地址
    $flash_url = CONFIG_SITEURL_MODEL . 'statics/js/swfupload/swfupload.swf';

    $init = 'var swfu_' . $module . ' = \'\';
    $(document).ready(function(){
        Wind.use("swfupload",GV.DIMAUB+"statics/js/swfupload/handlers.js",function(){
            swfu_' . $module . ' = new SWFUpload({
                flash_url:"' . $flash_url . '?"+Math.random(),
                upload_url:"' . $upload_url . '",
                file_post_name : "Filedata",
                post_params:{
                    "sessid":"' . $sess_id . '",
                    "module":"' . $module . '",
                    "catid":"' . $catid . '",
                    "uid":"' . $userid . '",
                    "isadmin":"' . $isadmin . '",
                    "groupid":"' . $groupid . '",
                    "watermark_enable":"' . intval($args[5]) . '",
                    "thumb_width":"' . intval($args[3]) . '",
                    "thumb_height":"' . intval($args[4]) . '",
                    "filetype_post":"' . $args[1] . '",
                    "swf_auth_key":"' . $swf_auth_key . '"
                  },
               file_size_limit:"' . $file_size_limit . 'KB",
               file_types:"' . $upload_allowext . '",
               file_types_description:"All Files",
               file_upload_limit:"' . $file_upload_limit . '",
               custom_settings : {progressTarget : "fsUploadProgress",cancelButtonId : "btnCancel"},
               button_image_url: "",
               button_width: 75,
               button_height: 28,
               button_placeholder_id: "buttonPlaceHolder",
               button_text_style: "",
               button_text_top_padding: 3,
               button_text_left_padding: 12,
               button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
               button_cursor: SWFUpload.CURSOR.HAND,
               file_dialog_start_handler : fileDialogStart,
               file_queued_handler : fileQueued,
               file_queue_error_handler:fileQueueError,
               file_dialog_complete_handler:fileDialogComplete,
               upload_progress_handler:uploadProgress,
               upload_error_handler:uploadError,
               upload_success_handler:uploadSuccess,
               upload_complete_handler:uploadComplete
        });
    });
})
';
    return $init;
}

/**
 * 取得文件扩展
 * @param type $filename 文件名
 * @return type 后缀
 */
function fileext($filename) {
    $pathinfo = pathinfo($filename);
    return $pathinfo['extension'];
}

/**
 * 返回附件类型图标
 * @param $file 附件名称
 * @param $type png为大图标，gif为小图标
 */
function file_icon($file, $type = 'png') {
    $ext_arr = array('doc', 'docx', 'ppt', 'xls', 'txt', 'pdf', 'mdb', 'jpg', 'gif', 'png', 'bmp', 'jpeg', 'rar', 'zip', 'swf', 'flv');
    $ext = fileext($file);
    if ($type == 'png') {
        if ($ext == 'zip' || $ext == 'rar')
            $ext = 'rar';
        elseif ($ext == 'doc' || $ext == 'docx')
            $ext = 'doc';
        elseif ($ext == 'xls' || $ext == 'xlsx')
            $ext = 'xls';
        elseif ($ext == 'ppt' || $ext == 'pptx')
            $ext = 'ppt';
        elseif ($ext == 'flv' || $ext == 'swf' || $ext == 'rm' || $ext == 'rmvb')
            $ext = 'flv';
        else
            $ext = 'do';
    }

    if (in_array($ext, $ext_arr)) {
        return CONFIG_SITEURL . 'statics/images/ext/' . $ext . '.' . $type;
    } else {
        return CONFIG_SITEURL . 'statics/images/ext/blank.' . $type;
    }
}

/**
 * 根据文件扩展名来判断是否为图片类型
 * @param type $file 文件名
 * @return type 是图片类型返回 true，否则返回 false
 */
function is_image($file) {
    $ext_arr = array('jpg', 'gif', 'png', 'bmp', 'jpeg', 'tiff');
    //取得扩展名
    $ext = fileext($file);
    return in_array($ext, $ext_arr) ? true : false;
}

/**
 * 安全过滤函数
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    $string = str_replace('\\', '', $string);
    return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
    if (!is_array($string))
        return stripslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_stripslashes($val);
    return $string;
}

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string) {
    if (!is_array($string))
        return addslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_addslashes($val);
    return $string;
}

/**
 * 生成SEO
 * @param $catid        栏目ID
 * @param $title        标题
 * @param $description  描述
 * @param $keyword      关键词
 */
function seo($catid = '', $title = '', $description = '', $keyword = '') {
    if (!empty($title))
        $title = strip_tags($title);
    if (!empty($description))
        $description = strip_tags($description);
    if (!empty($keyword))
        $keyword = str_replace(' ', ',', strip_tags($keyword));
    $site = F("Config");
    $cat = getCategory($catid);
    $seo['site_title'] = $site['sitename'];
    $titleKeywords = "";
    $seo['keyword'] = $keyword != $cat['setting']['meta_keywords'] ? (isset($keyword) && !empty($keyword) ? $keyword . (isset($cat['setting']['meta_keywords']) && !empty($cat['setting']['meta_keywords']) ? "," . $cat['setting']['meta_keywords'] : "") : $titleKeywords . (isset($cat['setting']['meta_keywords']) && !empty($cat['setting']['meta_keywords']) ? "," . $cat['setting']['meta_keywords'] : "")) : (isset($keyword) && !empty($keyword) ? $keyword : $cat['catname']);
    $seo['description'] = isset($description) && !empty($description) ? $description : $title . (isset($keyword) && !empty($keyword) ? $keyword : "");
    $seo['title'] = $cat['setting']['meta_title'] != $title ? ((isset($title) && !empty($title) ? $title . ' - ' : '') . (isset($cat['setting']['meta_title']) && !empty($cat['setting']['meta_title']) ? $cat['setting']['meta_title'] . ' - ' : (isset($cat['catname']) && !empty($cat['catname']) ? $cat['catname'] . ' - ' : ''))) : (isset($title) && !empty($title) ? $title . " - " : ($cat['catname'] ? $cat['catname'] . " - " : ""));
    foreach ($seo as $k => $v) {
        $seo[$k] = str_replace(array("\n", "\r"), '', $v);
    }
    return $seo;
}

/**
 * 获取模版文件 格式 项目://分组@主题/模块/操作
 * @param type $templateFile
 * @return boolean|string 
 */
function parseTemplateFile($templateFile = '') {
    static $TemplateFileCache = array();
    //模板路径
    $TemplatePath = TEMPLATE_PATH;
    //模板主题
    $Theme = empty(AppframeAction::$Cache["Config"]['theme']) ? 'Default' : AppframeAction::$Cache["Config"]['theme'];
    //如果有指定 GROUP_MODULE 则模块名直接是GROUP_MODULE，否则使用 GROUP_NAME，这样做的目的是防止其他模块需要生成
    $group = defined('GROUP_MODULE') ? GROUP_MODULE . '/' : GROUP_NAME . '/';
    C('TEMPLATE_NAME', $TemplatePath . $Theme . "/" . $group);
    //模板标识
    if ('' == $templateFile) {
        $templateFile = C('TEMPLATE_NAME') . MODULE_NAME . (defined('GROUP_NAME') ? C('TMPL_FILE_DEPR') : '/') . ACTION_NAME . C('TMPL_TEMPLATE_SUFFIX');
    }
    $key = md5($templateFile);
    if (isset($TemplateFileCache[$key])) {
        return $TemplateFileCache[$key];
    }
    if (false === strpos($templateFile, C('TMPL_TEMPLATE_SUFFIX'))) {
        // 解析规则为 模板主题:模块:操作 不支持 跨项目和跨分组调用
        $path = explode(':', $templateFile);
        $action = array_pop($path);
        $module = !empty($path) ? array_pop($path) : MODULE_NAME;
        if (!empty($path)) {// 设置模板主题
            $path = TEMPLATE_PATH . array_pop($path) . '/';
        } else {
            $path = C("TEMPLATE_NAME");
        }
        $depr = defined('GROUP_NAME') ? C('TMPL_FILE_DEPR') : '/';
        $templateFile = $path . $module . $depr . $action . C('TMPL_TEMPLATE_SUFFIX');
    }
    //区分大小写的文件判断，如果不存在，尝试一次使用默认主题
    if (!file_exists_case($templateFile)) {
        //记录日志
        $log = '模板:[' . $templateFile . ']不存在！';
        //启用默认主题模板
        $templateFile = str_replace(C("TEMPLATE_NAME"), TEMPLATE_PATH . 'Default/' . $group, $templateFile);
        //判断默认主题是否存在，不存在直接报错提示
        if (!file_exists_case($templateFile)) {
            $TemplateFileCache[$key] = false;
            Log::write($log);
            return false;
        }
    }
    $TemplateFileCache[$key] = $templateFile;
    return $templateFile;
}

/**
 * 分页处理
 * @staticvar array $_pageCache 静态变量
 * @param type $total 信息总数
 * @param type $size 每页数量
 * @param type $number 当前分页号（页码）
 * @param type $config 配置，会覆盖默认设置
 * @return \Page|array
 */
function page($total, $size = 0, $number = 0, $config = array()) {
    static $_pageCache = array();
    $cacheIterateId = to_guid_string(func_get_args());
    if (isset($_pageCache[$cacheIterateId])) {
        return $_pageCache[$cacheIterateId];
    }
    //配置
    $defaultConfig = array(
        //当前分页号
        'number' => $number,
        //接收分页号参数的标识符
        'param' => C("VAR_PAGE"),
        //分页规则
        'rule' => '',
        //是否启用自定义规则
        'isrule' => false,
        //分页模板
        'tpl' => '',
        //分页具体可控制配置参数默认配置
        'tplconfig' => array('listlong' => 6, 'listsidelong' => 2, "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""),
    );
    //分页具体可控制配置参数
    $cfg = array(
        //每次显示几个分页导航链接
        'listlong' => 6,
        //分页链接列表首尾导航页码数量，默认为2，html 参数中有”{liststart}”或”{listend}”时才有效
        'listsidelong' => 2,
        //分页链接列表
        'list' => '*',
        //当前页码的CSS样式名称，默认为”current”
        'currentclass' => 'current',
        //第一页链接的HTML代码，默认为 ”«”，即显示为 «
        'first' => '&laquo;',
        //上一页链接的HTML代码，默认为”‹”,即显示为 ‹
        'prev' => '&#8249;',
        //下一页链接的HTML代码，默认为”›”,即显示为 ›
        'next' => '&#8250;',
        //最后一页链接的HTML代码，默认为”»”,即显示为 »
        'last' => '&raquo;',
        //被省略的页码链接显示为，默认为”…”
        'more' => '...',
        //当处于首尾页时不可用链接的CSS样式名称，默认为”disabled”
        'disabledclass' => 'disabled',
        //页面跳转方式，默认为”input”文本框，可设置为”select”下拉菜单
        'jump' => '',
        //页面跳转文本框或下拉菜单的附加内部代码
        'jumpplus' => '',
        //跳转时要执行的javascript代码，用*代表页码，可用于Ajax分页
        'jumpaction' => '',
        //当跳转方式为下拉菜单时最多同时显示的页码数量，0为全部显示，默认为50
        'jumplong' => 50,
    );
    //覆盖配置
    if (!empty($config) && is_array($config)) {
        $defaultConfig = array_merge($defaultConfig, $config);
    }
    //每页显示信息数量
    $defaultConfig['size'] = $size ? $size : C("PAGE_LISTROWS");
    //把默认配置选项设置到tplconfig
    foreach ($cfg as $key => $value) {
        if (isset($defaultConfig[$key])) {
            $defaultConfig['tplconfig'][$key] = isset($defaultConfig[$key]) ? $defaultConfig[$key] : $value;
        }
    }
    import('Page');
    //是否启用自定义规则，规则是一个数组，index和list。不启用的情况下，直接以当前$_GET的参数组成地址
    if ($defaultConfig['isrule'] && empty($defaultConfig['rule'])) {
        //通过全局参数获取分页规则
        $URLRULE = $GLOBALS['URLRULE'] ? $GLOBALS['URLRULE'] : URLRULE;
        $PageLink = array();
        if (!is_array($URLRULE)) {
            $URLRULE = explode("~", $URLRULE);
        }
        $PageLink['index'] = $URLRULE['index'] ? $URLRULE['index'] : $URLRULE[0];
        $PageLink['list'] = $URLRULE['list'] ? $URLRULE['list'] : $URLRULE[1];
        $defaultConfig['rule'] = $PageLink;
    } else if ($defaultConfig['isrule'] && !is_array($defaultConfig['rule'])) {
        $URLRULE = explode('|', $defaultConfig['rule']);
        $PageLink = array();
        $PageLink['index'] = $URLRULE[0];
        $PageLink['list'] = $URLRULE[1];
        $defaultConfig['rule'] = $PageLink;
    }
    $Page = new Page($total, $defaultConfig['size'], $defaultConfig['number'], $defaultConfig['list'], $defaultConfig['param'], $defaultConfig['rule'], $defaultConfig['isrule']);
    $Page->SetPager('default', $defaultConfig['tpl'], $defaultConfig['tplconfig']);
    $_pageCache[$cacheIterateId] = $Page;
    return $_pageCache[$cacheIterateId];
}

/**
 * 取得URL地址中域名部分
 * @param type $url 
 * @return \url 返回域名
 */
function urlDomain($url) {
    if ($url) {
        $pathinfo = parse_url($url);
        return $pathinfo['scheme'] . "://" . $pathinfo['host'] . "/";
    }
    return false;
}

/**
 * 获取当前页面完整URL地址
 * @return type 地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 对URL中有中文的部分进行编码处理
 * @param type $url 地址 http://www.abc3210.com/s?wd=博客
 * @return type ur;编码后的地址 http://www.abc3210.com/s?wd=%E5%8D%9A%20%E5%AE%A2
 */
function cn_urlencode($url) {
    $pregstr = "/[\x{4e00}-\x{9fa5}]+/u"; //UTF-8中文正则
    if (preg_match_all($pregstr, $url, $matchArray)) {//匹配中文，返回数组
        foreach ($matchArray[0] as $key => $val) {
            $url = str_replace($val, urlencode($val), $url); //将转译替换中文
        }
        if (strpos($url, ' ')) {//若存在空格
            $url = str_replace(' ', '%20', $url);
        }
    }
    return $url;
}

/**
 *  通过用户邮箱，取得gravatar头像
 * @since 2.5
 * @param int|string|object $id_or_email 一个用户ID，电子邮件地址
 * @param int $size 头像图片的大小
 * @param string $default 如果没有可用的头像是使用默认图像的URL
 * @param string $alt 替代文字使用中的形象标记。默认为空白
 * @return string <img>
 */
function get_avatar($id_or_email, $size = '96', $default = '', $alt = false) {

    //头像大小
    if (!is_numeric($size))
        $size = '96';
    //邮箱地址
    $email = '';
    //如果是数字，表示使用会员头像 暂时没有写！
    if (is_int($id_or_email)) {
        $id = (int) $id_or_email;
        $userdata = service("Passport")->getLocalUser($id);
        $email = $userdata['email'];
    } else {
        $email = $id_or_email;
    }
    //设置默认头像
    if (empty($default)) {
        $default = 'mystery';
    }

    if (!empty($email))
        $email_hash = md5(strtolower($email));

    if (!empty($email))
        $host = sprintf("http://%d.gravatar.com", ( hexdec($email_hash[0]) % 2));
    else
        $host = 'http://0.gravatar.com';

    if ('mystery' == $default)
        $default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s={$size}"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
    elseif (!empty($email) && 'gravatar_default' == $default)
        $default = '';
    elseif ('gravatar_default' == $default)
        $default = "$host/avatar/s={$size}";
    elseif (empty($email))
        $default = "$host/avatar/?d=$default&amp;s={$size}";

    if (!empty($email)) {
        $out = "$host/avatar/";
        $out .= $email_hash;
        $out .= '?s=' . $size;
        $out .= '&amp;d=' . urlencode($default);

        $avatar = $out;
    } else {
        $avatar = $default;
    }

    return $avatar;
}

/**
 * 获取点击数
 * @param type $catid 栏目ID
 * @param type $id 信息ID
 * @return type
 */
function hits($catid, $id) {
    $tab = ucwords(getModel(getCategory($catid, 'modelid'), 'tablename'));
    return M($tab)->where(array("id" => $id))->getField("views");
}

/**
 * 标题链接获取
 * @param type $catid 栏目id
 * @param type $id 信息ID
 * @return type 链接地址
 */
function titleurl($catid, $id) {
    $tab = ucwords(getModel(getCategory($catid, 'modelid'), 'tablename'));
    return M($tab)->where(array("id" => $id))->getField("url");
}

/**
 * 获取文章评论总数
 * @param type $catid 栏目ID
 * @param type $id 信息ID
 * @return type 
 */
function commcount($catid, $id) {
    $comment_id = "c-$catid-$id";
    return M("Comments")->where(array("comment_id" => $comment_id, "parent" => 0, "approved" => 1))->count();
}

/**
 * 生成标题样式
 * @param $style   样式，通常时字段style，以“;”隔开，第一个是文字颜色，第二个是否加粗
 * @param $html    是否显示完整的STYLE样式代码
 */
function title_style($style, $html = 1) {
    $str = '';
    if ($html) {
        $str = ' style="';
    }
    $style_arr = explode(';', $style);
    if (!empty($style_arr[0])) {
        $str .= 'color:' . $style_arr[0] . ';';
    }
    if (!empty($style_arr[1])) {
        $str .= 'font-weight:' . $style_arr[1] . ';';
    }
    if ($html) {
        $str .= '" ';
    }
    return $style ? $str : "";
}

/**
 * 评论表情替换
 * @param type $content 评论内容
 * @param type $emotionPath 表情存放路径，以'/'结尾
 * @param type $classStyle 表情img附加样式
 * @return type
 */
function cReplaceExpression($content, $emotionPath = '', $classStyle = '') {
    D("Comments")->replaceExpression($content, $emotionPath, $classStyle);
    return $content;
}

/**
 * 根据tagid和tag获取url访问地址
 * @param type $tagid 
 * @param type $tag
 * @return type
 */
function getTagsUrl($tagid, $tag) {
    //获取分页规则
    $urlrules = F("urlrules");
    $urlrules = $urlrules[AppframeAction::$Cache['Config']['tagurl']];
    if (!$urlrules) {
        $urlrules = 'index.php?g=Tags&tagid={$tagid}|index.php?g=Tags&tagid={$tagid}&page={$page}';
    }
    $replace_l = array(); //需要替换的标签
    $replace_r = array(); //替换的内容
    if (strstr($urlrules, '{$tagid}')) {
        if ($tagid) {
            $replace_l[] = '{$tagid}';
            $replace_r[] = $tagid;
        }
    }
    if (strstr($urlrules, '{$tag}')) {
        $replace_l[] = '{$tag}';
        $replace_r[] = $tag;
    }
    //标签替换
    $tagurlrules = str_replace($replace_l, $replace_r, $urlrules);
    $tagurlrules = explode("|", $tagurlrules);
    $parse_url = parse_url($tagurlrules[0]);
    if (!isset($parse_url['host'])) {
        $url = CONFIG_SITEURL . $tagurlrules[0];
    } else {
        $url = $tagurlrules[0];
    }
    return $url;
}

/**
 * 生成缩略图
 * @param type $imgurl 图片地址
 * @param type $width 缩略图宽度
 * @param type $height 缩略图高度
 * @param type $thumbType 缩略图生成方式 1 按设置大小截取 0 按原图等比例缩略
 * @param type $smallpic 图片不存在时显示默认图片
 * @return type
 */
function thumb($imgurl, $width = 100, $height = 100, $thumbType = 0, $smallpic = 'nopic.gif') {
    static $_thumb_cache = array();
    if (empty($imgurl)) {
        return $smallpic;
    }
    //区分
    $key = md5($imgurl . $width . $height . $thumbType . $smallpic);
    if (isset($_thumb_cache[$key])) {
        return $_thumb_cache[$key];
    }
    if (!$width || !$height) {
        return $smallpic;
    }
    //当获取不到DOCUMENT_ROOT值时的操作！
    if (empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
    if (empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['PATH_TRANSLATED'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
    // 解析URLsitefileurl
    $imgParse = parse_url($imgurl);
    //图片路径
    $imgPath = $_SERVER['DOCUMENT_ROOT'] . $imgParse['path'];
    //取得文件名
    $basename = basename($imgurl);
    //取得文件存放目录
    $imgPathDir = str_replace($basename, '', $imgPath);
    //生成的缩略图文件名
    $newFileName = "thumb_{$width}_{$height}_" . $basename;
    //检查生成的缩略图是否已经生成过
    if (file_exists($imgPathDir . $newFileName)) {
        return str_replace($basename, $newFileName, $imgurl);
    }
    //检查文件是否存在，如果是开启远程附件的，估计就通过不了，以后在考虑完善！
    if (!file_exists($imgPath)) {
        return $imgurl;
    }
    //取得图片相关信息
    list($width_t, $height_t, $type, $attr) = getimagesize($imgPath);
    //判断生成的缩略图大小是否正常
    if ($width >= $width_t || $height >= $height_t) {
        return $imgurl;
    }
    //生成缩略图
    import('Image');
    if (1 == $thumbType) {
        Image::thumb2($imgPath, $imgPathDir . $newFileName, '', $width, $height, true);
    } else {
        Image::thumb($imgPath, $imgPathDir . $newFileName, '', $width, $height, true);
    }
    $_thumb_cache[$key] = str_replace($basename, $newFileName, $imgurl);
    return $_thumb_cache[$key];
}

/**
 * 检测一个数据长度是否超过最小值
 * @param type $value 数据
 * @param type $length 最小长度
 * @return type 
 */
function isMin($value, $length) {
    return mb_strlen($value, 'utf-8') >= (int) $length ? true : false;
}

/**
 * 检测一个数据长度是否超过最大值
 * @param type $value 数据
 * @param type $length 最大长度
 * @return type 
 */
function isMax($value, $length) {
    return mb_strlen($value, 'utf-8') <= (int) $length ? true : false;
}

/**
 * 对 javascript escape 解码
 * @param type $str 
 * @return type
 */
function unescape($str) {
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
            if ($val < 0x800)
                $ret .= chr(0xc0 | ($val >> 6)) .
                        chr(0x80 | ($val & 0x3f));
            else
                $ret .= chr(0xe0 | ($val >> 12)) .
                        chr(0x80 | (($val >> 6) & 0x3f)) .
                        chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
        if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        } else
            $ret .= $str[$i];
    }
    return $ret;
}

/**
 * 获取栏目相关信息
 * @param type $catid 栏目id
 * @param type $field 返回的字段，默认返回全部，数组
 * @param type $newCache 是否强制刷新
 * @return boolean
 */
function getCategory($catid, $field = '', $newCache = false) {
    if (empty($catid)) {
        return false;
    }
    $key = 'getCategory_' . $catid;
    //强制刷新缓存
    if ($newCache) {
        S($key, NULL);
    }
    $cache = S($key);
    if ($cache === 'false') {
        return false;
    }
    if (empty($cache)) {
        //读取数据
        $cache = M('Category')->where(array('catid' => $catid))->find();
        if (empty($cache)) {
            S($key, 'false', 60);
            return false;
        } else {
            //扩展配置
            $cache['setting'] = unserialize($cache['setting']);
            //栏目扩展字段
            $cache['extend'] = $cache['setting']['extend'];
            S($key, $cache, 3600);
        }
    }
    if ($field) {
        //支持var.property，不过只支持一维数组
        if (false !== strpos($field, '.')) {
            $vars = explode('.', $field);
            return $cache[$vars[0]][$vars[1]];
        } else {
            return $cache[$field];
        }
    } else {
        return $cache;
    }
}

/**
 * 获取模型数据
 * @param type $modelid 模型ID
 * @param type $field 返回的字段，默认返回全部，数组
 * @return boolean
 */
function getModel($modelid, $field = '') {
    if (empty($modelid)) {
        return false;
    }
    $key = 'getModel_' . $modelid;
    $cache = S($key);
    if ($cache === 'false') {
        return false;
    }
    if (empty($cache)) {
        //读取数据
        $cache = M('Model')->where(array('modelid' => $modelid))->find();
        if (empty($cache)) {
            S($key, 'false', 60);
            return false;
        } else {
            S($key, $cache, 3600);
        }
    }
    if ($field) {
        return $cache[$field];
    } else {
        return $cache;
    }
}
