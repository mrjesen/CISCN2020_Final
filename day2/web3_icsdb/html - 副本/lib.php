<?php
class User
{
    public $username;
    public $password;
    public $age;
    public $email;
    private $avatar;
    private $content;

    function __construct($username, $password, $age, $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->age = $age;
        $this->email = $email;
        $this->avatar = "/assets/images/users/1.png";
        $this->content = "";
    }

    public function alertMes($mes,$url){
        echo "<script>alert('{$mes}');location.href='{$url}';</script>";
        die;
    }

    private function waf($string)
    {
        $waf = '/phar|file|gopher|http|sftp|flag/i';
        return preg_replace($waf, 'index', $string);
    }

    public function register()
    {
        $_SESSION = [];
        $_SESSION['username'] = $this->username;
        $_SESSION['password'] = $this->password;
        $_SESSION['age'] = $this->age;
        $_SESSION['email'] =$this->email;
    }

    private function check_data($data)
    {
        foreach( $data as $key => $value)
        {
            if ( is_array($value) )
                return $this->check_data($value);
            if ( is_object($value) && $value instanceof User)
            {
                $data_avatar = $value->get_avatar();
                if ( is_string($data_avatar) && !empty($data_avatar)) {
                    $content = file_get_contents(__DIR__ . "/" . $data_avatar);
                    $png_header = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A\x00\x00\x00\x0D\x49\x48\x44\x52";
                    if (strpos($content, $png_header) === false )
                    {
                        throw new Exception("png content got an unexpected Exception");
                    }
                }
            }
        }
        return true;
    }


    public function update($profile)
    {
        $data = unserialize($this->waf($profile));
        if ($data["old_password"] !== $data["old_real_password"] )
            return $this->check_data($data);
        $this->password = $data["password"];
        $this->age = $data["age"];
        $this->email = $data["email"];
    }

    public function get_content()
    {
        return $this->content;
    }

    public function get_avatar()
    {
        return $this->avatar;
    }

    public function set_avatar(string $avatar)
    {
        $this->avatar = $avatar;
    }

    function __destruct()
    {
        if ( isset($this->username)
            && isset($this->password)
            && isset($this->age)
            && isset($this->email)
            && isset($this->avatar)
            && isset($this->content)
            && is_string($this->avatar)
            && !empty($this->avatar)
            && !preg_match('/\:\/\//', $this->avatar) )
        {
            $this->content = file_get_contents(__DIR__ . "/" . $this->avatar);
            $res = "<script>\nvar img = document.createElement(\"img\");\nimg.src= \"data:image/png;base64,content\";\nimg.alt = \"user\";\ndocument.getElementById(\"pro-avatar\").append(img);\n</script>";
            $res = str_replace("content", base64_encode($this->content), $res);
            echo $res;
        }
    }
}

