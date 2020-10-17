<?php

/**
 * File Name：CacheModel.class.php
 * File Encoding：UTF-8
 * File New Time：2014-3-30 19:56:51
 * Author：水平凡
 * Mailbox：admin@abc3210.com
 */
class CacheModel extends CommonModel {

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '缓存名称不能为空！', 1, 'regex', 3),
        array('model', 'require', '模型名称不能为空！', 1, 'regex', 3),
        array('action', 'require', '模型方法名称不能为空！', 1, 'regex', 3),
    );
    //自动完成
    protected $_auto = array(
        //array(填充字段,填充内容,填充条件,附加规则)
        array('system', 0),
    );

    /**
     * 添加需要的缓存队列
     * @param array $data
     * @return boolean
     */
    public function addCache(array $data) {
        if (empty($data)) {
            $this->error = '数据不能为空！';
            return false;
        }
        C('TOKEN_ON', false);
        $data = $this->create($data, 1);
        if ($data) {
            $id = $this->add($data);
            if ($id) {
                return $id;
            } else {
                $this->error = '添加失败！';
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除指定模块下的全部缓存队列
     * @param type $module 模块名称
     * @return boolean
     */
    public function deleteCacheModule($module) {
        if (empty($module)) {
            $this->error = '请指定模块！';
            return false;
        }
        if ($this->where(array('module' => $module, 'system' => 0))->delete() !== false) {
            return true;
        } else {
            $this->error = '删除失败！';
            return false;
        }
    }

    /**
     * 获取需要更新缓存列队
     * @param type $isup 是否强制获取最新
     * @return type
     */
    public function getCacheList($isup = false) {
        $cache = S('cache_list');
        if (empty($cache) || $isup) {
            //取得现在全部需要更新缓存的队列
            $cache = $this->order(array('id' => 'DESC'))->select();
            S('cache_list', $cache, 600);
        }
        return $cache;
    }

    /**
     * 执行更新缓存
     * @param array $config 缓存配置
     * @return boolean
     */
    public function runUpdate(array $config) {
        if (empty($config)) {
            $this->error = '没有可需要更新的缓存信息！';
            return false;
        }
        $mo = '';
        if (empty($config['module'])) {
            $mo = $config['model'];
        } else {
            $mo = $config['module'] . "/" . $config['model'];
        }
        $model = D($mo);
        if ($config['action']) {
            $action = $config['action'];
            $model->$action();
            return true;
        }
        return false;
    }

}
