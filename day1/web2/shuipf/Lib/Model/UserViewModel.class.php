<?php

/**后台管理员视图模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class UserViewModel extends ViewModel {
    
    public $viewFields = array(
        "User"=>array("*"),
        "Role"=>array("id"=>"role_id","name"=>"role_name","status"=>"role_status","_on"=>"User.role_id=Role.id"),
    );
}
?>
