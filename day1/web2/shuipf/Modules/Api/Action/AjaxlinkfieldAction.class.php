<?php

/**
 *  linkfield.php 异步获取数据
 * api.php?op=Ajaxlinkfield&act=search_ajax&value=nn&table_name=think_category&select_title=catid,catname&like_title=catname&set_title=catname
 */
class AjaxlinkfieldAction extends AdminbaseAction {

    function _initialize() {
        parent::_initialize();
        $key = authcode($this->_get("key"), "DECODE", C("AUTHCODE"));
        if ($key != "true") {
            exit;
        }
    }

    public function index() {
        $this->public_index();
    }

    public function public_index() {
        switch ($_GET['act']) {
            case 'search_ajax':
                $this->search_ajax();
                break;
            case 'check_search':
                $this->check_search();
                break;
            case 'search_data':
                $this->search_data();
                break;
        }
    }

    //搜索数据
    protected function search_ajax() {
        $data = array();
        if (I("get.value")) {
            $value = I("get.value",'','trim');//搜索值
            $table_name = I("get.table_name",'','trim');//表
            $table_name = ucwords(str_replace(C("DB_PREFIX"), "", $table_name));
            $select_title = I("get.select_title",'','trim');//返回字段
            $sel_tit = $select_title?$select_title:"*";
            $like_title = I("get.like_title",'','trim');//条件字段
            $set_where = I("get.set_where",'','trim');//条件 like eq
            $set_title = I("get.set_title",'','trim');//赋值字段
            $set_id = I("get.set_id",'','trim');//主键

            $limit = $_GET['limit'] ? trim($_GET['limit']) : '20';
            $where = array();
            if($set_where == "eq"){
                $where[$like_title] = $value;
            }else{
                $where[$like_title] = array("like","%$value%");
            }
            $db = M($table_name);

            $data = $db->where($where)->field($sel_tit)->order(array($set_id => "DESC"))->limit($limit)->select();

            echo $_GET['callback'] . '(', json_encode($data), ')';
        }
    }

    //搜索数据
    protected function check_search() {
        if (I("get.value")) {
            $value = I("get.value",'','trim');//值
            $table_name = I("get.table_name",'','trim'); //表
            $set_type = I("get.set_type",'','trim'); //存入数据方式
            $set_id = I("get.set_id",'','trim'); //主键
            $set_title = I("get.set_title",'','trim');//赋值字段
            //返回字段
            $sel_tit =  I("get.select_title",'*','trim');
            if (!$table_name || !$set_type || !$set_id || !$set_title) {
                exit;
            }
            //表名
            $table_name = ucwords(str_replace(C("DB_PREFIX"), "", $table_name));
            $get_db = M($table_name);
            //查询条件
            $sqls = array();
            if ($set_type == 'id') {//主键存储
                $sqls[$set_id] = $value;
            } elseif ($set_type == 'title') {//赋值字段
                $sqls[$set_title] = $value;
            } elseif ($set_type == 'title_id') {//赋值字段+主键
                $value = explode('_', $value);
                $sqls[$set_title] = $value[0];
                $sqls[$set_id] = $value[1];
            }
            //返回数据
            $dataArr = array();
            $dataArr = $get_db->field($sel_tit)->find();
            echo $_GET['callback'] . '(', json_encode($dataArr), ')';
        }
    }

    /**
     * 获取相应表字段列表 
     */
    protected function search_data() {

        //获取表名
        $tables = $_POST['tables'] ? $_POST['tables'] : trim($_GET['tables']);

        if ($tables) {
            $Common = D("Common");
            $tables = str_replace(C("DB_PREFIX"), "", $tables);
            $data = $Common->get_fields($tables);
            if ($data) {
                echo $_GET['callback'] . '(', json_encode($data), ')';
            } else {
                exit(0);
            }
        }
    }

}

?>