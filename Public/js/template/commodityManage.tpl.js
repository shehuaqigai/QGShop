(function(){
    /**
     * 这个模板是商品管理子功能的页面
     * @type {*}
     */
    ADMIN.tpl['productManage'] = _.template('' +
     '<div>' +
        '<div class="searchBox">' +
            '<div>' +
                '发布时间:<input type="text" id="dateTimePickerStart"/>-<input type="text"  id="dateTimePickerEnd"/>'+
                '分类:<select name="category" class="product_category">' +
                     "<% options.each(function(value){ if(value.get('pid')==0){%>" +
                        "<option value='<%= value.get('id') %>'><%= value.get('name') %></option>" +
                      "<% }}) %>"+
                '</select>' +
                '是否上架: <select name="isUp" class="is_up">' +
                    '<option value="all" selected>--所有--</option>' +
                    '<option value="1">上架</option>' +
                    '<option value="0">下架</option>' +
                '</select>'+
                '</div>' +
            '<div>' +
                '价格区间:<input type="text" class="priceMin"/>-<input type="text" class="priceMax"/>' +
                '关键字:<input name="keyword" type="text" class="search-text" size="25" placeholder="输入关键字">' +
                '<input type="submit" name="search" class="btn" value="搜索">' +
                '' +
            '</div>'+
        '</div>' +
        '<div class="productData">' +
            "<table>"+
                "<caption><strong>商品列表</strong></caption>"+
                "<thead>"+
                    "<tr>" +
                        "<% _.each(table_th,function(value,key){ %>" +
                            "<th><%= value %><%if(value){%><i class='icon-box-remove'></i><%}%></th>" +
                        "<% }) %>" +
                        "<th>管理操作</th>"+
                    "</tr>"+
                "</thead>"+
                "<tbody class='dataLists'></tbody>"+
            "</table>"+
            '<div>' +
                '<input type="checkbox" name="checkall" class="J_checkall">全选/取消'+
                '<button type="button" class="isDeleteBtn">删除</button>'+
                '<b id="pages">5 条记录 1/1 页</b>'+
            '</div>'+
        '</div>' +
     '</div>'+
     '');
    /**
     * 这个模板是商品管理子功能中表数据的生成的ui
     * @type {*}
     */
    ADMIN.tpl['p_m_td']= _.template('' +
        '<% tableContent.each(function(value){ %>' +
            '<tr>' +
                '<td><input type="checkbox" name="isSelect" value="<%= value.id %>"></td>' +
                '<td><%= value.get("id") %></td>' +
                '<td><img src="<%= value.get("img") %>"/><%= value.get("intro") %></td>' +
                '<td><%= value.get("buy_num") %></td>' +
                '<td class="cateId" cate_id="<%= value.get("cate_id") %>"><%= cate[value.get("cate_id")] %></td>' +
                '<td><%= value.get("price") %></td>' +
                '<td class="isSell" status="<%= value.get("status") %>"><i class=<% if(value.get("status")){ %>"icon-unlocked"><%}else{%>icon-lock2<%}%></i></td>' +
                '<td><%= value.get("add_time") %></td>' +
                '<td width="120px"><button class="Productedite" id="<%= value.get("id") %>">编辑</button><button id="<%= value.get("id") %>">删除</button></td>' +
        "</tr>"+
        '<% }) %>' +
        '');
    /**
     * 这个模板是商品分类子功能表数据展示的ui
     * 不包括数据填充
     * @type {*}
     */
    ADMIN.tpl['productCategory'] = _.template(''+
     '<div class="pro_cate">' +
        '<p><button class="add_cate" pid="0" status="addCate">添加分类</button></p>'+
        "<table>"+
        "<caption><strong>商品分类列表</strong></caption>"+
        "<thead>"+
        "<tr>" +
        "<% _.each(table_th,function(value,key){ %>" +
        "<th><%= value %></th>" +
        "<% }) %>" +
        "</tr>"+
        "</thead>"+
        "<tbody class='cateLists'></tbody>"+
        "<tfoot>" +
            "<tr>" +
                "<td colspan='5'>" +
                    "<input type='checkbox' name='allSelect'/>" +
                    "<strong class='allSelectOrCancel'>全选/取消</strong>" +
                    "<button class='deleteSelect'>删除</button></td>" +
            "</tr>" +
        "</tfoot>"+
        "</table>"+
     '</div>'+
    "");
    /**
     * 这个模板是分类表中数据填充的ui
     * @type {*}
     */
    ADMIN.tpl['p_c_td']= _.template('' +
        '<% tableContent.each(function(value){if(value.get("pid")==pid){ %>' +
        '<tr pid="<%= value.get("pid") %>" id="<%= value.get("id") %>">' +
            '<td><input type="checkbox" name="selectOrCancelId" value="<%= value.get("id") %>"><%= value.get("id")%></td>' +
            '<td class="cateName" ><i class="icon-contract2"></i><span><%= value.get("name") %></span></td>' +
            '<td><img src="'+ADMIN.global.APPPATH+'Public/resource/uploads/<%= value.get("img") %>"/></td>' +
            '<td class="cate_type" status="<%= value.get("type") %>"><% if(value.get("type")){ %>商品类型<%}else{%>标记类型<%}%></td>' +
            '<td width="250px">' +
                '<button class="add_subCate" pid="<%= value.get("pid") %>" id="<%= value.get("id") %>" status="addSubCate">添加子类</button>' +
                '<button class="updateCate" id="<%= value.get("id") %>" pid="<%= value.get("pid") %>" status="update">编辑</button>' +
                '<button id="<%= value.get("id") %>" class="deleteCate">删除</button>' +
            '</td>' +
        "</tr>"+
        '<% }}) %>' +
        '');
    /**
     * 这个是分类select选项ui
     * @type {*}
     */
    ADMIN.tpl['selectCate'] = _.template('' +
        '<select name="topCate" class="cateSelect" data-pid="<%= pid %>" >' +
                '<option value="self" <%if(!id){%> selected <%}%>>本级分类</option>'+
            '<% options.each(function(value){ if(value.get("pid")==pid){ %>' +
                '<option value="<%= value.get("id") %>"  ' +
                    '<%if(value.get("id")==id){%> selected <%}%>  >' +
                    '<%= value.get("name") %> ' +
                '</option>'+
            '<% }}) %>' +
        '</select>' +
        '');
    /**
     * 这个是添加分类子功能的ui
     * @type {*}
     */
    ADMIN.tpl['add_categoryAction'] = _.template(''+
        '<div class="add_categoryAction">' +
            '<p><i>添加分类</i><input class="close" type="button" value="X" spid="0"/></p>' +
                '<table>' +
                    '<tr><td>分类名称</td><td><input type="text" class="cate_name"/></td><td>请输入分类名称</td></tr>'+
                    '<tr><td>上级栏目</td><td colspan="2" class="add_cate_select"></td></tr>'+
                    '<tr>' +
                        '<td>是否首页显示</td>' +
                        '<td colspan="2">' +
                            '<input name="visibility" type="radio" class="show" value="1" checked="checked">是' +
                            '<input name="visibility" type="radio" class="hide" value="0">否' +
                        '</td>' +
                    '</tr>'+
                    '<tr>'+
                        '<td>排序</td>'+
                        '<td>' +
                            '<input class="ordid" type="text" value="0">' +
                        '</td>' +
                        '<td>填写数字</td>'+
                    '</tr>'+
                '</table>'+
                '<div id="uploader">'+
                    "<p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>"+
                '</div>'+
                '<p>' +
                    '<input type="button" class="formSubmit" value="确定"/>' +
                    '<input class="cancel_submit" type="button" value="取消"/>' +
                '</p>' +
                '<p class="add_cate_prompt"></p>'+
        '</div>'+

    '');
    ADMIN.tpl['addProduct'] = _.template('');
    ADMIN.tpl['brandManage'] = _.template('');
}).call(this);