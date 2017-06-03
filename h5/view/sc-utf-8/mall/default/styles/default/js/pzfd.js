



$(function(){

   lift.c($('.floor'));//生成电梯

    //domReady电梯 开始
   //滚动时电梯状态
   $(window).scroll(function(){
        var c_s = $(this).scrollTop();
        var p_len = lift.f_pos.length;
        var is = $('#l_wrap li .f-index');
        if((lift.f_pos[0]-150)<c_s ){
             if($('#float_tool').length>0){
           
                 
             }
             for(var i=0;i<= p_len-1;i++){
                 if((lift.f_pos[i]-150)<c_s && c_s<(lift.f_pos[i+1]-150)){
                     is.each(function(i){$(this).removeClass('f'+(i+1)+'-hover')});
                     is.eq(i).addClass('f'+(i+1)+'-hover');
                     break;
                 }else{
                     continue;
                 }
             }
        }
   })

   //楼层点击滚动定位
   $('.smooth').each(function(i){
           var $this =$(this);
           $this.click(function(){
               $('#l_wrap li').each(function(){$(this).removeClass('f'+(i+1)+'-hover')});
               $('html,body').animate({scrollTop:lift.f_pos[i]},300);
            return false;
          });
   });
   
  //滚动电梯IE6兼容
   $('.f-index').each(function(i){
     if( $.browser.msie && $.browser.version < 7.0){
         $(this).hover(function(){$(this).addClass("f"+(i+1)+"-hover");},function(){$(this).removeClass("f"+(i+1)+"-hover");})
      }
   })
//domReady电梯 结束

});

//浮动电梯工具栏
var lift ={
       f_pos:[],
         //生成
          c:function(f){
           var f_num = f.length;
           var f_name =['女装','男装','童装','内衣','箱包/衣饰','鞋类/运动'];
           var float_tool = $('<div id="float_tool" class="float-tool"><ul id="l_wrap" class="l-wrap f14 bold"></ul><div class="float-tool-bg"></div></div>');
           var l_wrap = float_tool.find(".l-wrap");
           var li_str='',c_li,c_f,c_f_pos,c_a,c_id,c_index,c_name,b_pos,last_li_c='';
           for(var i=0;i<= f_num-1;i++){
              c_f = f.eq(i);
              c_f_pos = c_f.offset().top;
              lift.f_pos.push(c_f_pos);
              c_index = i+1;
              if(c_f.attr('id') != undefined){
                  c_id = c_f.attr('id');
              }else{
                  c_id = 'f'+c_index;
                  c_f.attr('id',c_id);
              }
                c_name = f_name[i];
                if(i==f_num-1){
                    last_li_c = ' last-f';
                    b_pos = c_f_pos + c_f.height();//楼层范围是从1楼的首至最后楼的尾
                    lift.f_pos.push(b_pos);
                }
                c_li =$('<li class="f'+(i+1)+last_li_c+'"></li>');
                c_a = $('<a class="smooth a'+(i+1)+' href="#'+c_id+'" target="_self"><span class="f-index f'+(i+1)+'">'+c_index+'F</span><span class="f-name">'+c_name+'</span></a>');
                c_a.appendTo(c_li);
                c_li.mouseenter(function(){$(this).find('.f-name').show(40);}).mouseleave(function(){$(this).find('.f-name').hide(40);})
                c_li.appendTo(l_wrap);
            }
            float_tool.appendTo($('body'));
          }
   }


