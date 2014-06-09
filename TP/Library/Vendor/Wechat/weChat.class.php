<?php
namespace Vendor\Wechat;
/**
 * @Class weChat
 * 处理微信用户发送各种消息的处理类
 * @author yangchangqiu <shehuaqigai@gmail.com>
 * @link https://github.com/shehuaqigai/PHPwechatSDK
 * @version 1.0
 */
class weChat
{
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/token?grant_type=client_credential&';
    const QRCODE_IMG_URL='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
    const MENU_CREATE_URL = '/menu/create?';
    const MENU_GET_URL = '/menu/get?';
    const MENU_DELETE_URL = '/menu/delete?';
    const MEDIA_GET_URL = '/media/get?';
    const QRCODE_CREATE_URL='/qrcode/create?';
    const QR_SCENE = 0;
    const QR_LIMIT_SCENE = 1;
    const USER_GET_URL='/user/get?';
    const USER_INFO_URL='/user/info?';
    const GROUP_GET_URL='/groups/get?';
    const GROUP_CREATE_URL='/groups/create?';
    const GROUP_UPDATE_URL='/groups/update?';
    const GROUP_MEMBER_UPDATE_URL='/groups/members/update?';
    const CUSTOM_SEND_URL='/message/custom/send?';
    const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const OAUTH_AUTHORIZE_URL = '/authorize?';
    const OAUTH_TOKEN_PREFIX = 'https://api.weixin.qq.com/sns/oauth2';
    const OAUTH_TOKEN_URL = '/access_token?';
    const OAUTH_REFRESH_URL = '/refresh_token?';
    const OAUTH_USERINFO_URL = 'https://api.weixin.qq.com/sns/userinfo?';
    const PAY_DELIVERNOTIFY = 'https://api.weixin.qq.com/pay/delivernotify?';
    const PAY_ORDERQUERY = 'https://api.weixin.qq.com/pay/orderquery?';
    private $devAccount; //开发者微信号
    private $UserAccount;  //微信用户账号
    private $msgType; //接收消息类型
    public  $postXML;  //微信发送过来的xml数据转化后的数组
    private $access_token;//公众账号唯一标识
    private $token;
    private $appid;
    private $appsecret;
    private $tenpayid;
    private $tenpaykey;
    private $tenpaySignKey;
    private $debug =  false;
    private $errCode = 40001;
    private $errMsg = "no access";
    private $_logcallback;//日志打印回调
    /**
     * weChat构造函数
     */
    public function __construct($options)
    {
        $this->token = isset($options['token'])?$options['token']:'';
        $this->appid = isset($options['appid'])?$options['appid']:'';
        $this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        $this->tenpayid = isset($options['tenpayID'])?$options['tenpayID']:'';
        $this->tenpaykey = isset($options['tenpayKey'])?$options['tenpayKey']:'';
        $this->tenpaySignKey = isset($options['tenpaySignKey'])?$options['tenpaySignKey']:'';
        $this->debug = isset($options['debug'])?$options['debug']:false;
        $this->_logcallback = isset($options['logcallback'])?$options['logcallback']:false;
        /**
        开启调试这样在租用的服务器上就可以看到错误消息了
        如果租的是服务器默认是关闭错误输出的
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
         */
    }
    /**
     * 初始化接收用户
     */
    public function volid()
    {
        $echoStr = isset($_GET["echostr"]) ? $_GET["echostr"]: '';
        //valid signature , option检查签名是不跟微信的签名一样
        if($this->checkSignature()){
            if(!empty($echoStr)){//如果是true就是第一次配置url成为开发者的验证
                echo $echoStr;
                exit(0);
            }

        }
        else{
            echo '暂不支持非微信交互';
        }
    }
    /**
     *这个方法是检验是不是从微信服务器请求而来
     */
    private function checkSignature()
    {
        return true;
        $signature = isset($_GET["signature"])?$_GET["signature"]:'';
        $timestamp = isset($_GET["timestamp"])?$_GET["timestamp"]:'';
        $nonce = isset($_GET["nonce"])?$_GET["nonce"]:'';
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }
        else{
            return false;
        }
    }
    /**
     * 获取公众号的唯一标识access_token
     */
    public function get_Access_Token(){
          if($this->access_token) return;
         if(empty($_SESSION['expiresTime']) || (time()-$_SESSION['firstTime']) >=7000){
            $appid=$this->appid;
            $secret=$this->appsecret;
            $url=self::API_URL_PREFIX.self::AUTH_URL.'appid='.$appid."&secret=".$secret;
            $json = $this->http_get($url);
            $result=json_decode($json);
          $access_toke=$result->access_token;
            if($access_toke){
                $_SESSION['access_toke']=$access_toke;
                $_SESSION['expiresTime']=$result->expires_in;
                $_SESSION['firstTime']=time();
                $this->access_token=$access_toke;
                return;
            }else{
                echo '获取access_token失败';
                exit(0);
            }
        }
        $this->access_token=$_SESSION['access_toke'];
    }
    /**
     * 验证通过后分发各种api接口
     */
    public function getWechatPostXML(){
        //---------- 接 收 数 据 ---------- //
        //get post data, May be due to the different environments
        /**
        $_POST：通过 HTTP POST 方法传递的变量组成的数组。是自动全局变量。
        $GLOBALS['HTTP_RAW_POST_DATA'] ：总是产生 $HTTP_RAW_POST_DATA 变量包含有原始的 POST 数据。
        此变量仅在碰到未识别 MIME 类型的数据时产生。$HTTP_RAW_POST_DATA 对于 enctype="multipart/form-data" 表单数据不可用。
        也就是说基本上$GLOBALS['HTTP_RAW_POST_DATA'] 和 $_POST是一样的。
        但是如果post过来的数据不是PHP能够识别的，你可以用 $GLOBALS['HTTP_RAW_POST_DATA']来接收，比如 text/xml 或者 soap 等等。
        补充说明：PHP默认识别的数据类型是application/x-www.form-urlencoded标准的数据类型。
        */
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//获取POST数据

        /**
        php://input 是个可以访问请求的原始数据的只读流。
        POST 请求的情况下，最好使用 php://input 来代替 $HTTP_RAW_POST_DATA，因为它不依赖于特定的 php.ini 指令。
        而且，这样的情况下 $HTTP_RAW_POST_DATA 默认没有填充， 比激活 always_populate_raw_post_data 潜在需要更少的内存。
        enctype="multipart/form-data" 的时候 php://input 是无效的。
        */
         $postStr = file_get_contents("php://input");//获取POST数据
        if (!empty($postStr)) {
            $postObj = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->postXML = $postObj;
            $this->UserAccount = isset($postObj['FromUserName']) ? $postObj['FromUserName'] : '';//获取微信用户账号
            $this->devAccount = isset($postObj['ToUserName']) ? $postObj['ToUserName'] : '';//获取开发者微信号
            $this->msgType = isset($postObj['MsgType']) ? $postObj['MsgType'] : '';//获取消息类型
        } else {
            $this->responsiveText('真是神提问,我无法猜到你想说什么');
            exit;
        }
    }
    /**
    打印日志消息
     */
    public function log($log){
        if ($this->debug && is_callable($this->_logcallback)) {
            return call_user_func($this->_logcallback,$log);
        }
    }
    /**
     * 对MsgType进行分类让适合各自的方法处理
     */
    public function MsgTypeCheck()
    {
        if (isset($this->postXML['MsgType'])){
            return $this->postXML['MsgType'];
        }else{
            return false;
        }
    }
    /**
     * 获取消息ID
     */
    public function getMessageID() {
        if (isset($this->postXML['MsgId']))
            return $this->postXML['MsgId'];
        else
            return false;
    }
    /**
     * 获取接收消息内容正文
     */
    public function getMessageContent(){
        if (isset($this->postXML['Content']))
            return $this->postXML['Content'];
        else if (isset($this->postXML['Recognition'])) //获取语音识别文字内容，需申请开通
            return $this->postXML['Recognition'];
        else
            return false;
    }
    /**
     * 获取接收消息图片
     */
    public function getMessagePicUrl(){
        if (isset($this->postXML['PicUrl']))
            return $this->postXML['PicUrl'];
        else
            return false;
    }

    /**
     * 获取接收消息链接
     */
    public function getMessageLinkArray(){
        if (isset($this->postXML['Url'])){
            return array(
                'url'=>$this->postXML['Url'],
                'title'=>$this->postXML['Title'],
                'description'=>$this->postXML['Description']
            );
        } else
            return false;
    }
    /**
     * 获取接收地理位置
     */
    public function getMessageLocationArray(){
        if (isset($this->postXML['Location_X'])){
            return array(
                'x'=>$this->postXML['Location_X'],
                'y'=>$this->postXML['Location_Y'],
                'scale'=>$this->postXML['Scale'],
                'label'=>$this->postXML['Label']
            );
        } else
            return false;
    }
    /**
     * @param $str
     * @return string
     * xml 过滤
     *根据W3C的标准，以下16进制的字符是不被允许出现在XML文件中的，即使放在<![CDATE[]]> 中，
     * 也不能幸免遇难,会引起 Solr 对 XML 处理的错误，从而导致创建索引失败，
     * 所以在提交数据之前把这些字符过滤掉。\x00-\x08, \x0b-\x0c, \x0e-\x1f在 PHP 可以用这个方法
     * return preg_replace(’@[\x00-\x08\x0B\x0C\x0E-\x1F]@’, ”, $string);另外，
     * 在给 Solr 提交数据的时候要注意提交的 xml 文件/数据大小，
     * 如果太大可能会出现各种莫名奇妙的问题，Solr 会说你的 XML 格式不正确，缺少闭合标签，解析不了。
     *
     */
    public static function xmlSafeFilter($str)
    {
        return "<![CDATA[".preg_grep("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str)."]]>";
    }
    /**
     * 转化成xml
    */
    public function data_to_xml($data){
         $xml='<xml>';
        foreach($data as $key => $value){
            $val="<![CDATA[".$value."]]>";
            if($key == 'CreateTime'){
                $val=$value;
            }
            $xml.="<$key>$val</$key>";
        }
        $xml.="</xml>";
        return $xml;
        }
    /**回复文本消息
     *参数	是否必须	描述
     *ToUserName	 是	 接收方帐号（收到的OpenID）
     *FromUserName	 是	开发者微信号
     *CreateTime	 是	 消息创建时间 （整型）
     *MsgType	 是	 text
     *Content	 是	 回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
     *
     */
    public function responsiveText($text){

        $msg = array(
            'ToUserName' =>$this->UserAccount,
            'FromUserName'=>$this->devAccount,
            'CreateTime'=>time(),
            'MsgType'=>'text',
            'Content'=>$text
        );
        echo $this->data_to_xml($msg);
    }
    /**
     *回复图片消息
     *参数	是否必须	说明
     *ToUserName	 是	 接收方帐号（收到的OpenID）
     *FromUserName	 是	开发者微信号
     *CreateTime	 是	 消息创建时间 （整型）
     *MsgType	 是	 image
     *MediaId	 是	 通过上传多媒体文件，得到的id。
     */
    private function responsiveImage(){

    }
    /**
     *回复语音消息
     *参数	是否必须	说明
     *ToUserName	 是	 接收方帐号（收到的OpenID）
     *FromUserName	 是	开发者微信号
     *CreateTime	 是	 消息创建时间戳 （整型）
     *MsgType	 是	 语音，voice
     *MediaId	 是	 通过上传多媒体文件，得到的id
     */
    private function responsiveVoice(){}
    /**
     *回复视频消息
     *参数	是否必须	说明
     *ToUserName	 是	 接收方帐号（收到的OpenID）
     *FromUserName	 是	开发者微信号
     *CreateTime	 是	 消息创建时间 （整型）
     *MsgType	 是	 video
     *MediaId	 是	 通过上传多媒体文件，得到的id
     *Title	 否	 视频消息的标题
     *Description	 否	 视频消息的描述
     */
    private function responsiveVideo(){}
    /**
     *回复音乐消息
     *参数	是否必须	说明
     *ToUserName	 是	 接收方帐号（收到的OpenID）
     *FromUserName	 是	开发者微信号
     *CreateTime	 是	 消息创建时间 （整型）
     *MsgType	 是	 music
     *Title	 否	 音乐标题
     *Description	 否	 音乐描述
     *MusicURL	 否	 音乐链接
     *HQMusicUrl	 否	 高质量音乐链接，WIFI环境优先使用该链接播放音乐
     *ThumbMediaId	 是	 缩略图的媒体id，通过上传多媒体文件，得到的id
     */
    private function responsiveMusic(){}
    /**
     *回复图文消息
     *参数	是否必须	说明
     *ToUserName	 是	 接收方帐号（收到的OpenID）
     *FromUserName	 是	开发者微信号
     *CreateTime	 是	 消息创建时间 （整型）
     *MsgType	 是	 news
     *ArticleCount	 是	 图文消息个数，限制为10条以内
     *Articles	 是	 多条图文消息信息，默认第一个item为大图,注意，如果图文数超过10，则将会无响应
     *Title	 否	 图文消息标题
     *Description	 否	 图文消息描述
     *PicUrl	 否	 图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
     *Url	 否	 点击图文消息跳转链接
     */
    private function responsiveImageAndText(){}
    /**
     * @param $url
     * @return bool|mixed
     */
    private function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @return string content
     */
    private function http_post($url,$param){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
    /**
     * 上传多媒体文件
     */
    public function uploadMediaFile(){}
    /**
     * 下载多媒体文件
     */
    public function downloadMediaFile(){}
    /**
     * 微信api不支持中文转义的json结构
     * @param $arr
     * @return string
     */
    static function json_encode($arr) {
        $parts = array ();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode ( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    /**
     * 创建菜单
     * @param array $data 菜单数组数据
     * { "button":[  {"type":'click','name':'今日歌曲','key':"MENU_KEY_MUSIC"},
     *               {"type":"view","name":"歌手简介","url":"http://www.qq.com"},
     *               {"name":'菜单','sub_button':[ {'type':'click',"name":"hello word","key":"MENU_KEY_MENU"},
     *                                            {"type":"click","name":"赞一下我们","key":"MENU_KEY_GOOD"}
     *                                          ]
     *               }
     *            ]
     * }
     */
    /**
     * 就是一个json里面就一个button键,button键的值是一个数组,数组里放三个json表示三个菜单,
     * 每一个json里有一个sub_button键对应的是数组值   这个数组里可以最多放五个json
     * 没一个json的格式里必须有type键表示click还是view
     * name键表示菜单的名字
     * key表示菜单所对应的标识符,要唯一,只有type为click的才可以用key键
     * url表示type类型为view的键是一个网址
    */
    public function createCustomMenu($data){
        $this->get_Access_Token();
        $url=self::API_URL_PREFIX.self::MENU_CREATE_URL.'access_token='.$this->access_token;
        $result = $this->http_post($url,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取菜单
     * @return array('menu'=>array(....s))
     */
    public function getCustomMenu(){
        $this->get_Access_Token();
        $url=self::API_URL_PREFIX.self::MENU_GET_URL.'access_token='.$this->access_token;
        $result = $this->http_get($url);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除菜单
     * @return boolean
     */
    public function deleteMenu(){
        $this->get_Access_Token();
        $url=self::API_URL_PREFIX.self::MENU_DELETE_URL.'access_token='.$this->access_token;
        $result = $this->http_get($url);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 根据媒体文件ID获取媒体文件
     * @param string $media_id 媒体文件id
     * @return raw data
     */
    public function getMedia($media_id){
        $this->get_Access_Token();
        $url=self::API_URL_PREFIX.self::MEDIA_GET_URL.'access_token='.$this->access_token.'&media_id='.$media_id;
        $result = $this->http_get($url);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 创建二维码ticket
     * @param int $scene_id 自定义追踪id
     * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)
     * @param int $expire 临时二维码有效期，最大为1800秒
     * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800)
     */
    public function getQRCodeTicket($scene_id,$type=0,$expire=1800){
        $this->get_Access_Token();
        $data = array(
            'expire_seconds'=>$expire,
            'action_name'=>$type ? "QR_LIMIT_SCENE" : "QR_SCENE",
            'action_info'=>array('scene'=>array('scene_id'=>$scene_id))
        );
        if ($type == 1) {
            unset($data['expire_seconds']);
        }
        $url=self::API_URL_PREFIX.self::QRCODE_CREATE_URL.'access_token='.$this->access_token;
        $result = $this->http_post($url,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
               // $this->errCode = $json['errcode'];
               // $this->errMsg = $json['errmsg'];
                echo $result;
              exit();
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回http地址
     */
    public function getSceneQRImageUrl($ticket) {
        return self::QRCODE_IMG_URL.$ticket;
    }
    /**
     * 发送客服消息
     * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
     * @return boolean|array
     */
    public function sendCustomMessage($data){
        $this->get_Access_Token();
        $url=self::API_URL_PREFIX.self::CUSTOM_SEND_URL.'access_token='.$this->access_token;
        $result = $this->http_post($url,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 批量获取关注用户列表
     * 如果不写从哪里开始就默认从第一个开始
    */
    public function getUserList($next_openid=''){
        $this->get_Access_Token();
        $url=self::API_URL_PREFIX.self::USER_GET_URL.'access_token='.$this->access_token.'&next_openid='.$next_openid;
        $result = $this->http_get($url);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 获取关注者详细信息
     * @param string $openid
     * @return array
     */
    public function getUserInfo($openid){
        $this->get_Access_Token();
        $url=self::API_URL_PREFIX.self::USER_INFO_URL.'access_token='.$this->access_token.'&openid='.$openid;
        $result = $this->http_get($url);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 获取用户分组列表
     * @return boolean|array
     */
    public function getGroup(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::GROUP_GET_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 新增自定分组
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function createGroup($name){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'group'=>array('name'=>$name)
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 更改分组名称
     * @param int $groupid 分组id
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function updateGroup($groupid,$name){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'group'=>array('id'=>$groupid,'name'=>$name)
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 移动用户分组
     * @param int $groupid 分组id
     * @param string $openid 用户openid
     * @return boolean|array
     */
    public function updateGroupMembers($groupid,$openid){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'openid'=>$openid,
            'to_groupid'=>$groupid
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_MEMBER_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }



    /**
     * oauth 授权跳转接口
     * @param string $callback 回调URI
     * @return string
     */
    public function getOauthRedirect($callback,$state='',$scope='snsapi_userinfo'){
        return self::OAUTH_PREFIX.self::OAUTH_AUTHORIZE_URL.'appid='.$this->appid.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
    }

    /*
     * 通过code获取Access Token
     * @return array {access_token,expires_in,refresh_token,openid,scope}
     */
    public function getOauthAccessToken(){
        $code = isset($_GET['code'])?$_GET['code']:'';
        if (!$code) return false;
        $result = $this->http_get(self::OAUTH_TOKEN_PREFIX.self::OAUTH_TOKEN_URL.'appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code');
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 刷新access token并续期
     * @param string $refresh_token
     * @return boolean|mixed
     */
    public function getOauthRefreshToken($refresh_token){
        $result = $this->http_get(self::OAUTH_TOKEN_PREFIX.self::OAUTH_REFRESH_URL.'appid='.$this->appid.'&grant_type=refresh_token&refresh_token='.$refresh_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege}
     */
    public function getOauthUserinfo($access_token,$openid){
        $result = $this->http_get(self::OAUTH_USERINFO_URL.'access_token='.$access_token.'&openid='.$openid);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 获取签名
     * @param array $arrdata 签名数组
     * @param string $method 签名方法
     * @return boolean|string 签名值
     */
    public function getSignature($arrdata,$method="sha1") {
        if (!function_exists($method)) return false;
        ksort($arrdata);
        $paramstring = "";
        foreach($arrdata as $key => $value)
        {
            if(strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $paySign = $method($paramstring);
        return $paySign;
    }

    /**
     * 生成随机字串
     * @param number $length 长度，默认为16，最长为32字节
     * @return string
     */
    public function generateNonceStr($length=16){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++)
        {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * 生成订单package字符串
     * @param string $out_trade_no 必填，商户系统内部的订单号,32个字符内,确保在商户系统唯一
     * @param string $body 必填，商品描述,128 字节以下
     * @param int $total_fee 必填，订单总金额,单位为分
     * @param string $notify_url 必填，支付完成通知回调接口，255 字节以内
     * @param string $spbill_create_ip 必填，用户终端IP，IPV4字串，15字节内
     * @param int $fee_type 必填，现金支付币种，默认1:人民币
     * @param string $bank_type 必填，银行通道类型,默认WX
     * @param string $input_charset 必填，传入参数字符编码，默认UTF-8，取值有UTF-8和GBK
     * @param string $time_start 交易起始时间,订单生成时间,格式yyyyMMddHHmmss
     * @param string $time_expire 交易结束时间,也是订单失效时间
     * @param int $transport_fee 物流费用,单位为分
     * @param int $product_fee 商品费用,单位为分,必须保证 transport_fee + product_fee=total_fee
     * @param string $goods_tag 商品标记,优惠券时可能用到
     * @param string $attach 附加数据，notify接口原样返回
     * @return string
     */
    public function createPackage($out_trade_no,$body,$total_fee,$notify_url,$spbill_create_ip,$fee_type=1,$bank_type="WX",$input_charset="UTF-8",$time_start="",$time_expire="",$transport_fee="",$product_fee="",$goods_tag="",$attach=""){
        $arrdata = array("bank_type" => $bank_type, "body" => $body, "partner" => $this->partnerid, "out_trade_no" => $out_trade_no, "total_fee" => $total_fee, "fee_type" => $fee_type, "notify_url" => $notify_url, "spbill_create_ip" => $spbill_create_ip, "input_charset" => $input_charset);
        if ($time_start)  $arrdata['time_start'] = $time_start;
        if ($time_expire)  $arrdata['time_expire'] = $time_expire;
        if ($transport_fee)  $arrdata['transport_fee'] = $transport_fee;
        if ($product_fee)  $arrdata['product_fee'] = $product_fee;
        if ($goods_tag)  $arrdata['goods_tag'] = $goods_tag;
        if ($attach)  $arrdata['attach'] = $attach;
        ksort($arrdata);
        $paramstring = "";
        foreach($arrdata as $key => $value)
        {
            if(strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $stringSignTemp = $paramstring . "&key=" . $this->partnerkey;
        $signValue = strtoupper(md5($stringSignTemp));
        $package = http_build_query($arrdata) . "&sign=" . $signValue;
        return $package;
    }

    /**
     * 支付签名(paySign)生成方法
     * @param string $package 订单详情字串
     * @param string $timeStamp 当前时间戳（需与JS输出的一致）
     * @param string $nonceStr 随机串（需与JS输出的一致）
     * @return string 返回签名字串
     */
    public function getPaySign($package, $timeStamp, $nonceStr){
        $arrdata = array("appid" => $this->appid, "timestamp" => $timeStamp, "noncestr" => $nonceStr, "package" => $package, "appkey" => $this->paysignkey);
        $paySign = $this->getSignature($arrdata);
        return $paySign;
    }

    /**
     * 回调通知签名验证
     * @param array $orderxml 返回的orderXml的数组表示，留空则自动从post数据获取
     * @return boolean
     */
    public function checkOrderSignature($orderxml=''){
        if (!$orderxml) {
            $postStr = file_get_contents("php://input");
            if (!empty($postStr)) {
                $orderxml = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            } else return false;
        }
        $arrdata = array('appid'=>$orderxml['AppId'],'appkey'=>$this->paysignkey,'timestamp'=>$orderxml['TimeStamp'],'noncestr'=>$orderxml['NonceStr'],'openid'=>$orderxml['OpenId'],'issubscribe'=>$orderxml['IsSubscribe']);
        $paySign = $this->getSignature($arrdata);
        if ($paySign!=$orderxml['AppSignature']) return false;
        return true;
    }

    /**
     * 发货通知
     * @param string $openid 用户open_id
     * @param string $transid 交易单号
     * @param string $out_trade_no 第三方订单号
     * @param int $status 0:发货失败；1:已发货
     * @param string $msg 失败原因
     * @return boolean|array
     */
    public function sendPayDeliverNotify($openid,$transid,$out_trade_no,$status=1,$msg='ok'){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $postdata = array(
            "appid"=>$this->appid,
            "appkey"=>$this->paysignkey,
            "openid"=>$openid,
            "transid"=>strval($transid),
            "out_trade_no"=>strval($out_trade_no),
            "deliver_timestamp"=>strval(time()),
            "deliver_status"=>strval($status),
            "deliver_msg"=>$msg,
        );
        $postdata['app_signature'] = $this->getSignature($postdata);
        $postdata['sign_method'] = 'sha1';
        unset($postdata['appkey']);
        $result = $this->http_post(self::PAY_DELIVERNOTIFY.'access_token='.$this->access_token,self::json_encode($postdata));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /*
     * 查询订单信息
     * @param string $out_trade_no 订单号
     * @return boolean|array
     */
    public function getPayOrder($out_trade_no) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $sign = strtoupper(md5("out_trade_no=$out_trade_no&partner={$this->partnerid}&key={$this->partnerkey}"));
        $postdata = array(
            "appid"=>$this->appid,
            "appkey"=>$this->paysignkey,
            "package"=>"out_trade_no=$out_trade_no&partner={$this->partnerid}&sign=$sign",
            "timestamp"=>strval(time()),
        );
        $postdata['app_signature'] = $this->getSignature($postdata);
        $postdata['sign_method'] = 'sha1';
        unset($postdata['appkey']);
        $result = $this->http_post(self::PAY_ORDERQUERY.'access_token='.$this->access_token,self::json_encode($postdata));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'].json_encode($postdata);
                return false;
            }
            return $json["order_info"];
        }
        return false;
    }

    /**
     * 获取收货地址JS的签名
     * @param string $appId
     * @param string $url
     * @param int $timeStamp
     * @param string $nonceStr
     * @param string $user_token
     * @return Ambigous <boolean, string>
     */
    public function getAddrSign($url, $timeStamp, $nonceStr, $user_token=''){
        if (!$user_token) $user_token = $this->user_token;
        if (!$user_token) {
            $this->errMsg = 'no user access token found!';
            return false;
        }
        $url = htmlspecialchars_decode($url);
        $arrdata = array(
            'appid'=>$this->appid,
            'url'=>$url,
            'timestamp'=>strval($timeStamp),
            'noncestr'=>$nonceStr,
            'accesstoken'=>$user_token
        );
        return $this->getSignature($arrdata);
    }
}











