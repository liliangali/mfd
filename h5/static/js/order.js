$.fn.extend({
    order: function(options) {
        return this.each(function() {
            new $.order(options);
        });
    }
});
$.order = function(opt){
    opt.regions;
    _actionA();
    $('#submitOrder').click(function(){
        var _addr = $('input:radio:checked').val();
        var _taxType = $('#invoice select').val();
        var _taxCon = $('#invoice textarea').val();
        var _types   = $('#orderType').val();
        $.ajax({
            //url : '/index.php/order-create.html',
            url : opt.submitUrl,
            type:'post',
            data:'&addr='+_addr+'&taxid='+_taxType+'&taxcon='+_taxCon+'&types='+_types,
            success:function(res){
                var res = $.parseJSON(res);
                if(res.done == false){
                    //$(".submitNotice").html('<p class="ddtscw">'+res.msg+'</p>').show();
                    return false;
                }else{
                    location.href=opt.paycenterUrl+"?"+res.retval.order_sn;
                }
            }
        });
    });
    function _invoice(){
        $.ajax({
            url  : opt.invoiceUrl,
            success : function(res){
                var res = $.parseJSON(res);
                if(res != null && res.done == true){
                    $('.invoice_tax_box').html(res.retval);
                    $('#invoice').slideDown();
                    $('.invoiceEdit').hide();
                    //$('#tax_info').hide();
                    _actionA();
                }
            }
        })
    }
    function _pInvoice(){
        var _tp = $('#tax_type option:selected').val();
        var _tt = $('#tax_tit').val();
        $.post(opt.invoiceUrl,{tp:_tp,tt:_tt},function(res){
            var res = $.parseJSON(res);
            if(res != null && res.done == true){
                $('#tax_info').children('.tax_in').html(res.retval.inv);
                $('#tax_info').children('.tax_co').html(res.retval.ti);
                $('#invoice').fadeOut();
            }else{
                $('#invoice').fadeOut();
            }
            $('.invoiceEdit').show();
        });
    }
    
    function _actionA(){
        $('#invoiceLink').unbind().bind('click',_invoice);
        $('.save_invoice').unbind().bind('click',_pInvoice);
        $('.cancel_invoice').click(function(){
            if($('#invoice').is(':visible')){
                $('#invoice').fadeOut();
                $('.invoiceEdit').show();
            }
        });
        
        $(".addressList .address-div").each(function(){
            
            $(this).mouseover(function(){
                $(this).find(".lic").css('display','block');
                $(this).addClass("address-action");
            })
    
            $(this).mouseout(function(){
                var c = $(this).find("input").attr("checked");
                if(c != "checked"){
                    $(this).find(".lic").hide();
                    $(this).removeClass("address-action");
                }
            })
            var pu = $(".addressList");
            
             /* radio */
            $(this).find(":radio").click(function(){
                var pi = $(this).parents(".address-div");
                pu.find(".lic").hide();
                pi.find(".lic").show();
                pu.find(".address-div").removeClass("address-action");
                pi.addClass("address-action");
            });
            /*文字*/
            $(this).find("span").click(function(){
                var pi = $(this).parents(".address-div");
                pu.find(".lic").hide();
                pi.find(".lic").show();
                pu.find(".address-div").removeClass("address-action");
                pi.addClass("address-action");
                pi.find("input").attr('checked', 'true');
                var v = pi.find("input").val();
            })
            /* edit */
            $(this).find('a:first').click(function(){
                var _this = $(this);
                var _id   = _this.data("id");
                var pi    = _this.parents(".address-div");
                pu.find(".lic").hide();
                pi.find(".lic").show();
                pu.find(".address-div").removeClass("address-action");
                pi.addClass("address-action");
                pi.find("input").attr('checked', 'true');
    
               $("#address_add #address_add_form").find("input").each(function(){
                   var name = $(this).attr("name");
                   switch(name){
                       case "addr_id":
                           $(this).val(_id);
                       break;
                       case "consignee":
                           $(this).val($("#addr-consignee-"+_id).val());
                       break;
                       case "phone_tel":
                           $(this).val($("#addr-phone_tel-"+_id).val());
                       break;
                       case "phone_mob":
                           $(this).val($("#addr-phone_mob-"+_id).val());
                       break;
                       case "address":
                           $(this).val($("#addr-address-"+_id).val());
                       break;
                       case "region_id":
                           $(this).val($("#addr-region_id-"+_id).val());
                       break;
                       case "region_name":
                           $(this).val($("#addr-region_name-"+_id).val());
                       break;
                       case "email":
                           $(this).val($("#addr-email-"+_id).val());
                       break;
                       case "zipcode":
                           $(this).val($("#addr-zipcode-"+_id).val());
                       break;
                       case "al_name":
                           $(this).val($("#addr-al_name-"+_id).val());
                       break;
                       
                   }
               });
               var o = $("#addr-region_id-"+_id).val().split(",");
               opt.regions = o;
    
               $("#address_add #address_add_form #region select:first").find('option').each(function(){
                   if($(this).val() == o[0]){
                       $(this).attr("selected", "true");
                   }
               })
               
               $("#address_add #address_add_form #region select:first").change();
               $('#address_add').slideDown();
    
            });
        });
    }

    /* bind */
    $("#address-submit").bind("click", saveAddress);
    $("#region > select").change(regionChange);
    $('#addressLink').click(function(){
        cleanForm();
        if($('#address_add').is(':visible')){
           $('#address_add').fadeOut();
        }else{
           $('#address_add').slideDown();
        }
    });
    $("#address-cancel").click(function(){
        $('#address_add').fadeOut();
        cleanForm();
    });
    /* functions */
    function saveAddress(){
        $('#address_add_form').ajaxSubmit({
            type:"post",
            url : opt.addressUrl,
            success:function(res){
                var res = $.parseJSON(res);
                if(res.done == true){
                    $(".addressList").find(".address-div").removeClass("address-action");
                    $(".addrLists").html(res.retval);
                    $('#address_add').fadeOut();
                    cleanForm();
                    _actionA();
                }
            }
        });
    };
    function cleanForm(){
        $(':input','#address_add_form')  
        .not(':button, :submit, :reset')  
        .val('')  
        .removeAttr('checked')  
        .removeAttr('selected');
    }
    function regionChange()
    {
        // 删除后面的select
        $(this).nextAll("select").empty().append('<option>请选择..</option>');

        // 计算当前选中到id和拼起来的name
        var selects = $(this).siblings("select").andSelf();
        var ids =new Array();
        var names = new Array();
        for (i = 0; i < selects.length; i++)
        {
            sel = selects[i];
            if (sel.value > 0)
            {
                ids.push(sel.value);
                name = sel.options[sel.selectedIndex].text;
                names.push(name);
            }
        }

        $(".mls_id").val('');
        $(".mls_names").val('');
        // ajax请求下级地区
        if (this.value > 0)
        {
            var _self = this;
            //var regionUrl = "/index.php/mlselection.html";
            $.getJSON(opt.regionUrl, {'pid':this.value,'type':'region'}, function(data){
                if (data.done){
                    if (data.retval.length > 0){
                        ns = $(_self).next("select");
                        if(ns.is("select")  == false){
                             $("<select><option>" + lang.select_pls + "</option></select>").insertAfter(_self);
                        }
                       
                        $(_self).next("select").unbind().bind("change",regionChange)
                        var data  = data.retval;
                        for (i = 0; i < data.length; i++)
                        {
                            var s = "";
                            try{
                                if(opt.regions){
                                    for(r=0;r<opt.regions.length;r++){
                                        if(data[i].region_id == opt.regions[r]){
                                            s = " selected";
                                        }
                                    }
                                }
                            }catch(e){}
                            $(_self).next("select").append("<option value='" + data[i].region_id + "'"+s+">" + data[i].region_name + "</option>");
                        }
                        if(opt.regions){
                            var select = $(_self).next("select");
                            if(select.val()>0){
                                $(select).change();
                            }
                        }
                    }else{
                        opt.regions = "";
                        $(".mls_id").val(ids.join(","));
                        $(".mls_names").val(names.join(""));
                        $(_self).nextAll("select").remove()
                    }
                }else{
                    alert(data.msg);
                }
            });
        }
    }
    
}//obj end











