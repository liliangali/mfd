<?php echo $this->fetch('header.html'); ?>
<div id="rightTop">

    <ul class="subnav">
        <li><span>设置</span></li>

        <li><a class="btn1" href="index.php?app=setting&amp;act=debit_log">发放记录</a></li>
        </ul>
</div> 
<style>
.infoTable p{padding:10px 0;}
</style>
<div class="info">

<h2 style="padding: 20px 0px 0px 20px;">备注：注册券一张 ，订单满额券一张 </h2>
    <form method="post" enctype="multipart/form-data">
    <div style="border: 2px solid #FFBCBC;margin: 20px;width: 1000px;padding-bottom:20px;">
        <table class="infoTable">
   
           新用户注册送券设置
            <th class="paddingT15"></th>
            <td class="paddingT15 wordSpacing5">
            <p>
            
            	*券名称:   <input class="infoTableInput" id="debit" type="text" name="debit_name" value="<?php echo $this->_var['setting']['debit_name']; ?>"/>
            </p>
             <p>
              *券品类:
              <select id="time_zone" name="debit_type">
                    <?php echo $this->html_options(array('options'=>$this->_var['type'],'selected'=>$this->_var['setting']['debit_type'])); ?>
              </select>
             
            </p>
            <p>
            *券额度:	<input class="infoTableInput" id="debit" type="text" name="debit_num" value="<?php echo $this->_var['setting']['debit_num']; ?>"/>
            </p>
           
     
		      <p>
		       *券使用期:   
		      <select id="time_zone" name="debit_cate" onchange="changeCate(this)">
		            <?php echo $this->html_options(array('options'=>$this->_var['cate'],'selected'=>$this->_var['setting']['debit_cate'])); ?>
		      </select>
		      <input data_name="debit_time" <?php if ($this->_var['setting']['debit_cate'] == 1): ?> class="infoTableInput" name="debit_time1" onClick=""  <?php else: ?> name="debit_time2"  class="infoTableInput Wdate" onClick="WdatePicker()" <?php endif; ?>  type="text"  value="<?php echo $this->_var['setting']['debit_time']; ?>"  />
		                     天或截至日期
		       </p>
		       
		   
           
         </td>
                
        </tr>
       <tr>
		<td class="paddingT15"></td>&nbsp;
		<td class="paddingT15 wordSpacing5">
		*是否启用:
		   <input type="radio"  name="open" value="1" <?php if ($this->_var['setting']['open'] == 1): ?> checked="checked"<?php endif; ?> />
		   <label>是</label>
		   <input type="radio"  name="open"  value="0" <?php if ($this->_var['setting']['open'] == 0): ?> checked="checked"<?php endif; ?> />
		   <label>否</label>&nbsp;&nbsp;&nbsp;&nbsp;
		   <span class="grey"></span>
		</td>
     </tr>
            
            
        </table>
       

        </div>
        
        
    <div style="border: 2px solid #A7D6A9;margin: 20px;width: 1000px;padding-bottom:20px;">
         <table class="infoTable">
        订单满额送券设置
           <tr>
                <th class="paddingT15"></th>
                <td class="paddingT15 wordSpacing5">
                <p>
                *券名称:	<input class="infoTableInput" id="debit" type="text" name="debit_name_o" value="<?php echo $this->_var['setting']['debit_name_o']; ?>"/>
                </p>
                  <p>
                   *券品类:
                  <select id="time_zone" name="debit_type_o">
                        <?php echo $this->html_options(array('options'=>$this->_var['type'],'selected'=>$this->_var['setting']['debit_type_o'])); ?>
                  </select>
                 
                </p>
                
                 <p>
                 *订单额度:
                	<input class="infoTableInput" id="debit" type="text" name="debit_order_o" value="<?php echo $this->_var['setting']['debit_order_o']; ?>"/>
                </p>
                <p>
                *券额度:
                	<input class="infoTableInput" id="debit" type="text" name="debit_num_o" value="<?php echo $this->_var['setting']['debit_num_o']; ?>"/>
                </p>
              
               <p>
		       *券使用期:     
          <select id="time_zone" name="debit_cate_o" onchange="changeCate(this)">
                <?php echo $this->html_options(array('options'=>$this->_var['cate'],'selected'=>$this->_var['setting']['debit_cate_o'])); ?>
          </select>
        
           <input data_name="debit_time_o" <?php if ($this->_var['setting']['debit_cate_o'] == 1): ?> class="infoTableInput" name="debit_time_o1" onClick=""  <?php else: ?> name="debit_time_o2"  class="infoTableInput Wdate" onClick="WdatePicker()" <?php endif; ?>  type="text"  value="<?php echo $this->_var['setting']['debit_time_o']; ?>"  />
                      天或截至日期
           </p>
                    
             </td>
                    
         </tr>
      <tr>
		<td class="paddingT15"></td>&nbsp;
		<td class="paddingT15 wordSpacing5">
		*是否启用:
		   <input type="radio"  name="debit_open_o"  value="1" <?php if ($this->_var['setting']['debit_open_o'] == 1): ?> checked="checked"<?php endif; ?> />
		   <label>是</label>
		   <input type="radio"  name="debit_open_o"  value="0" <?php if ($this->_var['setting']['debit_open_o'] == 0): ?> checked="checked"<?php endif; ?> />
		   <label>否</label>&nbsp;&nbsp;&nbsp;&nbsp;
		   <span class="grey"></span>
		</td>
     </tr>
            
        </table>
	</div> 

<input class="tijia" type="submit" id="Submit" value="提交" style="margin:20px 0 20px 200px;" />
    </form>
</div>
<script>

var wait=60;
function time(o) {
        if (wait == 0) {
            o.removeAttribute("disabled");            
            o.value="免费获取验证码";
            wait = 60;
        } else { // www.jbxue.com
            o.setAttribute("disabled", true);
            o.value="重新发送(" + wait + ")";
            wait--;
            setTimeout(function() {
                time(o)
            },
            1000)
        }
    }
document.getElementById("btn").onclick=function()
{
	var _this = this;
	$.post("index.php?app=<?php echo $this->_var['app']; ?>&act=sendSafeCode",{router:"<?php echo $this->_var['app']; ?>-<?php echo $this->_var['act']; ?>"},function(res){
		if(!res.done)
	    {
			alert(res.msg);
	    }
		else
		{
			time(_this);
		}	
	},'json')
}

	function changeCate(obj)
	{
		var debit_time = "<?php echo $this->_var['setting']['debit_time']; ?>";
		var cate = $(obj).val();
		var data_name = $(obj).next().attr('data_name');
		if(cate == 1)
		{
			$(obj).next().attr('name',data_name+'1');
			$(obj).next().attr('onClick','');
			$(obj).next().attr('class','infoTableInput');
			$(obj).next().val('');
		}
		else
		{
			$(obj).next().attr('name',data_name+'2');
			$(obj).next().attr('onClick','WdatePicker()');
			$(obj).next().attr('class','infoTableInput Wdate');
			$(obj).next().val('');
		}
		
		
	}
</script>




<?php echo $this->fetch('footer.html'); ?>
