<?php
/**
  *   +----------------------------------------------------------------------
  *   | 微商城项目启动文件
  *   +----------------------------------------------------------------------
  *   | Copyright (c) 2014-2016 http://qiugeWeChatShop.cn All rights reserved.
  *   +----------------------------------------------------------------------
  *   | Author: yangchangqiu <shehuaqigai@gmail.com>
  *   +----------------------------------------------------------------------
*/
// 应用入口文件
/**
get_defined_functions();//获取全局的函数包括内置和自定义的是个二维数组[internal=>Array[1422],user=>Array[0]];
get_defined_constants(true);//获取全局的常量
get_defined_vars();跟$GLOBALS是一样的前者是是个数组
*/
require './vendor/autoload.php';//第三方类库自动加载器
// 检测PHP环境detect php enviroment
if(version_compare(PHP_VERSION,'5.3.0','<')) die('require PHP > 5.3.0 !');
//应用目录
define('APP_PATH','./Wechat/');

// 定义框架
define('THINK_PATH','./TP/');
//定义运行时的目录
define('RUNTIME_PATH',APP_PATH.'Runtime/');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

//默认安全文件
//define('DIR_SECURE_FILENAME', 'index.html');
//访问默认安全文件的返回内容
//define('DIR_SECURE_CONTENT', 'deney Access!');

//生成后台模块
//define('BIND_MODULE','Admin');

/**如果你的环境足够安全，不希望生成目录安全文件，可以在入口文件里面关闭目录安全文件的生成，例如：*/
define('BUILD_DIR_SECURE', false);//不生成默认文件


//默认存储类型
define('STORAGE_TYPE','File');


/**
 * 每个应用模式有自己的定义文件，用于配置当前模式需要加载的核心文件和配置文件，以及别名定义、行为扩展定义等等。
 * 除了模式定义外，应用自身也可以独立定义模式文件。
 * 默认情况下的应用模式是普通模式（common），
 * 如果要采用其他的应用模式（当然，前提是已经有定义），必须在入口文件中定义，设置APP_MODE常量即可
 */
define('APP_MODE','common');//当前应用模式名称

//每个应用都可以在不同的情况下设置自己的状态（或者称之为应用场景），并且加载不同的配置文件。
define('APP_STATUS','office');


//define("BUILD_LITE_FILE",true);
//Logic       逻辑目录（可选）
//Service     Service目录（可选）
// 引入ThinkPHP入口文件
require THINK_PATH.'TP.php';



























/**
 * ThinkPHP框架流程分析
 * 第一步 创建一个入口文件index.php可以配置一些常量定义应用的目录，框架的目录然后引入框架的thinkPHP.php(TP.php)
 *
 *
 * 第二步 执行TP.php ：tp.php步骤 ,记录开始运行时间,记录内存初始使用,thinkphp的版本定义,url模式定义
 *  0普通模式 1,PATHINFO模式 2,REWRITE模式 3,兼容模式
 * 定义类文件后缀常量EXT=".class.php"
 * 然后定义很多的路径常量对5.4以下的设置magic_quotes_runtime自动安全过滤状态，
 * 在判断php执行环境(cgi,apachehandler,命令模式)，判断操作系统,定义index.php文件路径,网站根目录路径
 * // 加载核心Think类 ThinkPHP/Library/Think/Think.class.php 然后调用Think\Think::start();方法(Think\是命名空间)
 * 第三步执行ThinkPHP/Library/Think/Think.class.php里的start()方法;
 * 注册AUTOLOAD方法就是自动加载类用spl_autoload_register注册加载函数
 * register_shutdown_function退出php进程的时候触发的注册函数
 * set_error_handler注册自定义的错误处理函数
 * set_exception_handler注册定义的异常处理函数
 * // 初始化文件存储方式 Storage::connect(STORAGE_TYPE);默认是文件类型
 * storage::connect调用后实例化Think\Storage\Driver\File.class.php对象
 * 判断是不是没有开启调试模式并且runtime目录下有没有 APP_MODE.'~runtime.php文件有得话就加载
 * （如果是调试模式或者非调试模式但是文件不存在）存在~runtime.php的话就删除这个文件
 * // 读取应用模式
//CONF_PATH.'core.php'这个文件是自定义的应用模式，默认的是普通应用模式
//应用模式设置包括俩种一种是显示设置，一种是隐士设置 core.php就是隐士设置
 *应用模式文件包括四种配置// 配置文件 // 别名定义// 函数和类文件 // 行为扩展定义
 * 读取应用模式后第一部加载核心文件(函数和类文件)然后加载应用模式配置文件 加载模式别名定义 加载应用别名定义文件
 * 加载模式行为定义 加载应用行为定义 加载框架底层语言包
 * 如何是非调试模式就编译这些加载文件然后写入~runtime.php
 * 否则的话就加载系统默认的配置文件读取应用调试配置文件
 * 读取当前应用状态对应的配置文件(应用场景）家里或办公
 * 设置系统时区
 * 检查应用目录结构 如果不存在则自动创建
 * 记录加载文件时间
 *  // 运行应用
  App::run();

/**
define与const的区别
1、const用于类成员变量定义，一旦定义且不能改变其值。define定义全局常量，在任何地方都可以访问。
2、define不能在类中定义而const可以。
3、const不能在条件语句中定义常量
4、const采用一个普通的常量名称，define可以采用表达式作为名称。
5、const只能接受静态的标量，而define可以采用任何表达式。
6、const 总是大小写敏感，然而define()可以通过第三个参数来定义大小写不敏感的常量
使用const简单易读，它本身是一个语言结构，而define是一个方法，用const定义在编译时比define快很多。
 *
 */
