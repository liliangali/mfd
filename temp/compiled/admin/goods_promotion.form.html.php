<?php echo $this->fetch('header.html'); ?>
<style>
.ac_results {padding: 0px; border: 1px solid WindowFrame; background-color: Window; overflow: hidden; z-index: 19891020; /* 1000 */}
.ac_results ul {width: 100%; list-style-position: outside; list-style: none; padding: 0; margin: 0;}
.ac_results iframe {display: none;/*sorry for IE5*/ display/**/: block;/*sorry for IE5*/ position: absolute; top: 0; left: 0; z-index: -1; filter: mask(); width: 3000px; height: 3000px;}
.ac_results li {margin: 0px; padding: 2px 5px; cursor: pointer; display: block; font: menu; font-size: 12px; overflow: hidden;}
.ac_over {background-color: Highlight; color: HighlightText;}
#thumbnails li{
	    border: 1px solid #eee;
    cursor: pointer;
    display: inline;
    float: left;
    height: 100px;
    margin: 0 10px 10px 0;
    text-align: center;
    width: 100px;
}
.dives{border:1px solid #000; padding:10px 0; width:96%; margin:0px 2% 20px 2%;} 
.infs{width:96%; margin:0 2%; line-height: 40px;}
</style>
<script type="text/javascript">
$(function(){
    $('#brand_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        onkeyup    : false,
        rules : {
            name : {
                required : true,
            },
            price : {
                required : true,
            },
//             small_img:{
//                 required: true,
//             },
//             source_img:{
//                 required: true,
//             },
//             to_site:{
//                 required: true,
//             }
        },
        messages : {
            name : {
                required : '请填写样衣名称',
            },
            price : {
                required : "请填写样衣价格",
            },
        }
    });
});
</script>
<script type="text/javascript">
function selectAll(param)
{
    
    var obj = document.getElementById(param).getElementsByTagName('input');
    var obj1 = document.getElementById('h'+param);
    for (i = 0; i < obj.length; i++ )
    {
      obj[i].checked = obj1.checked;
    }
}

</script>
<?php echo $this->_var['build_editor']; ?>
<div id="rightTop">
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=discount&amp;act=index">管理</a></li>
        <?php if ($this->_var['data']['id']): ?>
        <li><a class="btn1" href="index.php?app=goods&amp;act=add">新增</a></li>
        <?php else: ?>
        <li><span>新增</span></li>
        <?php endif; ?>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="brand_form">
         <div class="infs"> 基本信息</div>
         <table class="dives">
      	
      		 <tr>
                <th class="paddingT15">
                     规则名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="name" type="text" name="name" value="<?php echo $this->_var['rule']['name']; ?>" />
                </td>
            </tr>
            
              <tr>
                <th class="paddingT15">
                      简介:</th>
                <td class="paddingT15 wordSpacing5">
                    <textarea name="introduce" id="introduce" rows="5" cols="24"><?php echo $this->_var['rule']['introduce']; ?></textarea>
                </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                    <label for="if_show">启用状态:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" name="is_open" value="1" <?php if ($this->_var['rule']['is_open'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">是</label>
                    <input id="no" type="radio" name="is_open" value="0" <?php if ($this->_var['rule']['is_open'] == 0): ?> checked="checked"<?php endif; ?> />
                    <label for="no">否</label>
                </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                        优先级:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="level" type="text" name="level" value="<?php echo $this->_var['rule']['level']; ?>" />
                </td>
            </tr>
            
            
           <tr>
                <th class="paddingT15">生效平台 :</th>
                <td class="paddingT15 wordSpacing5">
                
                <?php $_from = $this->_var['site']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
                    <input type="checkbox" name="site_name[]" value="<?php echo $this->_var['key']; ?>" 
                    
                    <?php $_from = $this->_var['sof']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'checkcate');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['checkcate']):
?>
                    <?php if ($this->_var['key'] == $this->_var['checkcate']): ?>
                      checked
                    <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    
                    />  <?php echo $this->_var['item']; ?>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                
                </td>
            </tr>
            
             <tr>
                <th class="paddingT15">
                    <label for="if_show">是否排它:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" name="if_ex" onclick="ex(1)"  value="1" <?php if ($this->_var['rule']['if_ex'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">是</label>
                    <input id="no" type="radio" name="if_ex"  onclick="ex(0)" value="0" <?php if ($this->_var['rule']['if_ex'] == 0): ?> checked="checked"<?php endif; ?> />
                    <label for="no">否</label>
                </td>
            </tr>
            
         <tr class="pt" <?php if ($this->_var['rule']['if_ex'] != 1): ?>  style="display:none;" <?php endif; ?>>
        <th class="paddingT15"></th>
            <td class="paddingT15 wordSpacing5">
              
                    <?php $_from = $this->_var['rules']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
                                                         规则名称 : <?php echo $this->_var['item']['name']; ?>&nbsp; &nbsp;&nbsp;优先级 : <?php echo $this->_var['item']['level']; ?> <br/>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </td>
           </tr>
        
            
            <tr>
            <th class="paddingT15">
            <label for="if_show">开始时间:</label></th>
                <td class="paddingT15 wordSpacing5">
                <input class="queryInput2 Wdate" type="text" value="<?php echo local_date("Y-m-d H:i:s",$this->_var['rule']['starttime']); ?>" style="width:150px" id="add_time_from" name="add_time_from" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/>
                </td>
            </tr>
            
             <tr>
            <th class="paddingT15">
            <label for="if_show">结束时间:</label></th>
                <td class="paddingT15 wordSpacing5">
                 <input class="queryInput2 Wdate" type="text" value="<?php echo local_date("Y-m-d H:i:s",$this->_var['rule']['endtime']); ?>" style="width:150px" id="add_time_to" name="add_time_to" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" /> 
                
                </td>
            </tr>

            <tr>
                <th class="paddingT15">会员等级 :</th>
                <td class="paddingT15 wordSpacing5">
                
                <?php $_from = $this->_var['lvs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
                    <input type="checkbox" name="member_lv[]" value="<?php echo $this->_var['item']['member_lv_id']; ?>" 
                    
                    <?php $_from = $this->_var['member_lv']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'checkcate');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['checkcate']):
?>
                    <?php if ($this->_var['item']['member_lv_id'] == $this->_var['checkcate']): ?>
                      checked
                    <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    
                    />  <?php echo $this->_var['item']['name']; ?>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                
                </td>
            </tr>
      		
          </table>
            
       <div class="infs"> 优惠条件 </div>
         <table class="dives">     
             <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" onclick="gres(1)"  name="favorable" value="1" <?php if ($this->_var['rule']['favorable'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">商品类型</label><br/>
                </td>
            </tr>
            
           <tr class="splx" <?php if ($this->_var['rule']['favorable'] != 1): ?> style="display:none;"<?php endif; ?>>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">
                
            <?php $_from = $this->_var['goods_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
               <input type="checkbox" id="lx" name="goods_type[]" value="<?php echo $this->_var['key']; ?>" 
               
               <?php $_from = $this->_var['cof']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'checkcate');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['checkcate']):
?>
               <?php if ($this->_var['key'] == $this->_var['checkcate']['favorable_value']): ?>
                 checked
               <?php endif; ?>
               <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
               /> <?php echo $this->_var['item']['name']; ?>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    
                </td>
            </tr>
            
               <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">
                 
                    <input id="no" type="radio" name="favorable" onclick="gres(2)" value="2" <?php if ($this->_var['rule']['favorable'] == 2): ?> checked="checked"<?php endif; ?> />
                    <label for="no">指定商品</label><br/>
                </td>
            </tr>
            
		      <tr class="glcp" <?php if ($this->_var['rule']['favorable'] != 2): ?> style="display:none;"<?php endif; ?>>
		        <th class="paddingT15">
		          <a href="javascript:;" class="recommend" style="color:red;">关联产品:</a></th>
		          <td class="paddingT15 wordSpacing5 relitem" id="recommend">
				  <input type="hidden" name="linkid" value="<?php echo $this->_var['linkid']; ?>" id="input_linkid">
		             <ul>
		               <?php $_from = $this->_var['suitArr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
		                  <li data-id='<?php echo $this->_var['link']['c_id']; ?>' class='item'> <a href='javascript:;'>x</a><?php echo $this->_var['link']['name']; ?><input type='text' value='<?php echo $this->_var['link']['lorder']; ?>' name='lorder[<?php echo $this->_var['link']['c_id']; ?>]' size='5'> </li>
		               <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		            </ul>
		            </td>
		       </tr>
               <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">
                 
                  <input id="no" type="radio" name="favorable" onclick="gres(3)" value="3" <?php if ($this->_var['rule']['favorable'] == 3): ?> checked="checked"<?php endif; ?> />
                  <label for="no">商品分类</label><br/>
                </td>
            </tr>
           <?php $_from = $this->_var['parents']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'sp1');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['sp1']):
?>
          <tr id="<?php echo $this->_var['key']; ?>" class="spfl" <?php if ($this->_var['rule']['favorable'] != 3): ?> style="display:none;"<?php endif; ?>  >
                <th class="paddingT15"><label for="if_show"></label></th>
                <td class="paddingT15 floatleft wordSpacing5">
                 <input type="checkbox" onclick="selectAll('<?php echo $this->_var['key']; ?>')" value="<?php echo $this->_var['sp1']['cate_id']; ?>"  name="goods_cate[]"  id="h<?php echo $this->_var['key']; ?>"
                 <?php $_from = $this->_var['cof']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'checkcate');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['checkcate']):
?>
                  <?php if ($this->_var['key'] == $this->_var['checkcate']['favorable_value']): ?>
                    checked
                  <?php endif; ?>
                   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                 
                 ><b><?php echo $this->_var['sp1']['cate_name']; ?></b>
                </td>
                 <?php $_from = $this->_var['sp1']['second']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key11', 'sp11');if (count($_from)):
    foreach ($_from AS $this->_var['key11'] => $this->_var['sp11']):
?>
                <td class="paddingT15 floatleft">
                 <input type="checkbox" name="goods_cate[]" value="<?php echo $this->_var['sp11']['cate_id']; ?>"
                 
                  <?php $_from = $this->_var['cof']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'checkcate');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['checkcate']):
?>
                  <?php if ($this->_var['key11'] == $this->_var['checkcate']['favorable_value']): ?>
                    checked
                  <?php endif; ?>
                   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                 ><?php echo $this->_var['sp11']['cate_name']; ?>
                </td>
                 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
           </tr>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            
          <?php $_from = $this->_var['diym']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'sp1');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['sp1']):
?>
          <tr id="<?php echo $this->_var['key']; ?>" class="spfl" <?php if ($this->_var['rule']['favorable'] != 3): ?> style="display:none;"<?php endif; ?>  >
                <th class="paddingT15"><label for="if_show"></label></th>
                <td class="paddingT15 floatleft wordSpacing5">
                 <input type="checkbox" onclick="selectAll('<?php echo $this->_var['key']; ?>')" value="<?php echo $this->_var['sp1']['cate_id']; ?>"  name="diym_cate[]"  id="h<?php echo $this->_var['key']; ?>"
                 <?php $_from = $this->_var['cof']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'checkcate');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['checkcate']):
?>
                  <?php if ($this->_var['key'] == $this->_var['checkcate']['favorable_value']): ?>
                    checked
                  <?php endif; ?>
                   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                 
                 ><b><?php echo $this->_var['sp1']['cate_name']; ?></b>
                </td>
                 <?php $_from = $this->_var['sp1']['second']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key11', 'sp11');if (count($_from)):
    foreach ($_from AS $this->_var['key11'] => $this->_var['sp11']):
?>
                <td class="paddingT15 floatleft">
                 <input type="checkbox" name="diym_cate[]" value="<?php echo $this->_var['sp11']['cate_id']; ?>"
                 
                  <?php $_from = $this->_var['cof']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'checkcate');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['checkcate']):
?>
                  <?php if ($this->_var['key11'] == $this->_var['checkcate']['favorable_value']): ?>
                    checked
                  <?php endif; ?>
                   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                 ><?php echo $this->_var['sp11']['cate_name']; ?>
                </td>
                 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
           </tr>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
           <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">

                    <input id="no" type="radio" name="favorable" onclick="gres(4)" value="4" <?php if ($this->_var['rule']['favorable'] == 4): ?> checked="checked"<?php endif; ?> />
                    <label for="no">所有商品</label><br/>
                </td>
            </tr>
     
    

          </table>  
            
       
            
           <div class="infs">优惠方案</div>
         <table  id="yhcase"  class="dives">

			<tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">
                    <input id="yes" type="radio" name="yhcase" value="1" onclick="res(1)"  <?php if ($this->_var['rule']['yhcase'] == 1): ?> checked="checked"<?php endif; ?> />
                    <label for="yes">符合条件的商品以固定折扣出售</label><br/>
                </td>
            </tr>
            
               <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">
                 
                    <input id="no" type="radio" name="yhcase" value="2" onclick="res(2)" <?php if ($this->_var['rule']['yhcase'] == 2): ?> checked="checked"<?php endif; ?> />
                    <label for="no">符合条件的商品以固定价格出售</label><br/>
               
                </td>
            </tr>
               <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">
                 
                    <input id="no" type="radio" name="yhcase" value="3" onclick="res(3)" <?php if ($this->_var['rule']['yhcase'] == 3): ?> checked="checked"<?php endif; ?> />
                    <label for="no">符合条件的商品减去固定折扣出售</label><br/>
                  
                </td>
            </tr>
               <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">

                    <input id="no" type="radio" name="yhcase" value="4" onclick="res(4)" <?php if ($this->_var['rule']['yhcase'] == 4): ?> checked="checked"<?php endif; ?> />
                    <label for="no">符合条件的商品减去固定价格出售</label><br/>
                </td>
            </tr>
            
            <tr>
                <th class="paddingT15">
                    <label for="if_show"></label></th>
                <td class="paddingT15 wordSpacing5">

                    <input id="no" type="radio" name="yhcase" value="5" onclick="res(5)" <?php if ($this->_var['rule']['yhcase'] == 5): ?> checked="checked"<?php endif; ?> />
                    <label for="no">符合条件的商品免邮</label><br/>
                </td>
            </tr>

            <tr class="vcon">

           
                <?php if ($this->_var['rule']['yhcase'] == 1): ?>
                <th class="paddingT15"></th>
               <td>商品价格乘以<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="<?php echo $this->_var['rule']['yhcase_value']; ?>" />%折扣出售
               </td>
               <?php elseif ($this->_var['rule']['yhcase'] == 2): ?>
                <th class="paddingT15"></th>
               <td>商品价格以<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="<?php echo $this->_var['rule']['yhcase_value']; ?>" />出售
               </td>
               <?php elseif ($this->_var['rule']['yhcase'] == 3): ?>
                <th class="paddingT15"></th>
               <td>商品价格减去<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="<?php echo $this->_var['rule']['yhcase_value']; ?>" />%折扣出售
               </td>
               <?php elseif ($this->_var['rule']['yhcase'] == 4): ?>

                <th class="paddingT15"></th>
               <td>商品价格优惠<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="<?php echo $this->_var['rule']['yhcase_value']; ?>" />出售
               </td>
               <?php endif; ?>
          
            </tr>
         </table>  
   
            <table style="padding:10px 0; width:96%; margin:0px 2% 20px 2%;">
      
            
        <tr>
            <th></th>
            <td class="ptb20">
                <input type="hidden" name="id" value="<?php echo $this->_var['rule']['id']; ?>">
                <input class="tijia" type="submit" name="Submit" value="提交" />
                <input class="congzi" type="reset" name="Submit2" value="重置" />
            </td>
        </tr>
        </table>
    </form>
</div>

<script>
function res(cs){
	var yhcase = $('#yhcase');
    var _html;
    var  v = $('.vcon');
    if(cs==1){
        _html =
               
                '<th class="paddingT15"></th>'+
                '<td>'+'商品价格乘以'+'<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="" />'+'%折扣出售'+'</td>';
        v.html(_html);

    }
    if(cs==2){
            _html =
                '<th class="paddingT15"></th>'+
                '<td>'+'商品价格以'+'<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="" />'+'出售'+'</td>';
         v.html(_html);

    }
    if(cs==3){
            _html =
                '<th class="paddingT15"></th>'+
                '<td>'+'商品价格减去'+'<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="" />'+'%折扣出售'+'</td>';
          v.html(_html);

    }
    if(cs==4){
            _html =
                '<th class="paddingT15"></th>'+
                '<td>'+'商品价格优惠'+'<input class="infoTableInput" id="name" style="width:80px;"  type="text" name="casevalue" value="" />'+'出售'+'</td>';
          v.html(_html);

    }
    if(cs==5){
          _html='';
         v.html(_html);
    }


}


function gres(cs){
	var yhcase = $('#yhcase');
   
    if(cs==1){
    	$(".splx").show();
    	 $(".spfl").hide();
    	 $(".glcp").hide();
    }
    if(cs==2){
    	$(".glcp").show();
    	$(".splx").hide();
    	$(".spfl").hide();
    }
    if(cs==3){
    	$(".splx").hide();
    	 $(".glcp").hide();
        $(".spfl").show();
    }
    if(cs==4){
    	$(".splx").hide();
        $(".spfl").hide();
        $(".glcp").hide();

    }

}

function ex(cs){
	if(cs==1){
		$('.pt').show();
	}
	if(cs==0){
		$('.pt').hide();
	}
	
}


</script>
<script type="text/javascript">
_did = "<?php echo $this->_var['data']['id']; ?>";
$(function(){

    <?php if ($this->_var['linkid']): ?>
    ajax_cst('<?php echo $this->_var['linkid']; ?>');
    <?php endif; ?>
    
});
function cst_callback(_ids){
    ajax_cst(_ids.ids);
}
function ajax_cst(ids){
    $.ajax({
        url    :"index.php?app=jpjz_dissertation&act=ajax_customs_info",
        data   :"ids="+ids+"&did="+_did,
        success:function(res){
            $(".panel").html(res);
        }
    });
}
$(document).ready(function(){
	$(".recommend").click(function(){
	    var ids=$("#input_linkid").val();
	   
	    $.cookie("ids",null);
		var url = "index.php?app=goodslink";
		if(ids){
		    url += "&ids="+ids;
	     }
	    window.open(url,'请选择商品','height=800,width=800,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no')
	})

	$(".relitem").find("a").each(function(){
	$(this).click(function(){
		var temp =new Array();
		var linkids = $("#input_linkid").val();
		var id = $(this).parent("li").attr('data-id');
		
		  $(this).parent("li").remove();
		  
		  $("#input_linkid").val(removeHiddenIdsText(id,linkids));
	})
})
})

function customCallBack(ids){
    $.get('index.php?app=discount&act=loadgoods&ids='+ids+"&did="+_did,function(result){
        var result = eval("("+result+")");
        if(result.done == true){
            var html='<ul><input type="hidden" name="linkid" value="'+ids+'" id="input_linkid">';
            for(var i=0;i<result.retval.length;i++){
                html += "<li data-id='"+result.retval[i].id+"' class='item'> <a href='javascript:;'>x</a>"+result.retval[i].name+"<input type='text' value='"+result.retval[i].lorder+"' name='lorder["+result.retval[i].id+"]' size='5'> </li>";
            }
            html += "</ul>";
            $("#recommend").html(html);

				$(".relitem").find("a").each(function(){
					$(this).click(function(){
					var temp =new Array();
					var linkids = $("#input_linkid").val();
					var id = $(this).parent("li").attr('data-id');
					$(this).parent("li").remove();
				   $("#input_linkid").val(removeHiddenIdsText(id,linkids));
			   })
		})
        }else{ 
            alert(result.msg);
            return false;    
        }
    });
}

function removeHiddenIdsText(value, container) {
    if (value.length == 0)
        return '';
            
    //去除前后逗号    
    value = value.replace(/^,/, '').replace(/,$/, '');
    container = container.replace(/^,/, '').replace(/,$/, '');
            
    if (container == value)
    {
        return '';
    }
            
    var sArray = container.split(',');
    for (var i = sArray.length - 1; i >= 0; --i)
    {
        if (sArray[i] == value)
            sArray[i] = undefined;
    }
            
    var result = sArray.join(',');
    //因为undefined会连接成,,所以要将,,换成,            
    result = result.replace(/,,/,',');
    result = result.replace(/^,/, '').replace(/,$/, '');
    return result;
}
</script>
<?php echo $this->fetch('footer.html'); ?>
