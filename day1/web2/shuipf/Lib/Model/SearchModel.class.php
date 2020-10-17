<?php

/* * 
 * Search搜索模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

class SearchModel extends CommonModel {

    /**
     *  数据处理
     * @param type $data 数据
     * @return type
     */
    private function dataHandle($data) {
        if(!$data){
            return $data;
        }
        import("Input");
        $data = addslashes($data);
        $data = strip_tags($data);
        $data = str_replace(array(" ","\r\t"),array(""),$data);
        $data = Input::forSearch($data);
        $data = Input::deleteHtmlTags($data);
        return $data;
    }

    /**
     * 添加搜索数据
     * @param type $id 信息id
     * @param type $catid 栏目id
     * @param type $modelid 模型id
     * @param type $inputtime 发布时间
     * @param type $data 数据
     * @return boolean
     */
    public function searchAdd($id, $catid, $modelid, $inputtime, $data) {
        if (!$id || !$catid || !$modelid || !$data) {
            return false;
        }
        //发布时间
        $inputtime = $inputtime ? (int) $inputtime : time();
        $data = $this->dataHandle($data);
        $searchid = $this->add(array(
            "id" => $id,
            "catid" => $catid,
            "modelid" => $modelid,
            "adddate" => $inputtime,
            "data" => $data,
                ));
        if ($searchid !== false) {
            return $searchid;
        }
        return false;
    }

    /**
     * 更新搜索数据
     * @param type $id 信息id
     * @param type $catid 栏目id
     * @param type $modelid 模型id
     * @param type $inputtime 发布时间
     * @param type $data 数据
     * @return boolean
     */
    public function searchSave($id, $catid, $modelid, $inputtime, $data) {
        if (!$id || !$catid || !$modelid || !$data) {
            return false;
        }
        //发布时间
        $inputtime = $inputtime ? (int) $inputtime : time();
        $data = $this->dataHandle($data);
        $searchid = $this->where(array(
                    "id" => $id,
                    "catid" => $catid,
                    "modelid" => $modelid,
                ))->save(array(
            "adddate" => $inputtime,
            "data" => $data,
                ));
        if ($searchid !== false) {
            return $searchid;
        }
        return false;
    }

    /**
     * 删除搜索数据
     * @param type $id 信息id
     * @param type $catid 栏目id 
     * @param type $modelid 模型id
     * @return boolean
     */
    public function searchDelete($id, $catid, $modelid) {
        if (!$id || !$catid || !$modelid) {
            return false;
        }
        return $this->where(array(
                    "id" => $id,
                    "catid" => $catid,
                    "modelid" => $modelid,
                ))->delete();
    }

}

?>
