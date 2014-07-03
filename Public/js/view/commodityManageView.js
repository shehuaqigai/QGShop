(function(){
    //首页视图生成页
    var commodityManage = Backbone.View.extend({
        el:"#commodityManage",
        events:{            //添加分类           //添加子分类           //更新分类
          'click .pro_cate .add_cate,.pro_cate .add_subCate,.pro_cate .updateCate':'Category',
          'click .pro_cate .deleteCate':'deleteCate',//删除指定分类
          'click .pro_cate .icon-contract2,.pro_cate .icon-expand2':'openOrCloseCate',//关闭或者打开分类
          'click .close,.add_categoryAction .cancel_submit':'closeDialog',
          'click .add_categoryAction .formSubmit':'cateFormSubmit',
          'change .add_categoryAction select.cateSelect':'selectCateHandler',
          'click .pro_cate tfoot button.deleteSelect':"deleteSelectIds",
          'click .pro_cate tfoot input[name="allSelect"]':"isSelectIds",
          'click .cateNameEdite,.icon-pencil':"editeCateName",
          'change .inputEditeCateName':"inputEditeCateName"
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
            this.$el.find("tbody.dataLists").html(this.template.p_m_td({tableContent:lists,cate:cate}));
        },
        /**
         * 商品分类功能
         */
        productCategory:function(button){
            this.$el.prepend(this.template[button]({table_th:this.mixData.cateTitle}));
            this.$el.find("tbody.cateLists").html(this.template.p_c_td({tableContent:this.cateTable,pid:0}));
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
                temp=self.template.selectCate({options:cateTable,pid:0,id:null});
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
                    var $prompt=self.$el.find(".add_cate_prompt");
                    if(!res){
                        $prompt.html("数据提交失败！");
                    }else{
                        var result=(0,1,eval)('('+obj.response+')');
                        var createCate=_.extend(self.upload.settings.multipart_params,result);
                        cateTable.push(createCate);
                        $prompt.html("提交成功！");
                    }
                });
            }
            dialog.show();
        },
        /**
         * 打开或者折叠分类
         * @param e
         */
        openOrCloseCate:function(e){
            var ele= e.target;
            var self=this;
            var _class=ele.className;
            var cateTable=this.cateTable;
            var cateList=this.$el.find(".pro_cate .cateLists");
            var $tr=$(ele).parents("tr");
            var id=$tr.attr("id");
            var currentHtml=$(ele).parent().html();
            if(_class == "icon-contract2"){
                ele.className="icon-expand2";
                $tr.after(this.template.p_c_td({tableContent:cateTable,pid:id}));
                var _TD=cateList.find("tr[pid="+id+"] .cateName");
                //对该分类下的子分类进行层级排版
                //类似--田分类名|分类名|....   田是分类符号点击可以扩展,--是层级缩进;
                _TD.each(function(i,ele){
                    var $ele=$(ele);
                    var span=$ele.find("span");
                    var name=span.html();
                    $ele.html(currentHtml);
                    $ele.prepend("----");
                    $ele.find("span").html(name);
                });
            }else{
                ele.className="icon-contract2";
                deleteSubCate(id);
            }
            //删除子类下面的分类
            function deleteSubCate(pid){
                var $trs=cateList.find("tr[pid='"+pid+"']");
                if($trs.length>0){
                    $trs.each(function(i,ele){
                        var id=ele.id;
                        $(ele).remove();
                        deleteSubCate(id);
                    });
                }
            }


        },
        /**
         * 编辑分类名称
         */
        editeCateName:function(e){
           var $ele=this.$el.find(e.target);
            var self=this;
           var td=$ele.parent();
           var edite=td.find(".cateNameEdite");
            edite.replaceWith("<input type='text' value='"+edite.html()+"' class='inputEditeCateName' style='color:black;'/>");
           var input=td.find(".inputEditeCateName");
            input.focus();
            input.blur(function(e){
                self.inputEditeCateName(e);
            });

        },
        inputEditeCateName:function(e){
            var ele= e.target;
            var val=ele.value;
            var $ele=this.$el.find(ele);
            var id=$ele.parents("tr").attr("id");
            var model=this.cateTable.get(id);
            var url=this.prifix_url+"update_cateName";
            $ele.replaceWith('<span class="cateNameEdite">'+val+'</span>');
            model.urlRoot=url;
            model.save({name:val},{success:function(mod,response,options){
                if(response){console.log("更新成功");}
            },error:function(){console.log("错误");}});

        },
        /**
         * 图片上传渲染
         * @param url
         * @param callback
         */
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
        /**
         * 如果是点击的添加子分类和编辑
         * 就渲染这里的选项ui
         * 遍历该id上面的分类层级
         * @param pid
         * @param id
         * @param cateTable
         * @returns {string}
         */

        addSubCate:function(pid,id,cateTable){
            var pids=[];//pid数组
            var ids=[];//id数组
            var parentModel;
            var self=this;
            var temp='';
            pids.push(pid);
            ids.push(id);
            while(parseInt(pid) !==0){
                //获取父id的pid
                id=pid;
                parentModel=cateTable.get(id);
                ids.unshift(id);
                pid=parentModel.get("pid");
                pids.unshift(pid);
            }
            _.each(pids,function(value,key){
                console.log(ids[key]);
                temp+=self.template.selectCate({options:cateTable,pid:value,id:ids[key]});
            });
            return temp;
        },
        /**
         * 当点击删除按钮的时候
         * 对分类进行删除的动作
         * @param e
         */
        deleteCate:function(e){
            var ele= e.target;
            var self=this;
            var cateTable=this.cateTable;
            var id=ele.getAttribute("id");
            var $tr=$(ele).parents("tr");
            var urlRoot=this.prifix_url+"delete_cate";
            var model=cateTable.get(id);
            model.urlRoot=urlRoot;
            $.Zebra_Dialog('<strong>你确定要删除吗!</strong>',
                {
                    type:"confirmation",
                    title:"确认框",
                    buttons:[
                        {caption:"是",callback:function(){
                            model.destroy({wait:true,success:function(mod,response,options){
                                mod=null;
                                self.getSubCateIdsDelete(id,cateTable,self);
                                $tr.remove();
                                $tr=null;
                            },error:function(mod,response,options){
                                console.log("删除数据出错");
                            }});

                        }},
                        {caption:"否"}
                    ]
                });






        },
        /**
         * 获取子类的所有id
         * 如果删除一个分类就删除这个分类下面的所有分类
         * @param id
         */
        getSubCateIdsDelete:function(id,cateTable,self){
              cateTable.each(function(value){
                  if(value.get("pid") == id){
                      cateTable.remove(value);
                      self.getSubCateIdsDelete(value.id,cateTable,self);
                      value=null;
                  }
              });
              $(".pro_cate tr[id='"+id+"']").remove();
        },
        /**
         * 当点击编辑按钮后派发到这个方法来处理
         * @param id
         */
        updateCate:function(id){
              var cate=this.cateTable.get(id);
              var url=this.prifix_url+"update_cate";
              var dialog=this.$tempDialog;
            dialog.find(".add_categoryAction").attr("status","update");
              var name=dialog.find("input.cate_name");
              var is_index=parseInt(cate.get('is_index'));
               name.val(cate.get('name'));
               if( 1==is_index){
                dialog.find("input[type='radio'].show").attr("checked","checked");
               }else{
                dialog.find("input[type='radio'].hide").attr("checked","checked");
               }
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
         * 选择要取消或者删除的分类
         * @param e
         */
        isSelectIds:function(e){
            var isSelect= e.target.checked;
            var ids=$(".pro_cate input[name='selectOrCancelId']");
            if(isSelect){
                   ids.each(function(i,ele){
                     ele.checked=true;
                   });
               }else{
                ids.each(function(i,ele){
                    ele.checked=false;
                });
               }

        },
        /**
         * 删除选择的分类
         * @param e
         */
        deleteSelectIds:function(e){

            var ids=$(".pro_cate input[name='selectOrCancelId']:checked");
            var cateTable=this.cateTable;
            var urlRoot=this.prifix_url+"delete_cate";
            var self=this;
            if(!ids.length){
                $.Zebra_Dialog('<strong>你还没有选择要删除的分类</strong>');
                return;
            }
            $.Zebra_Dialog('<strong>你确定要删除吗!</strong>',{
                type:"confirmation",
                title:"确认框",
                buttons:[
                    {
                        caption:"是",
                        callback:function(){
                            ids.each(function(i,ele){
                                var id=ele.value;
                                var model=cateTable.get(id);
                                model.urlRoot=urlRoot;
                                model.destroy({
                                    wait:true,
                                    success:function(mod,response,options){
                                        mod=null;
                                        self.getSubCateIdsDelete(id,cateTable,self);
                                    },
                                    error:function(mod,response,options){
                                        console.log("删除数据出错");
                                    }
                                });

                            });
                            ids.parents("tr").remove();
                        }

                    },
                    {caption:"否"}
                ]
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
                $select.after(this.template.selectCate({options:cateTable,pid:cateId,id:null}));
            var next=$select.next();
            if(next[0].options.length==1){

                next.remove();
                return;
            }
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
            var id;
            if(status=="update"){
              id=this.addOrSubCate.id;
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
})();