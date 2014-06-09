(function(){
    //首页视图生成页
    var commodityManage=Backbone.View.extend({
        el:"#commodityManage",
        events:{
          'click .pro_cate .add_cate':'Category',//添加分类
          'click .pro_cate .add_subCate':'Category',//添加子分类
          'click .pro_cate .updateCate':'Category',//更新分类
          'click .close,.add_categoryAction .cancel_submit':'closeDialog',
          'click .add_categoryAction .formSubmit':'cateFormSubmit',
          'change .add_categoryAction select.cateSelect':'selectCateHandler'
        },
        template:ADMIN.tpl,//模板
        mixData:ADMIN.global.mixData,//其他一些数据对象
        upload:null,//上传对象
        addOrSubCate:null,//当前动作是添加分类还是子分类
        $tempDialog:null,//浮框容器对象
        cateTable:null,//商品分类表
        brandTable:null,//商品详情表
        prifix_url:null,//请求后台的网址前缀
        //初始化应用
        initialize:function(param){
            var self=this;
            var button=param.item;
            this.prifix_url=ADMIN.global.ADMINPATH+'CommodityM/';
            new this.collection.cateTable(function(collection,data,options){
                self.cateTable=collection;
                var brandTable=new self.collection.brandMangageTable(function(collection){
                    self.brandTable=collection;
                    self.init(button);
                });
            });
            this.$tempDialog=this.$el.find('#tem_Dialog');
        },
        init:function(button){
            if(button == 'add_categoryAction'){
            }else{
               this.$el.empty();
               this.$tempDialog.hide().empty();
               this.$el.append(this.$tempDialog);
            }
            this.renderSwitch(button);

        },
        /**
         * 渲染各个视图设置
         * @param button
         */
        renderSwitch:function(button){
            this[button](button);
        },
        /**
         * 产品管理功能部分
         */
        productManage:function(button){
            this.$el.prepend(this.template[button]({options:this.cateTable,table_th:this.mixData.productTitle}));
            this.$el.find('#dateTimePickerStart').datetimepicker({
                    timepicker:false,
                    format:'Y-m-d',//显示方式(年-月-日,日-月-年);
                    formatDate:'Y-m-d'//格式
                });
            this.$el.find('#dateTimePickerEnd').datetimepicker({
                    timepicker:false,
                    format:'Y-m-d',//显示方式(年-月-日,日-月-年);
                    formatDate:'Y-m-d'//格式
                });
            this.showBrandData(this.brandTable,this.cateTable);

        },
        /**
         * 添加商品数据
         * @param lists
         * @param data
         */
        showBrandData:function(lists,data){
            var cate={};
            data.each(function(value){
               cate[value.get('id')]=value.get('name');
            });
            this.$el.find("tbody.dataLists").html(this.template['p_m_td']({tableContent:lists,cate:cate}));
        },
        /**
         * 商品分类功能
         */
        productCategory:function(button){
            this.$el.prepend(this.template[button]({table_th:this.mixData.cateTitle}));
            this.$el.find("tbody.cateLists").html(this.template['p_c_td']({tableContent:this.cateTable}));
        },
        /**
         * 触发添加分类和添加子分类动作
         * 分配到指定路由
         * @param e
         */
        Category:function(e){
            var ele= e.target;
            var pid=ele.getAttribute("pid");
            var id=ele.getAttribute("id");
            var status=ele.getAttribute("status");
            this.addOrSubCate={pid:pid,id:id,status:status};
            var time=new Date();
           window.location.href=ADMIN.global.ADMINPATH+'Index/adminHome#commodityManage/add_categoryAction?time='+time.getTime();
        },
        /**
         * 添加分类ui创建
         * @param button
         */
        add_categoryAction:function(button){
            var dialog=this.$tempDialog;  //浮层容器
            var cateTable=this.cateTable;//分类表
            var self=this;
            //添加分类还是添加子分类
            var add_sub=this.addOrSubCate;
            var pid=add_sub.pid;
            var id=parseInt(add_sub.id);
            var status=add_sub.status;
            var temp='';
            //添加分类模板
            if(status=="addCate" || pid =="0"){
                temp=self.template['selectCate']({options:cateTable,pid:0,id:null});
            }else{
                temp=this.addSubCate(pid,id,cateTable);            //如果是添加子类进来和更新分类进来的

            }
            dialog.html(this.template[button]());
            dialog.find(".add_cate_select").append(temp);
            if(status=="update"){
                this.updateCate(id);
            }else{
                var url=this.prifix_url+'add_cate';
                this.imageUpload(url,function(up, file, obj) {
                    var res=obj.response;
                    if(!res){
                        var $prompt=self.$el.find(".add_cate_prompt");
                        $prompt.html("数据提交失败！");
                    }else{
                        var result=(0,1,eval)('('+obj.response+')');
                        var createCate=_.extend(self.upload.settings.multipart_params,result);
                        cateTable.push(createCate);
                    }
                });
            }
            dialog.show();
        },
        imageUpload:function(url,callback){
            var $upload=$("#uploader");
            var Public=ADMIN.global.APPPATH+'Public/';
            $upload.pluploadQueue({
                // General settings
                runtimes : 'html5,flash,silverlight,html4',
                url : url,
                chunk_size: '1mb',
                rename : true,
                dragdrop: true,
                filters : {
                    // Maximum file size
                    max_file_size : '0.5mb',
                    // Specify what files to browse for
                    mime_types: [
                        {title : "Image files", extensions : "jpg,gif,png"},
                        {title : "Zip files", extensions : "zip"}
                    ]
                },
                // Resize images on clientside if we can
                resize : {width : 320, height : 240, quality : 90},
                flash_swf_url : Public+'js/lib/plupload/Moxie.swf',
                silverlight_xap_url : Public+'js/lib/plupload/Moxie.xap'
            });
            this.$el.find('#uploader_filelist').css({'height':"7rem","overflow-y":"hidden"});
            var upload=$upload.pluploadQueue();
            this.upload=upload;
            upload.bind("FilesAdded",function(up){
                $.each(up.files, function (i, file) {
                    if (up.files.length <= 1) {
                        return;
                    }
                    $.Zebra_Dialog('<strong>哎呦!</strong>,忘记告诉你只能上传一张图片',
                        {
                            'type':     'warning',
                            'title':    '警告'
                        });
                    up.removeFile(file);
                });
            });
            upload.bind("FileUploaded",callback);


        },

        addSubCate:function(pid,id,cateTable){
            var pids=[];
            var parentModel;
            var temp='';
            pids[id]=pid;
            while(parseInt(pid) !=0){
                //获取父id的pid
                id=pid;
                parentModel=cateTable.get(id);
                pid=parentModel.get("pid");
                pids[id]=pid;
            }
            pids.push(0);
            pids.reverse();
            _.each(pids,function(value,key){
                temp+=self.template['selectCate']({options:cateTable,pid:value,id:key});
            });
            return temp;
        },
        updateCate:function(id){
              var cate=this.cateTable.get(id);
              var url=this.prifix_url+"update_cate";
              var dialog=this.$tempDialog;
            dialog.find(".add_categoryAction").attr("status","update");
              var name=dialog.find("input.cate_name");
              var is_index=parseInt(cate.get('is_index'));
               name.val(cate.get('name'));
               is_index == 1 ? dialog.find("input[type='radio'].show").attr("checked"):dialog.find("input[type='radio'].hide").attr("checked");
               dialog.find("input.ordid").val(cate.get("ordid"));
            this.imageUpload(url,function(up,file,data){
                var res=data.response;
                if(!res){
                    var $prompt=dialog.find(".add_cate_prompt");
                    $prompt.html("数据提交失败！");
                }else{
                    var result= data.response;
                    var cateUpdate=up.settings.multipart_params;
                        cateUpdate.img=result;
                        cate.set(cateUpdate);
                }
            });
        },
        /**
         * 在添加分类中进行分类选择的时候触发的动作处理
         * @param e
         */
        selectCateHandler:function(e){
            var select= e.target;
            var cateTable=this.cateTable;
            var $select=$(select);
            var spid=$select.attr('data-spid');
            var index=select.options.selectedIndex;
            var value=select.options[index].value;
            var cateId=parseInt(value);
                $select.nextAll().remove();
            if(value =="self"){return;}
                $select.after(this.template['selectCate']({options:cateTable,pid:cateId,spid:spid,id:null}));
            var next=$select.next();
            if(next[0].options.length==1){

                next.remove();
                return;
            }
            //spid就是层级关系按父id来连接
            spid !=0 ? next.attr("data-spid",spid+cateId+"|") : next.attr("data-spid",cateId+"|");
            next.attr("data-pid",cateId);
        },
        //文件开始上传事件
        cateFormSubmit:function(e){
            var upload=this.upload;
            var dialog=this.$tempDialog;
            var $prompt=this.$el.find(".add_cate_prompt");
            var name=dialog.find("input.cate_name").val();
            var select=dialog.find(".cateSelect").last();
            var status=dialog.find(".add_categoryAction").attr("status");
            var pid;
            //默认情况下是本级分类
            var parentPid=parseInt(select.attr("data-pid"));
            //如果选择了分类，这个分类下面还没有分类的话
            var index=select[0].options.selectedIndex;
            var cateValue=select[0].options[index].value;
            //是否首页显示
            var indexShow=dialog.find("input:checked").val();
            //排序
            var ordid=dialog.find("input.ordid").val();
            if(!name){
                $prompt.html("分类名称不能为空!");
                return;
            }
            if(cateValue=="self"){
                pid=parentPid;
            }else{
                pid=parseInt(cateValue);
            }
            if($("a.plupload_start").hasClass('plupload_disabled')) {
               $prompt.html("亲,要上传一张图片哦!");
                return;
            }
            var createCate={
                name:name,
                pid:pid,
                is_index:indexShow,
                ordid:ordid
            };
            if(status=="update"){
                var id=this.addOrSubCate.id;
            }
            createCate.id=id;
            upload.settings.multipart_params=createCate;
            upload.start();
            e.preventDefault();
        },
        /**
         * 添加商品功能
         */
        addProduct:function(){},
        /**
         * 品牌分类功能
         */
        brandManage:function(){},
        //页面渲染
        render:function(){

        },
        closeDialog:function(e){
            this.$tempDialog.hide().empty();
        },
        destroy:function(){
            this.el.innerHTML='';
            this.remove();
        }

    });
    window.ADMIN.V.commodityManageView=commodityManage;
}())