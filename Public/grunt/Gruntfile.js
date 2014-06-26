module.exports = function(grunt){
    // 构建任务配置
    grunt.initConfig({
        /**读取package.json的内容，形成个json数据*/
        pkg: grunt.file.readJSON('package.json'),
        /**合并js文件*/
        concat: {
            options: {
                separator: ';',
                stripBanners: true,
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'//标题注释
            },
            dist: {
                src: [],
                dest:""
            }
        },
        /**压缩js文件*/
        uglify: {
            options: {
            },
            dist: {
                files: {
                  //  'assets/js/default.min.js': 'assets/js/default.js'
                }
            }
        },
        /**less编译*/
        less:{
            options:{
                compress:false,//生成的文件是否删除空格进行压缩，默认不压缩。
                cleancss:false,//是否使用 clean-css 进行压缩，默认不使用。
                ieCompat:false,//例如，data-uri中包含了一个用base64编码的文件，并将其通过css引入，而IE8限制了data-uri的字节为32kb，因此开启ieCompat选项可防止超过这个数值。
                optimization:null,//设置解析器的优化级别。数字越低，创建的节点就越少。当进行调试或者想访问其中的节点可以设置这个选项。
                strictImports:false,//启用严格的导入模式，默认不启用。
                strictMath:false,//如果开启此项，则相关数学运算必须加上括号才有效。
                strictUnits:false,//当开启该选项后，编译时会检测less中的单位。例如: 4px/2px = 2 可以通过，而 4em/2px 则会报错。
                syncImport:false,//从磁盘中同步读取 @import 导入的文件。
                relativeUrls:false,//重写url为相对路径，默认false不开启。
                /*
                 * 可选择的值: false min gzip
                 默认值: false
                 report可以向我们展示less文件压缩前后以及服务器端开启gzip压缩后的css文件大小，可以很直观的看出使用clean-css的效果。
                 默认值为 false ，表示不展示任何信息；
                 当设置为 min 时会展示css压缩前和压缩后的文件大小；
                 当设置为 gzip 时，会展示css文件压缩前后以及服务器端开启gzip后的css文件大小。 必须说明的是当设置为 gzip 时，会花费原来5-10倍的时间才能完成本次任务。
                 下面是report设置为gzip时的显示结果：
                 Original: 198444 bytes.
                 Minified: 101615 bytes.
                 Gzipped:  20084 bytes.
                 */
                report:"min"
            },
            //后台首页登陆调试样式
            dev_admin_login:{
                files: {
                    "../build/debug/css/custom/admin_login.css":["../css/custom/adminLogin.less"]
                }
            },
            //后台每个设置文章的调试样式
            dev_admin_home:{
                files: {
                    "../build/debug/css/custom/admin.css":["../css/custom/admin.less"],
                    "../build/debug/css/custom/adminManage.css":["../css/custom/adminManage.less"],
                    "../build/debug/css/custom/commodityManage.css":["../css/custom/commodityManage.less"],
                    "../build/debug/css/custom/customMenu.css":["../css/custom/customMenu.less"],
                    "../build/debug/css/custom/otherSetting.css":["../css/custom/otherSetting.less"],
                    "../build/debug/css/custom/tradeManage.css":["../css/custom/tradeManage.less"],
                    "../build/debug/css/custom/wechat.css":["../css/custom/wechat.less"],
                    "../build/debug/css/custom/wechatSet.css":["../css/custom/wechatSet.less"],
                    "../build/debug/css/custom/wechatUserManage.css":["../css/custom/wechatUserManage.less"]
                }
            },
            production:{
                options:{
                    compress:true,
                    cleancss:true,
                    report:true
                },
                files: {
                    //压缩后台登陆页面
                    "../build/release/css/admin_login.min.css":["../css/lib/reset.css","../css/custom/adminLogin.css"],
                    //压缩后台除登陆页面的所有css
                    "../build/release/css/admin_home.min.css":["../css/lib/reset.css","../css/lib/jquery.datetimepicker.css","../css/compress/admin.less"]
                }
            }
        },
        //单元测试
        qunit:{},
        /**js语法规范检查*/
        jshint:{
            options:{
            curly: true,//作用：值为true时，不能省略循环和条件语句后的大括号.
            eqeqeq: false,//对于简单类型，使用===和!==，而不是==和!=
            //undef:true,// 查找所有未定义变量
            eqnull: true,//作用：值为false时，如果代码中使用"=="来比较变量与null，则JSHint会给出警告；值为true时，则不会.
            asi:false,//作用：值为false时，如果代码末尾省略了分号，则JSHint会给出警告；值为true时，则不会.
            boss:false,//作用：值为false时，如果预期为条件表达式的地方使用了赋值表达式，则JSHint会给出警告；值为true时，则不会.
            bitwise:true,//作用：值为true时，禁止使用位操作符，如"^，|，&"等.
           // camelcase:true,//作用：值为true时，变量名必须使用驼峰风格（如"loginStatus"）或UPPER_CASE风格（如"LOGIN_STATUS"）.
            indent:4,//作用：该选项要求你的代码必须使用指定的tab缩进宽度，如"indent:4"
          //  latedef:true,//作用：值为true时，禁止在变量定义之前使用它.
            newcap:true,//作用：值为true时，构造函数名需要大写.
            noarg:true,//作用：值为true时，禁止使用arguments.caller与arguments.callee.
           // noempty:true,//作用：值为true时，不允许代码中出现空的语句块（"{}"）
           // nonew:true,//作用：值为true时，禁止使用产生副作用的构造器调用方式，如"new MyConstructor();"
           plusplus:false,//作用：值为true时，禁止使用一元递增（"++"）和递减（"--"）运算符.
          // quotmark:true,//作用：该选项用于统一代码中的引号风格，可选的值有三个：(1) single -- 只能使用单引号；(2) double -- 只能使用双引号；(3) true -- 两者任选其一，但不能同时出现.
            strict:false,//作用：值为true时，该选项会要求所有函数在ECMAScript 5的严格模式中运行.
            maxparams:5,//作用：该选项用于设置每个函数形参数量的上限，如"maxparams:3".
            maxdepth:3,//作用：该选项用于设置每个函数中代码块嵌套层级的上限，如"maxdepth:1".
            debug:false,//作用：值为false时，如果代码中有debugger语句，则JSHint会给出警告；值为true时，则不会.
            esnext:false,//作用：值为true时，JSHint会使用ECMAScript 6的语法来校验你的代码.
            evil:false,//作用：值为false时，不允许在代码中使用eval.
            funcscope:false,//作用：值为false时，如果在控制语句中定义了变量，却在控制语句之外使用变量，则JSHint会给出警告.
            globalstrict:false,//作用：值为false时，不允许使用全局级别的严格模式.
            loopfunc:false,//作用：值为true时，允许在循环中定义函数；值为false时，会给出警告
            moz:true,//作用：该选项告诉JSHint，你的代码中使用了Mozilla JavaScript扩展.
            multistr:true,//作用：值为true时，允许多行字符串；值为false时，则会给出警告.
            proto:false,//作用：值为true时，允许在代码中使用__proto__属性；值为false时，则会给出警告.
            scripturl:false,//作用：值为true时，允许在代码中使用"javascript:..."这样的url；值为false时，则会给出警告.
            sub:false,//作用：值为true时，允许用obj['name']和obj.name两种方式访问对象的属性；值为false时，不允许使用obj['name']方式，除非只能使用这种方式访问.
            globals: {
                jQuery: true,
                Backbone:true
            },
                //输出信息流默认的是grunt内置的你可以自定义或者使用jslint和checkstyle这俩个是xml结构
                //也可以用jshint-stylish需要npm安装npm install --save-dev jshint-stylish -g
            reporter:require('jshint-stylish'),
            //reporterOutput:"hintLog.md",//把控制台信息流写入指定文件
            force:true//强制继续
            },
            js:{
                 files:{
                     src:[
                         'Gruntfile.js',
                         "../js/bootstrap/*.js",
                         "../js/controller/*.js",
                         "../js/model/*.js",
                         "../js/template/*.js",
                         "../js/test/*.js",
                         "../js/view/*.js"
                     ]
                 }
             }
        },
        /**html检查*/
        htmlhint:{},
        /**自动监控执行grunt自动化程序*/
        watch: {
             options:{
                        spawn: true,
                       // debounceDelay: 10000,//连续释放相同路径和状态的事件之前等待的时间。比如，你的Gruntfile.js文件发生了改变，一个改变（changed）事件，只有经过给定的毫秒数后，才能再次触发。
                        event:['added', 'deleted','changed'],
                        interval:5000,
                        interrupt: true

             },
            less:{
                files:['../css/custom/*.less'],
                tasks:['less:dev_admin_home'],
            },
            jshint:{
              files:["../js/bootstrap/*.js","../js/controller/*.js", "../js/model/*.js","../js/template/*.js","../js/test/*.js","../js/view/*.js"],
              tasks:['jshint']
            }
        },
        /**打开多个浏览器查看兼容性*/
        browserSync:{},
        /**文档生成工具*/
        jsdoc:{
          shopDOC:{
            src:[
                   "../js/bootstrap/*.js","../js/controller/*.js",
                   "../js/model/*.js","../js/template/*.js", "../js/test/*.js",
                   "../js/view/*.js","../../DevDoc/jsdoc/jsdoc.md"
                  ],
            options: {
                destination: '../../DevDoc/QGShopDoc',
                template : "node_modules/ink-docstrap/template",
                configure : "node_modules/ink-docstrap/template/jsdoc.conf.json"
            }
          }
        }
    });

    //加载Grunt插件
      grunt.loadNpmTasks('grunt-contrib-watch');
      grunt.loadNpmTasks('grunt-contrib-uglify');
      grunt.loadNpmTasks('grunt-htmlhint');
      grunt.loadNpmTasks('grunt-contrib-jshint');
      grunt.loadNpmTasks('grunt-contrib-qunit');
      grunt.loadNpmTasks('grunt-contrib-less');
      grunt.loadNpmTasks('grunt-contrib-concat');
      grunt.loadNpmTasks('grunt-browser-sync');
      grunt.loadNpmTasks('grunt-contrib-coffee');
      grunt.loadNpmTasks('grunt-jsdoc');

    //默认的Grunt任务
  //  grunt.registerTask('default',['Grunt任务']);
};