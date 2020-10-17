<?php

/**
 * 输出tags内容
 * @param type $field 字段名
 * @param type $value 字段内容
 * @return type
 */
function tags($field, $value) {
    if (empty($value)) {
        return array();
    }
    //把Tags进行分割成数组
    $tags = strpos($value, ',') !== false ? explode(',', $value) : explode(' ', $value);
    //获取分页规则
    $urlrules = F("urlrules");
    $urlrules = $urlrules[AppframeAction::$Cache['Config']['tagurl']];
    if (empty($urlrules)) {
        $urlrules = 'index.php?g=Tags&tagid={$tagid}|index.php?g=Tags&tagid={$tagid}&page={$page}';
    }
    if (strstr($urlrules, '{$tagid}')) {
        $db = M("Tags");
    }
    $return = array();
    foreach ($tags as $k => $v) {
        $v = trim($v);
        if (!$v) {
            continue;
        }
        $replace_l = array(); //需要替换的标签
        $replace_r = array(); //替换的内容
        if (strstr($urlrules, '{$tagid}')) {
            $tagid = $db->where(array("tag" => $v))->getField("tagid");
            if ($tagid) {
                $replace_l[] = '{$tagid}';
                $replace_r[] = $tagid;
            }
        }
        if (strstr($urlrules, '{$tag}')) {
            $replace_l[] = '{$tag}';
            $replace_r[] = $v;
        }
        //标签替换
        $tagurlrules = str_replace($replace_l, $replace_r, $urlrules);
        $tagurlrules = explode("|", $tagurlrules);
        $parse_url = parse_url($tagurlrules[0]);
        $return[$k]['tag'] = $v;
        if (!isset($parse_url['host'])) {
            $return[$k]['url'] = CONFIG_SITEURL . $tagurlrules[0];
        } else {
            $return[$k]['url'] = $tagurlrules[0];
        }
    }
    return $return;
}