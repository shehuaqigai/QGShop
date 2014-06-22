module.exports = function(grunt){
    // 构建任务配置
    grunt.initConfig({
        //读取package.json的内容，形成个json数据
        pkg: grunt.file.readJSON('package.json'),
        //合并js文件
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
        //压缩js文件
        uglify: {
            options: {
            },
            dist: {
                files: {
                  //  'assets/js/default.min.js': 'assets/js/default.js'
                }
            }
        },
        //less编译
        less:{},
        //单元测试
        qunit:{},
        //js语法规范检查
        jshint:{},
        //html检查
        htmlhint:{},
        //自动监控执行grunt自动化程序
        watch: {
            /*
            css: {
                files: ['public/scss/*.scss'],
                tasks: ['compass'],
                options: {
                    // Start a live reload server on the default port 35729
                    livereload: true
                },
            },
            another: {
                files: ['lib/*.js'],
                tasks: ['anothertask'],
                options: {
                    // Start another live reload server on port 1337
                    livereload: 1337
                }
            }
            */
        },
        //打开多个浏览器查看兼容性
        browserSync:{},
        //文档生成工具
        jsdoc:{}
    });

    //加载Grunt插件
      grunt.loadNpmTasks('grunt-contrib-watch');
      grunt.loadNpmTasks('grunt-contrib-uglify');
      grunt.loadNpmTasks('grunt-htmlhint');
      grunt.loadNpmTasks('grunt-contrib-jshint');
      grunt.loadNpmTasks('grunt-contrib-qunit');
      grunt.loadNpmTasks('grunt-contrib-less');
      grunt.loadNpmTasks('grunt-contrib-concat');
    //默认的Grunt任务
  //  grunt.registerTask('default',['Grunt任务']);
};