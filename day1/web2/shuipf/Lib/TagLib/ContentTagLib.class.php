<?php

/**
 * 内容标签
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ContentTagLib {

    public $db, $table_name, $modelid, $where;

    /**
     * 组合查询条件
     * @param type $attr
     * @return type
     */
    public function where($attr) {
        $where = array();
        //设置SQL where 部分
        if (isset($attr['where']) && $attr['where']) {
            $where['_string'] = $attr['where'];
        }
        //栏目id条件
        if (isset($attr['catid']) && (int) $attr['catid']) {
            $catid = (int) $attr['catid'];
            if (getCategory($catid, 'child')) {
                $catids_str = getCategory($catid, 'arrchildid');
                $pos = strpos($catids_str, ',') + 1;
                $catids_str = substr($catids_str, $pos);
                $where['catid'] = array("IN", $catids_str);
            } else {
                $where['catid'] = array("EQ", $catid);
            }
        }
        //缩略图
        if (isset($attr['thumb'])) {
            if ($attr['thumb']) {
                $where['thumb'] = array("NEQ", "");
            } else {
                $where['thumb'] = array("EQ", "");
            }
        }
        //审核状态
        $where['status'] = array("EQ", 99);
        $this->where = $where;
        return $this->where;
    }

    /**
     * 初始化模型
     * @param $catid
     * @param $tablename
     */
    public function set_modelid($catid = 0, $tablename = false) {
        if ($catid) {
            if (getCategory($catid, 'type') && getCategory($catid, 'type') != 0) {
                return false;
            }
            $this->modelid = getCategory($catid, 'modelid');
            if (empty($tablename)) {
                $tablename = ucwords(getModel($this->modelid, 'tablename'));
            }
        }
        $this->table_name = $tablename;
        return $this->db = M($this->table_name);
    }

    /**
     * 统计
     */
    public function count($data) {
        if ($data['action'] == 'lists') {
            $this->set_modelid($data['catid']);
            return $this->db->where($this->where($data))->count();
        }
    }

    /**
     * 内容列表（lists）
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID
     * where	 否	 null	 sql语句的where部分
     * thumb	 否	 0	 是否仅必须缩略图
     * order	 否	 null	 排序类型
     * num	 是	 null	 数据调用数量
     * moreinfo	 否	 0	 是否调用副表数据 1为是
     * 
     * moreinfo参数属性，本参数表示在返回数据的时候，会把副表中的数据也一起返回。一个内容模型分为2个表，一个主表一个副表，主表中一般是保存了标题、所属栏目等等短小的数据（方便用于索引），而副表则保存了大字段的数据，如内容等数据。在模型管理中新建字段的时候，是允许你选择存入到主表还是副表的（我们推荐的是，把不重要的信息放到副表中）。
     * @param $data
     */
    public function lists($data) {
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = to_guid_string($data);
        if ($cache && $return = S($cacheID)) {
            return $return;
        }
        if (!$data['catid']) {
            return false;
        }
        $this->set_modelid($data['catid']);
        $this->where($data);
        //判断是否启用分页，如果没启用分页则显示指定条数的内容
        if (!isset($data['limit'])) {
            $data['limit'] = (int) $data['num'] == 0 ? 10 : (int) $data['num'];
        }
        //排序
        if (empty($data['order'])) {
            $data['order'] = array("updatetime" => "DESC", "id" => "DESC");
        }
        $dataList = $this->db->where($this->where)->limit($data['limit'])->order($data['order'])->select();
        //把数据组合成以id为下标的数组集合
        if ($dataList) {
            $return = array();
            foreach ($dataList as $r) {
                $return[$r['id']] = $r;
            }
        } else {
            return false;
        }
        $getLastSql .= $this->db->getLastSql() . "|";
        //调用副表的数据
        if (isset($data['moreinfo']) && intval($data['moreinfo']) == 1) {
            $ids = array();
            foreach ($return as $v) {
                if (isset($v['id']) && !empty($v['id'])) {
                    $ids[] = $v['id'];
                } else {
                    continue;
                }
            }
            if (!empty($ids)) {
                //从新初始化模型
                $this->set_modelid(0, $this->table_name . '_data');
                $where = array();
                $where['id'] = array("IN", $ids);
                $r = $this->db->where($where)->select();
                $getLastSql .= $this->db->getLastSql() . "|";
                if (!empty($r)) {
                    foreach ($r as $v) {
                        if (isset($return[$v['id']])) {
                            $return[$v['id']] = array_merge($v, $return[$v['id']]);
                        }
                    }
                }
            }
        }
        //结果进行缓存
        if ($cache) {
            S($cacheID, $return, $cache);
        }
        //log
        if (APP_DEBUG) {
            $msg = "ContentTagLib标签->lists：参数：catid=$catid ,modelid=$modelid ,order=" . $data['order'] . " ,
            SQL:" . $getLastSql;
            Log::write($msg);
        }
        return $return;
    }

    /**
     * 排行榜标签
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID，只支持单栏目
     * where	 否	 null	 sql语句的where部分
     * modelid 否              null              模型ID
     * day	 否	 0	 调用多少天内的排行
     * order	 否	 null	 排序类型（本月排行- monthviews DESC 、本周排行 - weekviews DESC、今日排行 - dayviews DESC）
     * num	 是	 null	 数据调用数量
     * @param $data
     */
    public function hits($data) {
        $catid = intval($data['catid']);
        $modelid = intval($data['modelid']);
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = to_guid_string($data);
        if ($cache && $array = S($cacheID)) {
            return $array;
        }
        //初始化模型
        if ($modelid) {
            $this->modelid = $modelid;
            $tablename = ucwords(getModel($this->modelid, 'tablename'));
            $this->table_name = $tablename;
            $this->db = M($this->table_name);
        } elseif ($catid) {
            $this->set_modelid($catid);
        } else {
            return false;
        }

        $desc = $ids = '';
        $where = $array = array();
        //设置SQL where 部分
        if (isset($data['where']) && $data['where']) {
            $where['_string'] = $data['where'];
        }
        //排序
        $order = $data['order'];
        if (!$order) {
            $order = array('views' => 'DESC');
        }
        //条数
        $num = (int) $data['num'];
        if ($num < 1) {
            $num = 10;
        }
        if ($catid) {
            $where['catid'] = array("EQ", $catid);
        }
        //如果调用的栏目是存在子栏目的情况下
        if ($catid && getCategory($catid, 'child')) {
            $catids_str = getCategory($catid, 'arrchildid');
            $pos = strpos($catids_str, ',') + 1;
            $catids_str = substr($catids_str, $pos);
            $where['catid'] = array("IN", $catids_str);
        }
        //模型id条件
        if ($modelid) {
            $where['modelid'] = array("EQ", $modelid);
        }
        //调用多少天内
        if (isset($data['day'])) {
            $updatetime = time() - intval($data['day']) * 86400;
            $where['updatetime'] = array("GT", $updatetime);
        }

        $hits = array();
        $data = $this->db->where($where)->order($order)->limit($num)->select();
        foreach ($data as $r) {
            $array[$r['id']] = $r;
        }

        //结果进行缓存
        if ($cache) {
            S($cacheID, $array, $cache);
        }

        //log
        if (APP_DEBUG) {
            $msg = "ContentTagLib标签->hits：参数：catid=$catid ,modelid=$modelid ,order=$order ,
            SQL:" . $this->db->getLastSql();
            Log::write($msg);
        }
        return $array;
    }

    /**
     * 相关文章标签
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID
     * where	 否	 null	 sql语句的where部分
     * nid	 否	 null	 排除id 一般是 $id，排除当前文章
     * relation	 否	 $relation	 无需更改
     * keywords	 否	 null	 内容页面取值：$rs[keywords]
     * num	 是	 null	 数据调用数量
     * @param $data
     */
    public function relation($data) {
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = to_guid_string($data);
        if ($cache && $key_array = S($cacheID)) {
            return $key_array;
        }
        $catid = intval($data['catid']);
        if (!$catid) {
            return false;
        }
        if (!$this->set_modelid($catid)) {
            return false;
        }
        //调用数量
        $data['num'] = (int) $data['num'];
        if (!$data['num']) {
            $data['num'] = 10;
        }
        $where = array();
        //设置SQL where 部分
        if (isset($data['where']) && $data['where']) {
            $where['_string'] = $data['where'];
        }
        $where['status'] = array("EQ", 99);
        $order = $data['order'];
        $limit = $data['nid'] ? $data['num'] + 1 : $data['num'];
        //数据
        $key_array = array();
        $number = 0;
        //根据手动添加的相关文章
        if ($data['relation']) {
            $relations = explode('|', $data['relation']);
            $relations = array_diff($relations, array(null));
            $relations = implode(',', $relations);
            $where['id'] = array("IN", $relations);
            $_key_array = $this->db->where($where)->limit($limit)->order($order)->select();
            foreach ($_key_array as $r) {
                $key_array[$r['id']] = $r;
            }
            $number = count($key_array);
            //删除id条件
            if (isset($where['id'])) {
                unset($where['id']);
            }
            $getLastSql .= $this->db->getLastSql() . "|";
        }

        if ($data['keywords'] && $limit > $number) {//根据关键字的相关文章
            $limit = ($limit - $number <= 0) ? 0 : ($limit - $number);
            $keywords_arr = $data['keywords'];
            if ($keywords_arr && !is_array($keywords_arr)) {
                if (strpos($data['keywords'], ',') === false) {
                    $keywords_arr = explode(' ', $data['keywords']);
                } else {
                    $keywords_arr = explode(',', $data['keywords']);
                }
            }
            $i = 1;
            foreach ($keywords_arr as $_k) {
                $_k = str_replace('%', '', $_k);
                $where['keywords'] = array("LIKE", '%' . $_k . '%');
                $_r = $this->db->where($where)->limit($limit)->order($order)->select();
                //数据重组
                $r = array();
                foreach ($_r as $rs) {
                    $r[$rs['id']] = $rs;
                }
                $getLastSql .= $this->db->getLastSql() . "|";
                $number += count($r);
                foreach ($r as $id => $v) {
                    if ($i <= $data['num'] && !in_array($id, $key_array)) {
                        $key_array[$v['id']] = $v;
                    }
                    $i++;
                }
                if ($data['num'] < $number)
                    break;
            }
            unset($where['keywords']);
        }

        //去除排除信息
        if ($data['nid']) {
            unset($key_array[$data['nid']]);
        }

        //差额补齐
        if (count($key_array) < $data['num']) {
            $difference = $data['num'] - count($key_array);
            if ($difference) {
                $where['catid'] = $catid;
                //进行随机读取
                $count = $this->db->where($where)->count();
                $rand = mt_rand(1, $count - 1 < 1 ? 1 : $count - 1);
                $differenceList = $this->db->where($where)->limit($rand, $difference)->select();
                foreach ($differenceList as $r) {
                    $key_array[$r['id']] = $r;
                }
            }
        }

        //结果进行缓存
        if ($cache) {
            S($cacheID, $key_array, $cache);
        }

        //log
        if (APP_DEBUG) {
            $msg = "ContentTagLib标签->relation：参数：catid=$catid ,order=$order ,
            SQL:" . $getLastSql;
            Log::write($msg);
        }

        return $key_array;
    }

    /**
     * 栏目列表（category）
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 0	 调用该栏目下的所有栏目 ，默认0，调用一级栏目
     * order	 否	 null	 排序方式、一般按照listorder ASC排序，即栏目的添加顺序
     * @param $data
     */
    public function category($data) {
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = to_guid_string($data);
        if ($cache && $array = S($cacheID)) {
            return $array;
        }
        $data['catid'] = intval($data['catid']);
        $where = $array = array();
        //设置SQL where 部分
        if (isset($data['where']) && $data['where']) {
            $where['_string'] = $data['where'];
        }
        $db = M("Category");
        $num = (int) $data['num'];
        if (isset($data['catid'])) {
            $where['ismenu'] = array("EQ", 1);
            $where['parentid'] = array("EQ", $data['catid']);
        }
        //如果条件不为空，进行查库
        if (!empty($where)) {
            if ($num) {
                $categorys = $db->where($where)->limit($num)->order($data['order'])->select();
            } else {
                $categorys = $db->where($where)->order($data['order'])->select();
            }
        }
        //结果进行缓存
        if ($cache) {
            S($cacheID, $categorys, $cache);
        }
        return $categorys;
    }

}
