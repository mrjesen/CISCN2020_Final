<?php

/**
 * 内容模型单独配置
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
return array(
    /* URL设置 */
    'URL_MODEL' => 0, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_DEPR' => '/', // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX' => '.html', // URL伪静态后缀设置
);
