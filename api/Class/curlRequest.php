<?php
class curlRequest{
    private $cookie;
    private $response_cookie;
    private $header=array();
    private $response_code;
    private $response;
    private $proxy_enable=false;
    private $proxy_address="";
    private $proxy_port=0;
    private $cookie_filename="";
    private $follow_redirect=false;
    private $cur_url="";
    public function getCurUrl()
    {
        return $this->cur_url;
    }
    public function setFollowRedirect($bool)
    {
        $this->follow_redirect=$bool;
    }
    public function setCookie($arr)
    {
        $this->cookie='';
        foreach($arr as $key=>&$value)
        {
            $this->cookie=$this->cookie . ' '. htmlspecialchars($key) . "=" . htmlspecialchars($value) .';';
        }
        $this->cookie= substr($this->cookie,0,strlen($this->cookie)-1);
    }
    public function setHeader($value)
    {
        $this->header[]=$value;
    }
    public function setCookieRaw($cookie)
    {
        $this->cookie=$cookie;
    }
    public function setProxy($address,$port)
    {

        $this->proxy_enable=true;
        $this->proxy_address=$address;
        $this->proxy_port=$port;
    }
    private function _before(&$ch)
    {
        if($this->proxy_enable)
        {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy_address . ':' . $this->proxy_port); //代理服务器地址
        }
        curl_setopt($ch,CURLOPT_COOKIE,$this->cookie);
        if(!empty($this->header))
            curl_setopt($ch,CURLOPT_HTTPHEADER,$this->header);
        $this->cookie_filename=tempnam(sys_get_temp_dir(),"scutvj");
        curl_setopt($ch,CURLOPT_COOKIEJAR,$this->cookie_filename);
        if($this->follow_redirect)
        {
            curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        }
        $this->response_code=0;
    }
    private function _after(&$ch)
    {
        $this->response_code=curl_getinfo($ch,CURLINFO_HTTP_CODE );
        $this->cur_url=curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        $this->response_cookie=$this->processCookieJar(file_get_contents($this->cookie_filename));
        unlink($this->cookie_filename);//only close the connection could we get cookie
    }
    public function post($url, $post,$timeout=10)
    {
        $request_data='';
        $timeout=120;
        if(is_array($post))
        {
            $request_array=array();
            foreach($post as $key=>&$one)
            {
                $request_array[]=urlencode($key) . "=" . urlencode($one);
            }
            $request_data=implode("&",$request_array);
        }
        else
            $request_data=$post;

        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_POSTFIELDS =>$request_data
        );

        $ch = curl_init();
        $this->_before($ch);

        curl_setopt_array($ch, ( $defaults));

        $result = curl_exec($ch);
        $this->response=$result;
        $error=curl_error($ch);
        $this->_after($ch);
        if( ! $result)
        {
            echo $error;
            return false;
        }
        return $result;
    }
    public function postFromData($url, $formData,$timeout=10)
    {

        $defaults = array(
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_POSTFIELDS =>$formData,
            CURLOPT_SAFE_UPLOAD => true
        );

        $ch = curl_init();
        $this->_before($ch);

        curl_setopt_array($ch, ( $defaults));

        $result = curl_exec($ch);
        $this->response=$result;
        $error=curl_error($ch);
        $this->_after($ch);
        if( ! $result)
        {
            echo $error;
            return false;
        }
        return $result;
    }
    public function get($url,$timeout=10)
    {
        $defaults = array(
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => $timeout
        );
        $timeout=120;
        $ch = curl_init();
        $this->_before($ch);
        curl_setopt_array($ch, ( $defaults));

        $result = curl_exec($ch);
        $this->response=$result;
        $error=curl_error($ch);
        $this->_after($ch);

        if( ! $result)
        {
            echo $error;
            return false;
        }

        return $result;
    }
    public function getResponseCookie()
    {
        return $this->response_cookie;
    }
    public function getResponseCode()
    {
        return $this->response_code;
    }
    private function processCookieJar($str)
    {
        $str=str_replace("\t"," ",$str);
        $str=str_replace("\r","",$str);
        $arr=explode("\n",$str);
        $ret=array();
        foreach ($arr as $one) {
            $two=explode(" ",$one);
            if(count($two)!=7)
                continue;
            $ret[$two[5]]=$two[6];
        }
        return $this->cookieArr2Str($ret);

    }
    public function cookieArr2Str($arr)
    {
        $cookie="";
        foreach($arr as $key=>&$value)
        {
            $cookie=$cookie . ' '. htmlspecialchars($key) . "=" . htmlspecialchars($value) .';';
        }
        return substr($cookie,0,strlen($cookie)-1);
    }
    public function cookieStr2Arr($str)
    {
        $ret=array();
        $one=explode(";",$str);
        foreach($one as $two)
        {
            $three=explode("=",$two);
            if(count($three)==2)
                $ret[trim($three[0])]=trim($three[1]);
        }
        return $ret;
    }
    public function mergeCookieRaw($target,$from)
    {
        $arr1=$this->cookieStr2Arr($target);
        $arr2=$this->cookieStr2Arr($from);
        foreach($arr2 as $key=>&$one){
            $arr1[$key]=$one;
        }
        return $this->cookieArr2Str($arr1);
    }
}