<?php
namespace Wechatapi\Controller;
use Think\Controller;
class IndexController extends Controller {
    private static $wechat;

    /**
     * 在父类里的构造函数中调用这个方法
     * 在这里你可以初始化一些所有方法都要使用到的一些逻辑处理
     */
    protected function _initialize(){
        /**
         * 注意，由于用到了https所以首先要开启openssl扩展
         * 由于涉及到请求还需要开启curl扩展
         * 使用到回调函数，需要php5.3以上版本
         */
        $options = array(
            'token'=>'weixinEcshop', //填写你设定的key(这里写的是测试号)
            'appid'=>'wxdd108acd984a2fe1', //填写高级调用功能的app id, 请在微信开发模式后台查询(这里写的是测试号)
            'appsecret'=>'e8206cbe90c67555609fa57f633f8c78', //填写高级调用功能的密钥(这里写的是测试号)
            //'tenpayID'=>'88888888', //财付通商户身份标识，支付权限专用，没有可不填
            //'tenpayKey'=>'', //财付通商户权限密钥Key，支付权限专用
            // 'tenpaySignKey'=>'' //商户签名密钥Key，支付权限专用
            /**
             * @function logcallback
             * @param $debugData
             * 由于微信不能真实测试,为了准确性，提供了真机测试
             * 在微信中发送信息，只要开启调试模式就可以把信息写入
             * 文件就可以查看到调试信息了
             */
            'logcallback'=>function($debugData){//
                    // 要写入的文件名字
                    $filename = 'logTrace.txt';
                    $word = var_export($debugData,true);
                    $fh = fopen($filename, "w");
                    fwrite($fh, $word);
                    fclose($fh);
                    exit(0);
                },
            'debug'=>true
        );
        $wechat=new \Vendor\Wechat\weChat($options);//创建实例对象
        self::$wechat=$wechat;
        self::$wechat->volid();
    }
    /**
     * 微信接口url地址
     * 所谓的微信接口的入口地址,微信用户所有的信息提交都是有
     * 微信服务器发送到这个index动作里
     */
    public function index(){
        self::$wechat->getWechatPostXML();//接收微信发送过来的消息
        $type=self::$wechat->MsgTypeCheck();
        switch($type){
            case 'text': //普通文本
                $this->getTextMessageHandle();
                break;
            case 'image': //图片
                $this->getImageMessageHandle();
                break;
            case 'voice': //语音或者语音识别功能
                $this->getVoiceMessageHandle();
                break;
            case 'video': //视频
                $this->getVideoMessageHandle();
                break;
            case 'location'://地理位置消息
                $this->getLocationMessageHandle();
                break;
            case 'link': //链接消息
                $this->getLinkMessageHandle();
                break;
            case 'event': //事件类型
                $this->eventPullHandle();
                break;
            case 'default':
                echo '不知道你想表达什么!';
                break;
        }
    }
    /**
     * 生成带场景参数二维码动作
     * @param {int} $type
     * @param {int} $sceneId
     * sceneId是场景值 永久的最多100000临时的最多4个字节整型
     */
    public function generateSceneQRAction($type,$sceneId){
        //type为0是临时,1为永久
      $json=self::$wechat->getQRCodeTicket($sceneId,$type);
      $ticket=$json['ticket'];
      echo self::$wechat->getSceneQRImageUrl($ticket);
    }

    /**
     * 这个接口是用来创建自定义菜单的
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
    public function customCreateMenu(){
        $data=array("button"=>array(
                            array("name"=>"活动","sub_button"=>array(
                                array("type"=>"click","name"=>"开发中1","key"=>"developer_1"),
                                array("type"=>"click","name"=>"开发中2","key"=>"developer_2"),
                                array("type"=>"click","name"=>"开发中3","key"=>"developer_3"),
                                array("type"=>"click","name"=>"开发中4","key"=>"developer_4"),
                                array("type"=>"click","name"=>"开发中5","key"=>"developer_5")
                                                                   )
                                 ),
                            array("name"=>"商城中心","sub_button"=>array(
                                array("type"=>"view","name"=>"开发中商城","url"=>"http://115.28.26.50/weChatDev/index.php/Wechatapi/WechatShop/index"),
                                array("type"=>"click","name"=>"微支付","key"=>"http://"),
                                array("type"=>"view","name"=>"木木商城","url"=>"http://115.28.26.50/wxscwz/"),
                                array("type"=>"click","name"=>"微订餐","key"=>"http://"),
                                array("type"=>"click","name"=>"微相册","key"=>"http://")
                                                                  )
                                   ),
                            array("name"=>"服务","sub_button"=>array(
                                array("type"=>"click","name"=>"订单查询","key"=>"query"),
                                array("type"=>"click","name"=>"客服联系","key"=>"customer"),
                                array("type"=>"click","name"=>"信息反馈","key"=>"messageBack"),
                                array("type"=>"click","name"=>"投诉中心","key"=>"center"),
                                array("type"=>"click","name"=>"聊天机器人","key"=>"chat")
                                                                  )
                                )
        ));
        if(self::$wechat->createCustomMenu($data)){
         echo "创建菜单成功!";
        }
    }
    /**
     * 获取自定义的菜单列表返回一个数组
    */
    public function getCustomMenuList(){

      self::$wechat->getCustomMenu();
    }
    /**
     * 删除自定义的菜单列表
    */
    public function deleteCustomMenu(){
        self::$wechat->deleteMenu();
    }
    /**
     * 发送客服消息
     * 要发送一个json数据
    */
    public function sendCustomerServerMessage(){}
    /**
     * 获取关注者列表
    */
    public function FllowUsersList(){
        self::$wechat->getUserList();
    }
    public function test(){
        echo APP_PATH;


    }

    //---------------------------------------------------------------------------------------------------------
    //下面get开头的是接收普通消息处理动作
    //-------------------------------------------------------------------------------------------------------
    /**
     * 接收普通消息API中的文本消息处理
     * 参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 text
     *Content	 文本消息内容
     *MsgId	 消息id，64位整型
     */
    private function getTextMessageHandle(){
        self::$wechat->responsiveText('欢迎参加技术交流!');//响应文本消息返回给用户
    }
    /**
     *接收普通消息API中的图片消息处理
     *参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 image
     *PicUrl	 图片链接
     *MediaId	 图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
     *MsgId	 消息id，64位整型
     *
     */
    private function getImageMessageHandle(){

        self::$wechat->responsiveText('你的图片很性感，可惜我没有更性感的图片!');

    }
    /**
     * 接收普通消息API中的声音消息处理
     *开通语音识别功能，用户每次发送语音给公众号时，微信会在推送的语音消息XML数据包中，增加一个Recongnition字段。
     * 由于客户端缓存，开发者开启或者关闭语音识别功能，对新关注者立刻生效，
     * 对已关注用户需要24小时生效。开发者可以重新关注此帐号进行测试。
     * 参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 语音为voice
     *MediaId	 语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
     *Format	 语音格式，如amr，speex等
     *Recognition	 只有开启语音识别结果才会有这个字段，UTF8编码
     *MsgID	 消息id，64位整型
     *
     */
    private function getVoiceMessageHandle(){
        self::$wechat->responsiveText('你的声音很有磁性!');

    }
    /**
     *接收普通消息API中的视频消息处理
     * 参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 视频为video
     *MediaId	 视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
     *ThumbMediaId	 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
     *MsgId	 消息id，64位整型
     */
    private function getVideoMessageHandle(){
        self::$wechat->responsiveText('你的视频很搞笑');

    }
    /**
     *接收普通消息API中的位置消息处理
     * 参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 location
     *Location_X	 地理位置维度
     *Location_Y	 地理位置经度
     *Scale	 地图缩放大小
     *Label	 地理位置信息
     *MsgId	 消息id，64位整型
     *
     */
    private function getLocationMessageHandle(){
        $gbs=self::$wechat->getMessageLocationArray();
        $text="你所在的位置：\n\r经度为".$gbs['x']."\n\r维度为".$gbs['y']."\n\r当前缩放比例为".$gbs['scale']."\n\r你的地址是:".$gbs['label'];
        self::$wechat->responsiveText($text);

    }
    /**
     *接收普通消息API中的链接消息处理
     * 参数	描述
     *ToUserName	 接收方微信号
     *FromUserName	 发送方微信号，若为普通用户，则是一个OpenID
     *CreateTime	 消息创建时间
     *MsgType	 消息类型，link
     *Title	 消息标题
     *Description	 消息描述
     *Url	 消息链接
     *MsgId	 消息id，64位整型
     */
    private function getLinkMessageHandle(){
        self::$wechat->responsiveText('你的链接消息我收藏了');

    }

    //---------------------------------------------------------------------------------------------------------
    //下面event开头的是事件推送动作处理
    //-------------------------------------------------------------------------------------------------------
    /**
     * 事件推送处理
     * 1 关注/取消关注事件
     * 2 扫描带参数二维码事件
     * 3 上报地理位置事件
     * 4 自定义菜单事件
     * 5 点击菜单拉取消息时的事件推送
     * 6 点击菜单跳转链接时的事件推送
     */
    private function eventPullHandle(){
        $event=self::$wechat->postXML['Event'];
         switch($event){
             case 'subscribe'://在没有关注的情况下扫描公众号或者带参数二维码都会触发这个事件
                 $this->eventPullSubscribe();
                 break;
             case 'unsubscribe':
                 $this->eventPullUnsubscribe();
                 break;
             case 'SCAN'://在已经关注的情况下扫描带参数二维码
                 $this->eventPullScanSeneQRCodeHasSubscribe();
                 break;
             case 'LOCATION':
                 $this->eventPullLocation();
                 break;
             case 'CLICK':
                 $this->eventPullClickMenuGetMessage();
                 break;
             case 'VIEW':
                 $this->eventPullClickMenuJumpToLink();
                 break;
             case 'default':
                 break;
         }

    }
    /**
     *关注/取消关注事件
     *用户在关注与取消关注公众号时，微信会把这个事件推送到开发者填写的URL。方便开发者给用户下发欢迎消息或者做帐号的解绑。
     *参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 消息类型，event
     *Event	 事件类型，subscribe(订阅)、unsubscribe(取消订阅)
     *<EventKey><![CDATA[]]></EventKey>
     */
    //触发关注事件
    private function eventPullSubscribe(){
        $eventKey=self::$wechat->postXML['EventKey'];
        self::$wechat->responsiveText('木木科技，专注于电商Web解决方案8年，微商城将于6、1日正式上线，欢迎各界精英人士洽谈合作');
        //如果是扫描带参数二维会有这个字段
        if(!empty($eventKey)){
            $senceID = explode("qrscene_",$eventKey)[1];
            $this->eventPullScanSeneQRCodeIsNotSubscribe($senceID);
        }
    }
    //取消关注事件
    private function eventPullUnsubscribe(){


    }
    //-----------------------------------------------------------------------------------
    //扫描带参数二维码事件
    //用户扫描带场景值二维码时，可能推送以下两种事件：
    //如果用户还未关注公众号，则用户可以关注公众号，关注后微信会将带场景值关注事件推送给开发者。
    //如果用户已经关注公众号，则微信会将带场景值扫描事件推送给开发者。
    //------------------------------------------------------------------------------------
    /**
     *1. 用户未关注时，进行关注后的事件推送
     *参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 消息类型，event
     *Event	 事件类型，subscribe
     *EventKey	 事件KEY值，qrscene_为前缀，后面为二维码的参数值
     *Ticket	 二维码的ticket，可用来换取二维码图片
     */
    private function eventPullScanSeneQRCodeIsNotSubscribe($senceID){

    }
    /**
     *2. 用户已关注时的事件推送
     *参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 消息类型，event
     *Event	 事件类型，SCAN
     *EventKey	 事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id
     *Ticket	 二维码的ticket，可用来换取二维码图片
     */
    private function eventPullScanSeneQRCodeHasSubscribe(){
        $eventKey=self::$wechat->postXML['EventKey'];

    }
    /**
     *上报地理位置事件
     *用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，
     *或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置。
     *上报地理位置时，微信会将上报地理位置事件推送到开发者填写的URL。
     *参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 消息类型，event
     *Event	 事件类型，LOCATION
     *Latitude	 地理位置纬度
     *Longitude	 地理位置经度
     *Precision	 地理位置精度
     */
    private function eventPullLocation(){

    }
    //------------------------------------------------------------------------------------------
    //自定义菜单事件
    //用户点击自定义菜单后，微信会把点击事件推送给开发者，请注意，点击菜单弹出子菜单，不会产生上报。
    //------------------------------------------------------------------------------------------
    /**
     *点击菜单拉取消息时的事件推送
     *参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 消息类型，event
     *Event	 事件类型，CLICK
     *EventKey	 事件KEY值，与自定义菜单接口中KEY值对应
     */
    private function eventPullClickMenuGetMessage(){
        $key=self::$wechat->postXML['EventKey'];
    }
    /**
     *点击菜单跳转链接时的事件推送
     *参数	描述
     *ToUserName	开发者微信号
     *FromUserName	 发送方帐号（一个OpenID）
     *CreateTime	 消息创建时间 （整型）
     *MsgType	 消息类型，event
     *Event	 事件类型，VIEW
     *EventKey	 事件KEY值，设置的跳转URL
     */
    private function eventPullClickMenuJumpToLink(){
        $url=self::$wechat->postXML['EventKey'];
    }

}