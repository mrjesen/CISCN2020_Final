<?php

/* * 
 * 敏感词过滤处理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class FilterService {
    
    //审核状态
    const statusCheck = -1;
    //拒绝状态
    const statusRefuse = 0;
    //替换状态
    const statusReplace = -2;
    //通过
    const statusPass = 1;

    //错误信息
    public $error = "";
    //匹配到的敏感关键字
    public $words_found;
    //敏感词数组
    //filter：替换关键字，banned：禁止关键字，mod：审核关键字
    public $censor_words = array();

    function __construct($censor_words = false) {
        if ($censor_words) {
            $this->censor_words = $censor_words;
        } else {
            $this->censor_words = F("Censor_words");
        }
    }

    /**
     * 高亮显示
     * @param type $message 内容
     * @param type $badwords_regex 敏感词正则
     * @return type
     */
    public function highlight($message, $badwords_regex) {
        $color = $this->highlight;
        if (empty($color)) {
            return $message;
        }
        $message = preg_replace($badwords_regex, '<span style="color: ' . $color . ';">\\1</span>', $message);
        return $message;
    }

    /**
     * 敏感词检测处理
     * @param type $message 需要检查的内容
     * @return boolean 返回 1 检测通过，0表示不通过，-1表示需要审核 2 替换关键词
     */
    public function check(&$message) {
        $bbcodes = 'b|i|color|size|font|align|list|indent|email|hide|quote|code|free|table|tr|td|img|swf|attach|payto|float';
        //禁止关键字处理
        if (is_array($this->censor_words['banned']) && !empty($this->censor_words['banned'])) {
            foreach ($this->censor_words['banned'] as $banned_words) {
                foreach ($this->censor_words['banned'] as $banned_words) {
                    if (preg_match_all($banned_words, @preg_replace(array("/\[($bbcodes)=?.*\]/iU", "/\[\/($bbcodes)\]/i"), '', $message), $matches)) {
                        //匹配到的关键字
                        $this->words_found = $matches[0];
                        //移除重复
                        $this->words_found = array_unique($this->words_found);
                        //高亮敏感词
                        $message = $this->highlight($message, $banned_words);
                        $this->error = "抱歉，您填写的内容包含不良信息【" . $this->words_found[0] . "】而无法提交！";
                        return self::statusRefuse;
                    }
                }
            }
        }

        //审核关键字
        if (is_array($this->censor_words['mod']) && !empty($this->censor_words['mod'])) {
            foreach ($this->censor_words['mod'] as $mod_words) {
                if (preg_match_all($mod_words, @preg_replace(array("/\[($bbcodes)=?.*\]/iU", "/\[\/($bbcodes)\]/i"), '', $message), $matches)) {
                    //匹配到的关键字
                    $this->words_found = $matches[0];
                    //移除重复
                    $this->words_found = array_unique($this->words_found);
                    //高亮敏感词
                    $message = $this->highlight($message, $mod_words);
                    $this->error = "抱歉，您填写的内容包含敏感关键字【" . $this->words_found[0] . "】需要进行管理员审核！";
                    return self::statusCheck;
                }
            }
        }

        //替换关键词
        $limitnum = 1000;
        if (!empty($this->censor_words['filter'])) {
            $i = 0;
            while ($find_words = array_slice($this->censor_words['filter']['find'], $i, $limitnum)) {
                if (empty($find_words))
                    break;
                $replace_words = array_slice($this->censor_words['filter']['replace'], $i, $limitnum);
                $i += $limitnum;
                $message = preg_replace($find_words, $replace_words, $message);
            }
            return self::statusReplace;
        }
        return self::statusPass;
    }

    /**
     *  获取错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }

}

?>
