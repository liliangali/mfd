var selfurl = window.location.href;
function change_background()
{
    $("tbody tr").hover(
    function()
    {
        $(this).css({background:"#f7f7f7"});
    },
    function()
    {
        $(this).css({background:"#fff"});
    });
}

$(function()
{
    change_background();
    var selfurl = window.location.href;
    //给图标的加减号添加展开收缩行为
    $('img[ectype="flex"]').click(function(){
        var status = $(this).attr("status");
        var id = $(this).attr("fieldid");
        //状态是加号的事件
        if(status == 'open')
        {
            var src = $(this).attr('src');
            var pr = $(this).parent('td').parent('tr');
            //如果已经请求过的数据再次请求时只显示改变状态，不再去请求
            /*if($("."+id).length > 0)
            {
                $("."+id).show();
                $(this).attr('src',src.replace("tv-expandable","tv-collapsable"));
                $(this).attr('status','close');
                return;
            }*/
            $.get(selfurl + "&act=ajax_cate", {id: id}, function(data){
                if(data)
                {
                    var str = "";
                    var res = eval('('+data+')');
                    for(var i = 0; i < res.length; i++)
                    {
                        var src = "";
                        var if_show = "";
                        var is_def = "";
                        //给每一个异步取出的数据添加伸缩图标后者无状态图标
                        if(res[i].switchs)
                        {
                           src =  "<img src='templates/style/images/treetable/tv-expandable.gif' ectype='flex' status='open' fieldid="+res[i].cate_id+
                                  " onclick='secajax($(this))'>";
                        }
                        else
                        {
                           src =  "<img src='templates/style/images/treetable/tv-item.gif' fieldid='"+res[i].cate_id+"'>";
                        }
                        //给每一个取出的数据添加是否显示标志
                        if(res[i].if_show == '1')
                        {
                            if_show = "<img src='templates/style/images/positive_enabled.gif' ectype='inline_edit' fieldname='if_show' fieldid='"+res[i].cate_id+"' fieldvalue='1'/>";
                        }
                        else
                        {
                            if_show = "<img src='templates/style/images/positive_disabled.gif' ectype='inline_edit' fieldname='if_show' fieldid='"+res[i].cate_id+"' fieldvalue='0'/>";
                        }
                        if(res[i].is_def == '1')
                        {
                            is_def = "<img src='templates/style/images/positive_enabled.gif' ectype='inline_edit' fieldname='is_def' fieldid='"+res[i].cate_id+"' fieldvalue='1'/>";
                        }
                        else
                        {
                            is_def = "<img src='templates/style/images/positive_disabled.gif' ectype='inline_edit' fieldname='is_def' fieldid='"+res[i].cate_id+"' fieldvalue='0'/>";
                        }
                        //构造每一个tr组成的字符串，标语添加
                        str+="<tr class='row"+id+"'><td class='align_center w30'></td>"+
                        "<td class='node' width='50%'><img class='preimg' src='templates/style/images/treetable/vertline.gif'/>"+src+"<span class='node_name editable' ectype='inline_edit' fieldname='cate_name' fieldid='"+res[i].cate_id+"' required='1'>"+res[i].cate_name+"</span></td>"+
                        "<td class='align_center'>"+res[i].uprice+"|"+res[i].fprice+"</td>"+
                        "<td class='align_center'>"+res[i].ve+"</td>"+
                        "<td class='align_center'><img src="+res[i].small_img+" height='50' width='50'></td>"+
                        "<td class='align_center'>"+is_def+"</td>"+
                        "<td class='align_center'>"+res[i].sort_order+"</td>"+
			            "<td class='align_center'>"+if_show+"</td>"+
			            "<td class='handler'><span><a href='index.php?app=fabric_brand&amp;act=edit&amp;id="+res[i].cate_id+"'>"+lang.edit+"</a></span></td></tr>";
              /*"  | <a href='javascript:if(confirm(\""+lang.confirm_delete+"\"))window.location=\"index.php?app=fabric_brand&act=drop&id="+res[i].cate_id+"\";'>"+lang.drop+"</a> ";
            | <a href='index.php?app=fabric_brand&amp;act=add&amp;pid="+res[i].cate_id+"'>"+lang.add_child+"</a></span></td></tr>";*/
                    }
                    //将组成的字符串添加到点击对象后面
                    pr.after(str);
                    change_background();
                    //解除行间编辑的绑定事件
                    $('span[ectype="inline_edit"]').unbind('click');
                    //重现初始化页面
                    $.getScript(SITE_URL+"/includes/libraries/javascript/inline_edit.js");
                }
            });
            $(this).attr('src',src.replace("tv-expandable","tv-collapsable"));
            $(this).attr('status','close');
        }
        //状态是减号的事件
        if(status == "close")
        {
            var src = $(this).attr('src');
            $('.row'+id).hide();
            $(this).attr('src',src.replace("tv-collapsable","tv-expandable"));
            $(this).attr('status','open');
        }
    });
});
//异步请求回来的数据的再次添加异步伸缩行为
function secajax(ob)
{
    var status = $(ob).attr("status");
    var id = $(ob).attr("fieldid");
    var src = $(ob).attr('src');
        if(status == 'open')
        {
            var src = $(ob).attr('src');
            var pr  = $(ob).parent('td').parent('tr');
            var pid = pr.attr('class');
            var sr  = pr.clone();
            var td2 = sr.find("td:eq(1)");
            td2.prepend("<img class='preimg' src='templates/style/images/treetable/vertline.gif'/>")
                            .children('span')
                            .remove().end()
                            .find("img[ectype=flex]").remove();
            var td2html = td2.html();

            $.get(selfurl + "&act=ajax_cate", {id: id}, function(data){
                if(data)
                {
                    var str = '';
                    var res = eval('('+data+')');
                    for(var i = 0; i < res.length; i++)
                    {
                        var src = "";
                        var if_show = "";
                        var is_def = "";
                        var add_child = '';
                        if(res[i].switchs)
                        {
                           src =  "<img src='templates/style/images/treetable/tv-expandable.gif' ectype='flex' status='open' fieldid="+res[i].cate_id+
                           " onclick='secajax($(this))'><span class='node_name editable' ectype='inline_edit' fieldname='cate_name' fieldid='"+res[i].cate_id+"' required='1'>"+res[i].cate_name+"</span>";

                        }
                        else
                        {
                           src =  "<img src='templates/style/images/treetable/tv-item.gif' fieldid='"+res[i].cate_id+"'><span class='node_name editable' ectype='inline_edit' fieldname='cate_name' fieldid='"+res[i].cate_id+"' required='1'>"+res[i].cate_name+"</span>";
                        }
//                        面料品牌不能在增加下级
                       /* if(res[i].add_child)
                        {
                            add_child =  " | <a href='index.php?app=fabric_brand&amp;act=add&amp;pid="+res[i].cate_id+"'>"+lang.add_child+"</a>";
                        }*/
                        var itd2 = td2html+src;
                        if(res[i].if_show == '1')
                        {
                            if_show = "<img src='templates/style/images/positive_enabled.gif' ectype='inline_edit' fieldname='if_show' fieldid='"+res[i].cate_id+"' fieldvalue='1'/>";
                        }
                        else
                        {
                            if_show = "<img src='templates/style/images/positive_disabled.gif' ectype='inline_edit' fieldname='if_show' fieldid='"+res[i].cate_id+"' fieldvalue='0'/>";
                        }
                        if(res[i].is_def == '1')
                        {
                            is_def = "<img src='templates/style/images/positive_enabled.gif' ectype='inline_edit' fieldname='is_def' fieldid='"+res[i].cate_id+"' fieldvalue='1'/>";
                        }
                        else
                        {
                            is_def = "<img src='templates/style/images/positive_disabled.gif' ectype='inline_edit' fieldname='is_def' fieldid='"+res[i].cate_id+"' fieldvalue='0'/>";
                        }
                        str+="<tr class='"+pid+" row"+id+"'><td class='align_center w30'></td>"+
                        "<td class='node' width='50%'>"+itd2+"</td>"+
                        "<td class='node' width='50%'>"+res[i].uprice+"</td>"+
                        "<td class='node' width='50%'>"+res[i].ve+"</td>"+
                        "<td class='align_center'><img src="+res[i].small_img+" height='50' width='50'></td>"+
                        "<td class='align_center'>"+is_def+"</td>"+
			            "<td class='align_center'>"+res[i].sort_order+"</td>"+
			            "<td class='align_center'>"+if_show+"</td>"+
			            "<td class='handler'><span><a href='index.php?app=fabric_brand&amp;act=edit&amp;id="+res[i].cate_id+"'>"+lang.edit+"</a> </span></td></tr>";
//                        面料品牌不能删除和增加下级
//            		" | <a href='javascript:if(confirm(\""+lang.confirm_delete+"\"))window.location =\"index.php?app=fabric_brand&amp;act=drop&amp;id="+res[i].cate_id+"\";'>"+lang.drop+"</a>" + add_child + "</span></td></tr>";
                    }
                    pr.after(str);
                    change_background();
                    $('span[ectype="inline_edit"]').unbind('click');
                    $.getScript(SITE_URL+"/includes/libraries/javascript/inline_edit.js");
                }
            });
            $(ob).attr('src',src.replace("tv-expandable","tv-collapsable"));
            $(ob).attr('status','close');
        }
        if(status == "close")
        {
            $('.row' + id).hide();
            $(ob).attr('src',src.replace("tv-collapsable","tv-expandable"));
            $(ob).attr('status','open');
        }
}