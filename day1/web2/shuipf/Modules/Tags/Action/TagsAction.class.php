<?php

/**
 * TAGS管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class TagsAction extends AdminbaseAction {

    /**
     * TAG列表 
     */
    public function index() {
        $db = M("Tags");
        $where = array();
        $count = $db->where($where)->count();
        $page = $this->page($count, 20);
        $data = $db->where($where)->order(array("listorder" => "DESC", "tagid" => "DESC"))->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign("data", $data);
        $this->display();
    }

    /**
     * 修改 
     */
    public function edit() {
        $db = D("Tags");
        if (IS_POST) {
            $_POST['tag'] = trim($_POST['tag']);
            if ($db->create()) {
                if ($db->save() !== false) {
                    M("Tags_content")->where(array("tag" => $_POST['_tag']))->data(array("tag" => $_POST['tag'],"lastusetime"=>  time()))->save();
                    $this->success("更新成功！", U("Tags/Tags/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($db->getError());
            }
        } else {
            $tagid = (int) $this->_get('tagid');
            if (!$tagid) {
                $this->error("缺少参数！");
            }
            $data = $db->where(array("tagid" => $tagid))->find();
            if ($data) {
                $this->assign($data);
            } else {
                $this->error("该TAG不存在！");
            }
            $this->display();
        }
    }

    /**
     * 删除 
     */
    public function delete() {
        $db = D("Tags");
        if (IS_POST) {
            $tagid = $_POST['tagid'];
            if (is_array($tagid)) {
                foreach ($tagid as $tid) {
                    $r = $db->where(array("tagid" => $tid))->find();
                    if ($r) {
                        $db->where(array("tagid" => $tid))->delete();
                        M("Tags_content")->where(array("tag" => $r['tag']))->delete();
                    }
                }
                $this->success("删除成功！");
            } else {
                $this->error("参数错误！");
            }
        } else {
            $tagid = (int) $this->_get('tagid');
            if (!$tagid) {
                $this->error("缺少参数！");
            }
            $r = $db->where(array("tagid" => $tagid))->find();
            if (!$r) {
                $this->error("该TAG不存在！");
            }
            $status = $db->where(array("tagid" => $tagid))->delete();
            if ($status) {
                M("Tags_content")->where(array("tag" => $r['tag']))->delete();
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * TAG重建 
     */
    public function create() {
        if (IS_POST || isset($_GET['modelid'])) {
            if (isset($_GET['modelid'])) {
                $modelid = (int) $this->_get('modelid');
            } else {
                $modelid = (int) $this->_post('modelid');
            }

            $_GET['modelid'] = $modelid;
            $lun = (int) $this->_get('lun'); //第几轮 0=>1
            if ($lun > (int) $_GET['zlun'] - 1) {
                $lun = (int) $_GET['zlun'] - 1;
            }
            $lun = $lun < 0 ? 0 : $lun;
            $mlun = 100;
            $firstRow = $mlun * ($lun < 0 ? 0 : $lun);
            
            $db = M("Tags_content");
            $tagdb = M("Tags");

            if (isset($_GET['delete'])) {
                $db->query('TRUNCATE TABLE  `' . C("DB_PREFIX") . 'tags_content`');
                $tagdb->query('TRUNCATE TABLE  `' . C("DB_PREFIX") . 'tags`');
            }
            unset($_GET['delete']);
            $model = F("Model");

            if ((int) $_GET['mo'] == 1) {
                
            } else {
                //模型总数
                $_GET['mocount'] = 1;
            }
            if (!$modelid) {
                $modelCONUT = M("Model")->count();
                $modelDATA = M("Model")->where(array("type"=>0))->order(array("modelid" => "ASC"))->find();
                $modelid = $modelDATA['modelid'];
                $_GET['mo'] = 1;
                $_GET['mocount'] = $modelCONUT;
                $_GET['modelid'] = $modelid;
            }

            $models_v = $model[$modelid];
            if (!is_array($models_v)) {
                $this->error("该模型不存在！");
                exit;
            }
            $count = M(ucwords($models_v['tablename']))->count();
            if ($count == 0) {
                if (isset($_GET['mo'])) {
                    $where = array();
                    $where['type'] = array("EQ",0);
                    $where['modelid'] = array("GT", $modelid);
                    $modelDATA = M("Model")->where($where)->order(array("modelid" => "ASC"))->find();
                    if (!$modelDATA) {
                        $this->success("TAG重建结束！", U('Tags/Tags/index'));
                        exit;
                    }
                    unset($_GET['zlun']);
                    unset($_GET['lun']);
                    $modelid = $modelDATA['modelid'];
                    $_GET['modelid'] = $modelid;
                    $this->assign("waitSecond", 200);
                    $this->success("模型：" . $models_v['name'] . "，第 " . ($lun + 1) . "/$zlun 轮更新成功，进入下一轮更新中...", U('Tags/Tags/create', $_GET));
                    exit;
                } else {
                    $this->error("该模型下没有信息！");
                    exit;
                }
            }
            //总轮数
            $zlun = ceil($count / $mlun);
            $_GET['zlun'] = $zlun;

            $this->createUP($models_v, $firstRow, $mlun);

            if ($lun == (int) $_GET['zlun'] - 1) {
                if (isset($_GET['mo'])) {
                    $where = array();
                    $where['type'] = array("EQ",0);
                    $where['modelid'] = array("GT", $modelid);
                    $modelDATA = M("Model")->where($where)->order(array("modelid" => "ASC"))->find();
                    if (!$modelDATA) {
                        $this->success("TAG重建结束！", U('Tags/Tags/index'));
                        exit;
                    }
                    unset($_GET['zlun']);
                    unset($_GET['lun']);
                    $modelid = $modelDATA['modelid'];
                    $_GET['modelid'] = $modelid;
                } else {
                    $this->success("TAG重建结束！", U('Tags/Tags/index'));
                    exit;
                }
            } else {
                $_GET['lun'] = $lun + 1;
            }

            $this->assign("waitSecond", 200);
            $this->success("模型：" . $models_v['name'] . "，第 " . ($lun + 1) . "/$zlun 轮更新成功，进入下一轮更新中...", U('Tags/Tags/create', $_GET));
            exit;
        } else {
            import('Form');
            $model = F("ModelType_0");
            $mo = array();
            foreach ($model as $k => $v) {
                $mo[$k] = $v['name'];
            }

            $this->assign("Model", $mo);
            $this->display();
        }
    }

    //数据重建
    protected function createUP($models_v, $firstRow, $mlun) {
        $db = M("Tags_content");
        $tagdb = M("Tags");
        $keywords = M(ucwords($models_v['tablename']))->where(array("status"=>99))->order(array("id" => "ASC"))->limit("$firstRow,$mlun")->getField("id,id,keywords,tags,url,title,catid,updatetime");
        foreach ($keywords as $keyword) {
            $data = array();
            $time = time();
            $key = strpos($keyword['tags'], ',') !== false ? explode(',', $keyword['tags']) : explode(' ', $keyword['tags']);
            foreach ($key as $key_v) {
                if (empty($key_v) || $key_v == "") {
                    continue;
                }

                $key_v = trim($key_v);
                if ($tagdb->where(array("tag" => $key_v))->getField('tagid')) {
                    $tagdb->where(array("tag" => $key_v))->setInc('usetimes');
                } else {
                    $tagdb->data(array(
                        "tag" => $key_v,
                        "usetimes" => 1,
                        "lastusetime" => $time,
                        "lasthittime" => $time,
                    ))->add();
                }
                $data[] = array(
                    'tag' => $key_v,
                    "url" => $keyword['url'],
                    "title" => $keyword['title'],
                    "modelid" => $models_v[modelid],
                    "contentid" => $keyword['id'],
                    "catid" => $keyword['catid'],
                    "updatetime" => $time,
                );
            }
            
            $db->addAll($data);
        }
        return true;
    }

    /**
     * 排序 
     */
    public function listorder() {
        $db = M("Tags");
        if (IS_POST) {
            $listorder = $_POST['listorder'];
            if (is_array($listorder)) {
                foreach ($listorder as $tagid => $v) {
                    $db->where(array("tagid" => $tagid))->data(array("listorder" => (int)$v))->save();
                }
                $this->success("排序更新成功！");
            } else {
                $this->error("参数错误！");
            }
        } else {
            $this->error("参数错误！");
        }
    }

}