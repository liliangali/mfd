<?php echo $this->fetch('../header-new.html'); ?>
<link href="/public/static/pc/css/user.css" rel="stylesheet">
<link href="/diy/assets/css/croppic.css" rel="stylesheet">
<script src="../static/v1/js/jquery.min.js"></script>
<style>
.borline td {padding:10px 0px;}
.ware_list th {text-align:left;}
</style>
<script type="text/javascript" src="static/expand/jquery.js" charset="utf-8"></script>
<script type="text/javascript" src="static/expand/my97date/wdatepicker.js" charset="utf-8"></script>
<?php echo $this->_var['_head_tags']; ?> 
<script type="text/javascript">
//<!CDATA[
var PINER = {uid: "<?php echo $this->_var['session_info_user_id']; ?>"}; 
var SITE_URL = "<?php echo $this->_var['site_url']; ?>";
var REAL_SITE_URL = "<?php echo $this->_var['real_site_url']; ?>";
var REAL_BACKEND_URL = "<?php echo $this->_var['real_backend_url']; ?>";
var REGION_URL="<?php echo $this->build_url(array('app'=>'mlselection','act'=>'index','arg'=>'region')); ?>";
var reginUrl=REAL_SITE_URL+REGION_URL;
 $(function(){    
    regionInit("region");
}); 
</script>

<div class="user_box">
<?php echo $this->fetch('member.menu.html'); ?>


<div class="user_right fr">

        <div class="lntegral">
            <p class="mlntegral mlntegrals fl">个人资料</p>
        </div>

        <div class="profile">
            <div class="yhname">
                <div class="namexx">
                    <p class="portrait personal fl" id="">
                        <img src="<?php echo $this->_var['avatar']; ?>" width="94" height="94">
                        <!--<img src="/public/static/pc/images/bjpic.png" width="45" height="45" class="edit animated zoomIn">-->
                    </p>
                    <div class="member fl">
                        <p class="display"><?php echo $this->_var['user']['nickname']; ?></p>
                        <div class="zhaqc">
                            <p class="hpjw fl">账户安全：</p>
                            <div class="hptia fl"><p style="width:<?php echo $this->_var['percent']; ?>%;background-color:<?php echo $this->_var['color']; ?>"></p></div>
                            <p class="jiaog fl"><?php echo $this->_var['safe_level']; ?></p>
                            &nbsp;&nbsp;<?php if ($this->_var['percent'] < 100): ?> <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'safe')); ?>">完善设置&nbsp;></a><?php endif; ?>
                        </div>

                    </div>
                    <!--<p class="grzlewm fr"><img src="<?php echo $this->_var['profile']['erweima_url']; ?>"  id="erweima"><br>二维码名片</p>-->
                </div>
            </div>
            <!--基本资料-->
            <div class="grzlqh">
                <div class="grzltit" id="point">
                	<ul>
                    	<li><a href="member-profile.html#point">基本资料</a></li>
                        <li><span></span></li>
                        <li><a href="member-detailed.html#point">详细资料</a></li>
                        <li><span></span></li>
                        <li><a href="#point" class="grzlcur">更换头像</a></li>
                    </ul>
                </div>
                <form method="post" class="avatar_box">
                    <div class="tag_img">
                        <div id="croppic"><img src="<?php echo $this->_var['avatar']; ?>" width="200" height="200"></div>
                        <div class="file_btn">
                          <span class="btn" id="cropContainerHeaderButton">上传头像</span>
                          <input type="submit" value="保存" class="file_sub">
                        </div>
                        <input type="hidden" name="avatar" value="" />
                    </div>

                </form>

            </div>
        </div>
</div>

        </div>
</div>
</div>

<?php echo $this->fetch('footer.html'); ?>
<script src="/public/global/jquery-1.8.3.min.js"></script>
<script src="/public/global/luck/pc/luck.js"></script>
<script src="/public/global/jquery.form.js"></script>
<script src="/public/global/jcrop/js/jquery.Jcrop.js"></script>
<script src="/public/static/pc/js/public.js"></script>
<script src="/public/static/pc/js/usercenter.js"></script>
<link rel="stylesheet" href="/public/global/jcrop/css/jquery.Jcrop.css" type="text/css" />
<script src="/diy/js/jQuery.peSlider.js"></script>
<script src="/diy/js/croppic/croppic.min.js"></script>
<script>
    var croppicHeaderOptions = {
        //uploadUrl:'img_save_to_file.php',
        cropData:{
            "dummyData":1,
            "dummyData2":"asdas"
        },
        cropUrl:'fdiy-upfile.html',
        customUploadButtonId:'cropContainerHeaderButton',
        modal:false,
        processInline:true,
        loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
        onBeforeImgUpload: function(){
            $(".file_sub").hide();
            //alert(1)
            console.log('onBeforeImgUpload')
        },
        onAfterImgUpload: function(){
            // alert('aa')
            console.log('333')
        },
        onImgDrag: function(){console.log('onImgDrag')},
        onImgZoom: function(){console.log('onImgZoom')},
        onBeforeImgCrop: function(){console.log('onBeforeImgCrop')},
        onAfterImgCrop:function(){
            $('.file_img').attr('src',$('.croppedImg').attr('src'));
            $("input[name='avatar']").val($('.croppedImg').attr('src'));
            $(".file_sub").show();
        },
        onReset:function(){
            console.log('onReset')
        },
        onError:function(errormessage){console.log('onError:'+errormessage)}
    }
    var croppic = new Croppic('croppic', croppicHeaderOptions);



</script>


<script type="text/javascript">
function updateavatar() {
    window.location.href='/member-profile.html';
    //window.location.reload();
}
//    头像上传
$('#headEdit').click(function(){
    luck.open({
        title:'修改头像',
        content:'<div class="head-edit"><div class="step1"><form id="uploadForm"><div class="btn">上传头像<input type="file" name="avatar" accept="image/jpeg, image/png" onChange="uploadHead(this)"></div><p>jpg 或 png，大小不超过2M</p></form></div><div class="step2" style="display:none"><div class="picBox" id="picBox"></div><p class="btnBox"><button class="ok">确定</button><button class="reset">重新上传</button></p></div><div class="loading" style="display:none"><img src="/public/static/pc/images/loading.gif" width="90" height="90"><p class="tip">上传中...</p></div></div>',
        width:'400px',
        height:'auto',
        class:'mfd-luck'
    })
});
function uploadHead(e){
    $('#uploadForm').ajaxSubmit({
        url:'/member-getImg.html',
        type:'post',
        dataType:'json',
        beforeSubmit:function(a,b,c){
            $('.head-edit .step1').hide();
            $('.head-edit .loading').show();
        },
        success:function(d){
            if(!d.done){
                alert(d.msg);
                return
            }
            var data=null;
            var img=new Image;
            img.src=d.retval+'?'+Math.random();
            img.className="img";
            img.onload=function(){
                //切换状态
                $('.head-edit .loading').hide();
                $('.head-edit .step2').show();
                //追加图片
                var oDiv=$('<div/>');
                oDiv.html(this);
                $('#picBox').html(oDiv);
                oDiv.css('margin-top',(300-oDiv.height())/2>0?(300-oDiv.height())/2:0);
                //调用裁剪工具
                $(this).Jcrop({
                    //完成回调
                    onSelect:function(c){
                        data=c;
                    },
                    //移动回调
                    onChange:function(c){
                        data=c
                    },
                    //背景颜色
                    bgColor:'fff',
                    //背景透明度
                    bgOpacity:'.5',
                    //坐标
                    setSelect: [ 0, 0, 100, 100],//x,y,w,h
                    //最小
                    minSize:[100,100],
                    //最大
                    maxSize:[200,200],
                    //比例
                    aspectRatio:1/1
                });
                //绑定按钮事件
                $('.head-edit .ok').unbind('click').click(function(){
                    $.ajax({
                        url:'/member-editAvatar.html',
                        type:'POST',
                        dataType:"json",
                        data:{
                            data:'{"x":'+data.x+',"y":'+data.y+',"w":'+data.w+',"h":'+data.h+',"imgw":300,"imgh":'+(300/img.width*img.height)+'}',
                            avatar:d.retval
                        },
                        beforeSend: function(){
                            $('.head-edit .step2').hide();
                            $('.head-edit .loading').show();
                        },
                        success: function(d){
                            if(d.done){
                                $('#headEdit img:eq(0)').attr('src',d.retval.avatar+'?'+Math.random());
                                $('#erweima').attr('src',d.retval.erweima_url+'?'+Math.random());
                            }else{
                                alert(d.msg)
                            }
                            luck.close();
                        },
                        error:function(){
                            alert('errorasdfasdf')
                        }
                    })
                });
                $('.head-edit .reset').unbind('click').click(function(){
                    $('.head-edit .step1').show();
                    $('.head-edit .step2').hide();
                });
            }
        },
        error:function(){
            alert('error')
        }
    })
}
</script>
