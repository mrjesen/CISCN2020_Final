<?php

/**
 * 关联字段 表单组合处理
 * @param type $field 字段名
 * @param type $value 字段内容
 * @param type $fieldinfo 字段配置
 * @return type
 */
function linkfield($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);
    //表名
    $table_name = ucwords(str_replace(C("DB_PREFIX"),"",$setting['table_name']));
    //自动显示
    if ($setting['link_type']) {
        //数据模型
        $get_db = M($table_name);
        //返回字段
        $sel_tit = $setting['select_title'] ? $setting['select_title'] : '*';

        $sql = "SELECT " . $sel_tit . " FROM `" . $setting['table_name'] . "`";

        $dataArr = $get_db->field($sel_tit)->select();

        $value = str_replace('&amp;', '&', $value);
        $data = '<input type="text" name="info[' . $fieldinfo['field'] . ']" id="' . $fieldinfo['field'] . '" value="'.$value.'" class="input"  style="display:none;"><select id="set_' . $fieldinfo['field'] . '" onchange="$(\'#' . $fieldinfo['field'] . '\').val(this.value);"><option value="">请选择</option>';

        foreach ($dataArr as $v) {
            //存入数据方式
            if ($setting['insert_type'] == "id") {
                $output_type = $v[$setting['set_id']];
            } elseif ($setting['insert_type'] == "title") {
                $output_type = $v[$setting['set_title']];
            } else {
                $output_type = $v[$setting['set_title']] . '_' . $v[$setting['set_id']];
            }
            if ($output_type == $value)
                $select = 'selected';
            else
                $select = '';
            $data .= "<option value='" . $output_type . "' " . $select . ">" . $v[$setting['set_show']?$setting['set_show']:$setting['set_title']] . "</option>\n";
        }
        $data .= '</select>';
    }else {
        $key = urlencode(authcode("true", "", C("AUTHCODE"), 3600));
        $domain = CONFIG_SITEURL_MODEL;
        $set_title = $setting['set_show']?$setting['set_show']:$setting['set_title'];
        $data = <<<EOT
            <style type="text/css">
            .content_div{ margin-top:0px; font-size:14px; position:relative}
            #search_div{$field}{ position:absolute; top:23px; border:1px solid #dfdfdf; text-align:left; padding:1px; left:0px;*left:0px; width:263px;*width:260px; background-color:#FFF; display:none; font-size:12px;}
            #search_div{$field} li{ line-height:24px;cursor:pointer}
            #search_div{$field} li a{  padding-left:6px;display:block}
            #search_div{$field} li a:hover, #search_div{$field} li:hover{ background-color:#e2eaff}
            </style>
            <div class="content_div">
                <input type="text" size="41" id="cat_search{$field}" value="" onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;" class='input'><input name="info[{$fieldinfo['field']}]" id="{$fieldinfo['field']}" type="hidden" class='input' value="{$value}" size="41"/>
                <ul id="search_div{$field}"></ul>
            </div>		
            <script type="text/javascript" language="javascript" >
                // 赋值字段,主键
                function setvalue{$field}(title,id,set_title)
                {
                    var title = title;
                    var id = id;
                    var type = "{$setting['insert_type']}";
                    if(type == "id")
                    {
                        $("#{$fieldinfo['field']}").val(id);
                    }
                    else if(type == "title")
                    {
                        $("#{$fieldinfo['field']}").val(title);
                    }
                    else if(type == "title_id")
                    {
                        $("#{$fieldinfo['field']}").val(title+'|'+id);
                    }
                    $("#cat_search{$field}").val(set_title);
                    $('#search_div{$field}').hide();
                }
				
            $(document).ready(function(){
				if($("#{$fieldinfo['field']}").val().length > 0){
				
					var value = $("#{$fieldinfo['field']}").val();//字段值
					var tablename = '{$setting['table_name']}';//表
					var select_title = '{$setting['select_title']}';//返回字段
					var set_title = '{$setting['set_title']}';//赋值字段
					var set_id = '{$setting['set_id']}';//主键
					var set_type = '{$setting['insert_type']}';//存入数据方式
					$.getJSON('{$domain}api.php?m=Ajax_linkfield&a=public_index&act=check_search&key={$key}&callback=?', {value: value,table_name: tablename,set_title: set_title,set_id: set_id,set_type: set_type,select_title:select_title,random:Math.random()}, function(data2){
						if (data2 != null) {
                                                                                                                                $('#cat_search{$field}').val(data2.{$set_title});
						} else {
							$('#search_div{$field}').hide();
						}
					});
					
				}

				$('#cat_search{$field}').keyup(function(){
					var value = $("#cat_search{$field}").val();//搜索值
					var tablename = '{$setting['table_name']}';//表
					var select_title = '{$setting['select_title']}';//返回字段
					var like_title = '{$setting['like_title']}';//条件字段
					var set_where = '{$setting['set_where']}';//条件 like eq
					var set_title = '{$setting['set_title']}';//赋值字段
					var set_id = '{$setting['set_id']}';//主键
					
					if (value.length > 0){
						$.getJSON('{$domain}api.php?m=Ajax_linkfield&a=public_index&act=search_ajax&key={$key}&callback=?', {value: value,table_name: tablename,select_title: select_title,like_title: like_title,set_where:set_where,set_title: set_title,set_id: set_id,limit: 20,random:Math.random()}, function(data){
							if (data != null) {
								var str = '';
								$.each(data, function(i,n){
									str += '<li onclick=\'setvalue{$field}("'+n.{$setting['set_title']}+'","'+n.{$setting['set_id']}+'","'+n.{$set_title}+'");\'>'+n.{$set_title}+'</li>';
								});
								$('#search_div{$field}').html(str);
								$('#search_div{$field}').show();
							} else {
								$('#search_div{$field}').hide();
							}
						});
					} else {
						$('#search_div{$field}').hide();
					} 
				});	
            })
            </script>
EOT;
    }
    return $data;
}