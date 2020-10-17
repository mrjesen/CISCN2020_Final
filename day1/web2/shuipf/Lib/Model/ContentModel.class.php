<?php

/**
 * 内容模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ContentModel extends RelationModel {

    /**
     *  静态成品变量 保存全局实例
     *  @access private
     */
    static private $_instance = NULL;
    //当前模型id
    protected $modelid = 0;
    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array();
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array();

    /**
     * 取得内容模型实例
     * @param type $modelid 模型ID
     * @return obj
     */
    static public function getInstance($modelid) {
        if (is_null(self::$_instance[$modelid]) || !isset(self::$_instance[$modelid])) {
            $modelCache = F("Model");
            if (empty($modelCache[$modelid])) {
                return false;
            }
            $tableName = $modelCache[$modelid]['tablename'];
            self::$_instance[$modelid] = new self(ucwords($tableName));
            //内容模型
            if ($modelCache[$modelid]['type'] == 0) {
                self::$_instance[$modelid]->_validate = array(
                    //栏目
                    array('catid', 'require', '请选择栏目！', 1, 'regex', 1),
                    array('catid', 'isUltimate', '该模型非终极栏目，无法添加信息！', 1, 'callback', 1),
                    //标题
                    array('title', 'require', '标题必须填写！', 1, 'regex', 1),
                );
            }
            //设置模型id
            self::$_instance[$modelid]->modelid = $modelid;
            //初始化关联定义
            self::$_instance[$modelid]->relationShipsDefine($tableName);
        }
        return self::$_instance[$modelid]->relation(self::$_instance[$modelid]->getRelationName($tableName));
    }

    /**
     * 关联定义
     * @param array $tableName 关联定义条件。
     * 如果是数组，直接定义配置好的关联条件，如果是字符串，则当作表名进行定义一对一关联条件！
     */
    public function relationShipsDefine($tableName) {
        if (is_array($tableName)) {
            $this->_link = $tableName;
        } else {
            $tableName = ucwords($tableName);
            //进行内容表关联定义
            $this->_link = array(
                //主表 附表关联
                $this->getRelationName($tableName) => array(
                    "mapping_type" => HAS_ONE,
                    "class_name" => $tableName . "_data",
                    "foreign_key" => "id"
                ),
            );
        }
        return $this->_link;
    }

    /**
     * 获取关联定义名称
     * @param type $tableName 表名
     * @return type
     */
    public function getRelationName($tableName = '') {
        if (empty($tableName)) {
            $tableName = $this->name;
        }
        return ucwords($tableName) . 'Data';
    }

    /**
     * 对通过连表查询的数据进行合并处理
     * @param type $data
     */
    public function dataMerger(&$data) {
        $relationName = $this->getRelationName();
        $datafb = $data[$relationName];
        unset($data[$relationName]);
        if (is_array($datafb)) {
            $data = array_merge($data, $datafb);
        }
        return $data;
    }

    /**
     * 创建数据对象 但不保存到数据库
     * @access public
     * @param mixed $data 创建数据
     * @param string $type 状态
     * @param string $name 关联名称
     * @return mixed
     */
    public function create($data = '', $type = '', $name = true) {
        // 如果没有传值默认取POST数据
        if (empty($data)) {
            $data = $_POST;
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        }
        // 验证数据
        if (empty($data) || !is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        //关联定义
        $relation = $this->_link;
        //验证规则
        $_validate = $this->_validate;
        //自动完成
        $_auto = $this->_auto;
        if (!empty($relation)) {
            // 遍历关联定义
            foreach ($relation as $key => $val) {
                // 操作制定关联类型
                $mappingName = $val['mapping_name'] ? $val['mapping_name'] : $key; // 映射名称
                if (empty($name) || true === $name || $mappingName == $name || (is_array($name) && in_array($mappingName, $name))) {
                    //关联类名
                    $mappingClass = !empty($val['class_name']) ? $val['class_name'] : $key;
                    //关联类型
                    $mappingType = !empty($val['mapping_type']) ? $val['mapping_type'] : $val;
                    switch ($mappingType) {
                        case HAS_ONE:
                            //是否有副表数据
                            $isLinkData = false;
                            //数据
                            if (isset($data[$mappingName])) {
                                $sideTablesData = $data[$mappingName];
                                unset($data[$mappingName]);
                                $isLinkData = true;
                            }
                            //自动验证
                            if (isset($_validate[$mappingName])) {
                                $_validateSideTables = $_validate[$mappingName];
                                unset($_validate[$mappingName], $this->_validate[$mappingName]);
                            }
                            //自动完成
                            if (isset($_auto[$mappingName])) {
                                $_autoSideTables = $_auto[$mappingName];
                                unset($_auto[$mappingName], $this->_auto[$mappingName]);
                            }
                            //进行主表create
                            if ($type == 1) {
                                $data = parent::create($data, $type);
                            } else {
                                if (empty($data)) {
                                    $data = true;
                                    if (empty($sideTablesData)) {
                                        $this->error = L('_DATA_TYPE_INVALID_');
                                        return false;
                                    }
                                } else {
                                    $data = parent::create($data, $type);
                                }
                                //存在主键副表也自动加上
                                if (!empty($data[$this->getPk()])) {
                                    $sideTablesData[$this->getPk()] = $data[$this->getPk()];
                                }
                            }
                            //下面进行的是副表验证操作，这里需要检查特殊情况，例如没有开启关联的，其实不用进行下面
                            if (empty($this->options['link']) || empty($isLinkData)) {
                                return $data;
                            }
                            //关闭表单验证
                            C('TOKEN_ON', false);
                            //不管成功或者失败，清空_validate和_auto
                            $this->_validate = $this->_auto = array();
                            if ($data) {
                                if (empty($sideTablesData)) {
                                    return $data;
                                } else {
                                    $sideTablesData = M($mappingClass)->validate($_validateSideTables)->auto($_autoSideTables)->create($sideTablesData, $type);
                                    if ($sideTablesData) {
                                        if (is_array($data)) {
                                            return array_merge($data, array($mappingName => $sideTablesData));
                                        } else {
                                            return array($mappingName => $sideTablesData);
                                        }
                                    } else {
                                        $this->error = M($mappingClass)->getError();
                                        return false;
                                    }
                                }
                            } else {
                                return false;
                            }
                            break;
                        case BELONGS_TO://不支持
                            return parent::create($data, $type);
                            break;
                        case HAS_MANY://不支持
                            return parent::create($data, $type);
                            break;
                        case MANY_TO_MANY://不支持
                            return parent::create($data, $type);
                            break;
                    }
                }
            }
        } else {
            return parent::create($data, $type);
        }
    }

    /**
     * 对验证过的token进行复原
     * @param type $data 数据
     */
    public function tokenRecovery($data = array()) {
        if (empty($data)) {
            $data = $_POST;
        }
        //TOKEN_NAME
        $tokenName = C('TOKEN_NAME');
        if (empty($data[$tokenName])) {
            return false;
        }
        list($tokenKey, $tokenValue) = explode('_', $data[$tokenName]);
        //如果验证失败，重现对TOKEN进行复原生效
        $_SESSION[$tokenName][$tokenKey] = $tokenValue;
        return true;
    }

    /**
     * 自动表单处理
     * @access public
     * @param array $data 创建数据
     * @param string $type 创建类型
     * @return mixed
     */
    private function autoOperation(&$data, $type) {
        $options = $this->options;
        $autoOperation = parent::autoOperation($data, $type);
        $this->options = $options;
        return $autoOperation;
    }

    /**
     * 自动表单验证
     * @access protected
     * @param array $data 创建数据
     * @param string $type 创建类型
     * @return boolean
     */
    protected function autoValidation($data, $type) {
        $options = $this->options;
        $autoValidation = parent::autoValidation($data, $type);
        $this->options = $options;
        return $autoValidation;
    }

    /**
     * 添加验证规则
     * @param array $validate 规则
     * @param type $issystem 是否主表
     * @param type $name 关联名称
     * @return type
     */
    public function addValidate(array $validate, $issystem = true, $name = true) {
        $relation = $this->_link;
        if (!empty($relation)) {
            // 遍历关联定义
            foreach ($relation as $key => $val) {
                // 操作制定关联类型
                $mappingName = $val['mapping_name'] ? $val['mapping_name'] : $key; // 映射名称
                if (empty($name) || true === $name || $mappingName == $name || (is_array($name) && in_array($mappingName, $name))) {
                    //关联类名
                    $mappingClass = !empty($val['class_name']) ? $val['class_name'] : $key;
                    if ($issystem) {
                        $this->_validate[] = $validate;
                    } else {
                        $this->_validate[$mappingName][] = $validate;
                    }
                }
            }
        }
        return $this->_validate;
    }

    /**
     * 添加自动完成
     * @param array $validate 规则
     * @param type $issystem 是否主表
     * @param type $name 关联名称
     * @return type
     */
    public function addAuto(array $auto, $issystem = true, $name = true) {
        $relation = $this->_link;
        if (!empty($relation)) {
            // 遍历关联定义
            foreach ($relation as $key => $val) {
                // 操作制定关联类型
                $mappingName = $val['mapping_name'] ? $val['mapping_name'] : $key; // 映射名称
                if (empty($name) || true === $name || $mappingName == $name || (is_array($name) && in_array($mappingName, $name))) {
                    //关联类名
                    $mappingClass = !empty($val['class_name']) ? $val['class_name'] : $key;
                    if ($issystem) {
                        $this->_auto[] = $validate;
                    } else {
                        $this->_auto[$mappingName][] = $validate;
                    }
                }
            }
        }
        return $this->_auto;
    }

    /**
     * 是否终极栏目
     * @param type $catid
     * @return boolean
     */
    public function isUltimate($catid) {
        if (empty($catid)) {
            return false;
        }
        if (getCategory($catid) == false) {
            return false;
        } else if (getCategory($catid, 'child')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 信息锁定
     * @param type $catid 栏目ID
     * @param type $id 信息ID
     * @param type $userid 用户名ID
     * @param type $username 用户名
     * @return type
     */
    public function locking($catid, $id, $userid = 0) {
        $db = M("Locking");
        $time = time();
        //锁定有效时间
        $Lock_the_effective_time = 300;
        if (empty($userid)) {
            $userid = AppframeAction::$Cache["uid"];
        }
        $where = array();
        $where['catid'] = array("EQ", $catid);
        $where['id'] = array("EQ", $id);
        $where['locktime'] = array("EGT", $time - $Lock_the_effective_time);
        $info = $db->where($where)->find();
        if ($info && $info['userid'] != AppframeAction::$Cache["uid"]) {
            $this->error = 'o(︶︿︶)o 唉，该信息已经被用户【<font color=\"red\">' . $info['username'] . '</font>】锁定~请稍后在修改！';
            return false;
        }
        //删除失效的
        $where = array();
        $where['locktime'] = array("LT", $time - $Lock_the_effective_time);
        $db->where($where)->delete();
        return true;
    }

    /**
     * 检查表是否存在 
     * $table 不带表前缀
     */
    public function table_exists($table) {
        $tables = $this->list_tables();
        return in_array(C("DB_PREFIX") . $table, $tables) ? true : false;
    }

    /**
     * 读取全部表名
     */
    public function list_tables() {
        $tables = array();
        $data = $this->query("SHOW TABLES");
        foreach ($data as $k => $v) {
            $tables[] = $v['Tables_in_' . C("DB_NAME")];
        }
        return $tables;
    }

}
