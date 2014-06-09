(function(){
    //首页视图生成页
    var indexView=Backbone.View.extend({
        el:"#container",
        events:{
            "click #navMark>ul":'navEventHandler',//左边导航列表事件处理
            "click #content .category":'categoryEventHandler'//分类点击事件处理
        },
        navScroll:null,//章节列表滑动对象
        $article:null,//文章展示容器对象
        $chapter:null,//文章容器对象下得chapter对象
        articleScroll:null,//文章容器滑动事件
        $ul:null,//导航列表ul容器对象
        template:APP.JST['homeUI'],//首页模板
        models:null,//表的集合backbone.collection
        //初始化应用
         initialize:function(){
           this.models=this.collection;
           this.render();
           //创建导航功能（鼠标滚动和拖动）
           this.navScroll=new IScroll('#navMark',{mouseWheel: true, click: true });
           this.articleScroll=new IScroll('#articleContent',{mouseWheel: true, click: true });
         },
        //页面渲染
         render:function(){
             this.$el.html(this.template());
             this.$article=this.$el.find("#article");
             this.$chapter=this.$el.find("#chapter");
             this.$ul=this.$el.find("#navMark>ul");
             //创建章节导航列表初次进入默认为基础知识类
             this.createChapterNavList(1);
             //创建分类列表
             this.createCategory();
         },
        //创建章节导航列表
         createChapterNavList:function(type){
        var navList=''
             //遍历chapter表
             this.models.chapter.each(function(value,key){
                 //如果类型为1就是php的基础知识内容
                 if(value.attributes.type==type){
                     navList+=APP.JST['navListUI'](value.toJSON());
                 }

             });
        //把导航列表插入左边导航
        this.$ul.html(navList);
        //绑定导航列表事件

    },
        //创建手册的分类列表(手册总共分几大类)
         createCategory:function(){
            var cateList='';
            var $cate=$("#content .category");
             //遍历category表
             this.models.category.each(function(value){
                 //如果类型为1就是php的基础知识内容
                     cateList+=APP.JST['cateListUI'](value.toJSON());
             });
            $cate.append(cateList);
        },
        //绑定手册分类事件列表
         categoryEventHandler:function(evt){
            var curEle=evt.target;//当前元素
            var nodeName=curEle.nodeName;//当前元素节点
            var type=curEle.getAttribute("type");//当前元素的分类type
            var className=curEle.className;//当前元素的类名
            if(this.$ul.hasClass(className)){return;}
            if("NAV" == nodeName){
                this.$ul.removeClass().addClass(curEle.className);
                this.createChapterNavList(type);
                this.navScroll.refresh();

            }
        },
        //章节列表导航事件处理
         navEventHandler:function(Evt){
            //点击的目标
            var target=Evt.target;
            //转换成jquery对象
            var $target=$(target);
            var self=this,pclassName;
            //当前标签名
            var tag=target.nodeName;
            //当前状态
            var status=target.getAttribute("status");
            //如果是关就打开
            if('LI'==tag && status && 'close' == status){
                target.style.height='auto';
                target.setAttribute("status",'open');
                this.insertContent(target);
                //如果是开就关上改状态为光，删除内联样式，刷新滑动
            }else if('LI'==tag && status && 'open' == status){
                $target.find(".pList").slideUp(500,'linear',function(){
                    $target.find(".pList").remove();
                    target.setAttribute("status","close");
                    target.removeAttribute("style");
                    self.navScroll.refresh();
                });
            }
            //如果是打开状态里面是具体的文字列表就请求文章然后展示
            if('P' == tag){
                pclassName=target.className.replace(" icon-file3",'');
                this.requestContent(pclassName,target.innerHTML);
            }
        },
        //插入内容列表
         insertContent:function(target){
            //获取当前的章节id
            var chapterId=target.getAttribute("chapter_id");
            //创建容器
            var content=document.createDocumentFragment();
            //p标签列表容器
            var pList=document.createElement("div");
            pList.className="pList";
            content.appendChild(pList);
            var eleP='';
            //把当前章的所有文章列表加入到容器
             this.models.content.each(function(value){
                 if(chapterId == value.get('chapter_id')){
                     eleP=document.createElement("p");
                     eleP.className=value.get('mark')+" icon-file3";
                     eleP.innerHTML="  "+value.get('title');
                     pList.appendChild(eleP);
                 }
             });

            //插入p列表
            target.appendChild(content);
            //刷新滑动
            this.navScroll.refresh();

        },
        //请求内容
         requestContent:function(file,title){
            var dir=file.replace(/_/g,'/');
            this.ajaxHandler(dir,title);

        },
        //ajax处理
         ajaxHandler:function(dir,title){
            this.$article.find(".articleTitle").html(' '+title);
            this.$chapter.empty();
            this.$chapter.append(APP.JST['iconBusyUI']());
            var url="./packages/resource/"+dir+'.html';
            var defer=$.ajax(url,{
                type:"get",
                dataType:"html",
                timeout:5000,
                context:this,
                cache:false
            });
            defer.then(function(data){
                this.$chapter.empty();
                //由于开发环境原因我有得时候在window下开发，有得时候在mac下，有得时候在linux下，由于三个平台
                //换行符不一样所以进行了处理window(\r\n)linux(\n)mac(\r)    \r回车\n换行
               var strimStr= data.replace(/(\r)*\n/g,"<br\/>");
                document.querySelector("#chapter").innerHTML= strimStr;
                this.articleScroll.refresh();
            },function(err){

            })

        }
        });
        window.APP.V.indexView=indexView;
}())