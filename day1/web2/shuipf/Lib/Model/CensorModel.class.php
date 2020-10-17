<?php

/**
 * 关键词过滤模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CensorModel extends CommonModel {

    protected $tableName = 'censor_word';

    /**
     * 添加分类
     * @param type $name 
     */
    public function addTerms($name) {
        $db = M("Terms");
        $name = trim($name);
        if (empty($name)) {
            return false;
        }
        $count = $db->where(array("name" => $name, "module" => "censor"))->count();
        if ($count > 0) {
            return false;
        }
        return $status = $db->add(array("name" => $name, "module" => "censor"));
    }

    /**
     * 生成敏感词缓存 filter：替换关键字，banned：禁止关键字，mod：审核关键字
     * @return string 关键词数据
     */
    public function censorword_cache() {
        $banned = $mod = array();
        $bannednum = $modnum = 0;
        $data = array('filter' => array(), 'banned' => '', 'mod' => '');
        foreach ($this->select() as $censor) {
            if (preg_match('/^\/(.+?)\/$/', $censor['find'], $a)) {
                switch ($censor['replacement']) {
                    case '{BANNED}':
                        $data['banned'][] = $censor['find'];
                        break;
                    case '{MOD}':
                        $data['mod'][] = $censor['find'];
                        break;
                    default:
                        $data['filter']['find'][] = $censor['find'];
                        $data['filter']['replace'][] = preg_replace("/\((\d+)\)/", "\\\\1", $censor['replacement']);
                        break;
                }
            } else {
                $censor['find'] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($censor['find'], '/'));
                switch ($censor['replacement']) {
                    case '{BANNED}':
                        $banned[] = $censor['find'];
                        $bannednum++;
                        if ($bannednum == 1000) {
                            $data['banned'][] = '/(' . implode('|', $banned) . ')/i';
                            $banned = array();
                            $bannednum = 0;
                        }
                        break;
                    case '{MOD}':
                        $mod[] = $censor['find'];
                        $modnum++;
                        if ($modnum == 1000) {
                            $data['mod'][] = '/(' . implode('|', $mod) . ')/i';
                            $mod = array();
                            $modnum = 0;
                        }
                        break;
                    default:
                        $data['filter']['find'][] = '/' . $censor['find'] . '/i';
                        $data['filter']['replace'][] = $censor['replacement'];
                        break;
                }
            }
        }

        if ($banned) {
            $data['banned'][] = '/(' . implode('|', $banned) . ')/i';
        }
        if ($mod) {
            $data['mod'][] = '/(' . implode('|', $mod) . ')/i';
        }
        //过滤关键词数据
        F("Censor_words", $data);
        //分类数据
        $typedata = M("Terms")->where(array("module" => "censor"))->select();
        F("Censor_type", $typedata);
        return $data;
    }

}

?>
