

<style>
.btn { display: inline-block; *zoom:1;
*display:inline;
width: 158px; height: 38px; padding: 0; margin: 0; border: 1px solid #b0b0b0; font-size: 14px; line-height: 38px; text-align: center; color: #b0b0b0; cursor: pointer; -webkit-transition: all 0.4s; transition: all 0.4s }
.btn:hover {text-decoration: none; color: #b0b0b0}
.btn:focus {outline: 0}
.btn:active {-webkit-box-shadow: inset 0 2px 4px rgba(0,0,0,0.18); box-shadow: inset 0 2px 4px rgba(0,0,0,0.18)}
.btn[disabled] {border-style: dashed !important; border-color: #e0e0e0; background-color: #fff !important; color: #b0b0b0 !important; cursor: default !important}
.btn-disabled {background: #e0e0e0 !important; border-color: #e0e0e0 !important; color: #b0b0b0 !important; cursor: default !important}
.btn-small {width: 118px; height: 28px; font-size: 12px; line-height: 28px}
.btn-large {width: 178px; height: 48px; line-height: 48px}
.btn-block {display: block; width: 100%; padding-left: 0; padding-right: 0}
.input-label {position: absolute; left: 12px; top: 11px; z-index: 2; padding: 0 3px; font-size: 14px; line-height: 18px; color: #b0b0b0; background: transparent; cursor: text; -webkit-transition: all 0.2s linear; transition: all 0.2s linear}
.input-text {width: 186px; height: 18px; padding: 10px 16px; border: 1px solid #e0e0e0; font-size: 14px; line-height: 18px; background: #fff; -webkit-transition: border-color 0.2s linear; transition: border-color 0.2s linear}
.input-text:hover {border-color: #b0b0b0}
.input-text:focus {outline: 0}
textarea.input-text {height: 3em; resize: vertical}
.xm-select {display: block; width: 220px; margin-right: 14px}
.xm-select label {position: absolute; right: 14px; top: 11px; z-index: 1; width: 16px; height: 16px; padding: 0; font-size: 16px; line-height: 1; color: #b0b0b0; cursor: pointer; pointer-events: none; background:url(data:image/gif;base64,R0lGODlhDQAHAIAAAAAAAP///yH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MENFRDg2QzMzQjJGMTFFNTlCQTdEMjhENkJCNEFGRjgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MENFRDg2QzQzQjJGMTFFNTlCQTdEMjhENkJCNEFGRjgiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDowQ0VEODZDMTNCMkYxMUU1OUJBN0QyOEQ2QkI0QUZGOCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDowQ0VEODZDMjNCMkYxMUU1OUJBN0QyOEQ2QkI0QUZGOCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAAAAAAALAAAAAANAAcAAAIRBIKZFqzo2lERTvkszvbylhUAOw==) no-repeat center center; opacity:.5;}
.xm-select select {-webkit-box-sizing: border-box; box-sizing: border-box; width: 120%; max-width: 120%; min-width: 120%; height: 38px; margin: 0; border: 0; padding: 0 16px; -webkit-appearance: none; -moz-appearance: none; appearance: none; font-size: 14px; font-weight: 400; line-height: 38px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; _zoom: 1; vertical-align: middle; background: none; color: #333; outline: none; cursor: pointer}
.xm-select .dropdown {position: relative; display: block; overflow: hidden; _zoom: 1; width: 100%; max-width: 100%; height: 38px; border: 1px solid #e0e0e0; background: #fff; color: #333; -webkit-transition: border-color 0.2s linear; transition: border-color 0.2s linear}
.xm-select:hover .dropdown {border-color: #b0b0b0}
.xm-ie-select label {display: none}
.xm-ie-select select {-webkit-box-sizing: content-box; box-sizing: content-box; width: 96%; max-width: 96%; min-width: 96%; height: 28px 9; padding: 0 2%}
.xm-ie-select .dropdown { height: 33px; *height:32px;
padding-top: 5px; *padding-top:8px;
*border:0
}
.btn-gray {background: #b0b0b0; border-color: #b0b0b0; color: #fff}
.btn-gray:hover {background-color: #999; border-color: #999; color: #fff}
.btn-primary {background: #fff; border-color: #dfdfdf; color: #666}
.btn-primary:hover {background-color: #e66800; border-color: #e66800; color: #fff}
.form-section {position: relative; margin: 0 0 14px; padding: 0; border: 0; text-align: left}
.form-section .input-text::-webkit-input-placeholder {
color:#fff
}
.form-section .input-text::-moz-placeholder {
color:#fff
}
.form-section .input-text:-ms-input-placeholder {
color:#fff
}
.form-section .input-text::placeholder {
color:#fff
}
.form-section .input-text[disabled] {background-color: #f5f5f5}
.form-section .input-text[disabled]::-webkit-input-placeholder {
color:#f5f5f5
}
.form-section .input-text[disabled]::-moz-placeholder {
color:#f5f5f5
}
.form-section .input-text[disabled]:-ms-input-placeholder {
color:#f5f5f5
}
.form-section .input-text[disabled]::placeholder {
color:#f5f5f5
}
.form-section .msg {position: absolute; left: 10px; top: -26px; z-index: 5; padding: 6px 12px; margin: 0; font-size: 12px; -webkit-box-shadow: 0 3px 4px rgba(0,0,0,0.18); box-shadow: 0 3px 4px rgba(0,0,0,0.18)}
.form-section .msg-error {visibility: hidden; visibility: visible 9; display: none 9; opacity: 0; filter: alpha(opacity=0)9; background-color: #e53935; color: #fff; -webkit-transform: translate3d(0, 5px, 0); transform: translate3d(0, 5px, 0); -webkit-transition: all 0.2s; transition: all 0.2s}
.form-section .msg-error:after {position: absolute; top: 29px; left: 15px; width: 12px; height: 6px; content: ''; background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAGCAYAAAD37n+BAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDIxIDc5LjE1NTc3MiwgMjAxNC8wMS8xMy0xOTo0NDowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MkY4QzZBOTEzMDMyMTFFNTlCQzFDMTI2ODdDRkMyNzciIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MkY4QzZBOTAzMDMyMTFFNTlCQzFDMTI2ODdDRkMyNzciIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBQTZCNDRFMzJFOTAxMUU1OUJDMUMxMjY4N0NGQzI3NyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBQTZCNDRFNDJFOTAxMUU1OUJDMUMxMjY4N0NGQzI3NyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PoqQVgUAAABNSURBVHjaYnxhrlvPwMDQwEAcaGACEo1AXE+EYpCaRiYop4mApnqoGgYmJEGQQB0WxXUwxegaQKAZiGuR+LVQMThgwWJiCw42GAAEGADDKgvj76U41wAAAABJRU5ErkJggg==") no-repeat 50% 50%}
.form-section-focus .input-text {border-color: #e66800}
.form-section-focus .input-text::-webkit-input-placeholder {
color:#b0b0b0;
-webkit-transition:color 0.2s 0.2s linear;
transition:color 0.2s 0.2s linear
}
.form-section-focus .input-text::-moz-placeholder {
color:#b0b0b0;
transition:color 0.2s 0.2s linear
}
.form-section-focus .input-text:-ms-input-placeholder {
color:#b0b0b0;
transition:color 0.2s 0.2s linear
}
.form-section-focus .input-text::placeholder {
color:#b0b0b0;
-webkit-transition:color 0.2s 0.2s linear;
transition:color 0.2s 0.2s linear
}
.form-section-focus .input-label {color: #e66800}
.form-section-focus .msg-error {display: block 9; visibility: visible; opacity: 1; filter: alpha(opacity=100)9; -webkit-transform: translate3d(0, 0, 0); transform: translate3d(0, 0, 0)}
.form-section-active .input-label {top: -7px; font-size: 12px; background: #fff}
.address-edit-box {width: 334px}
.address-edit-box .box-main {height: 340px; padding: 30px 20px 13px}
.address-edit-box .input-text {width: 260px}
.address-edit-box textarea {height: 40px; line-height: 20px}
.address-edit-box .form-section .xm-select {width: 294px; margin-right: 0}
.address-edit-box .form-select-2 .xm-select {float: left; width: 140px}
.address-edit-box .form-select-2 .select-province, .address-edit-box .form-select-2 .select-county {margin-right: 14px}
.address-edit-box .form-confirm {padding: 14px 4px 15px 20px; border-top: 1px solid #f5f5f5}
.address-edit-box .form-confirm .btn {float: left; width: 140px; height: 30px; line-height: 30px; margin-right: 14px}
.address-edit-box .form-confirm .btn-primary {margin-right: 10px}
</style>


<div class="address-edit-box">
    <form id="addressForm">
    <div class="box-main">
        <div class="form-section <?php if ($this->_var['data']['consignee']): ?> form-section-active <?php endif; ?>">
            <label for="user_name" class="input-label">姓名</label>
            <input type="text" placeholder="收货人姓名" name="user_name" id="user_name" class="input-text" validate="required" value="<?php echo $this->_var['data']['consignee']; ?>" />
            <input type="hidden" name="id" value="<?php echo $this->_var['data']['addr_id']; ?>" />
        </div>
        <div class="form-section <?php if ($this->_var['data']['phone_mob']): ?> form-section-active <?php endif; ?>">
            <label for="user_phone" class="input-label">手机号</label>
            <input type="text" placeholder="11位手机号" name="user_phone" id="user_phone" class="input-text" validate="required|phone" value="<?php echo $this->_var['data']['phone_mob']; ?>" />
        </div>
        <div class="form-section form-select-2 clearfix">
            <div class="xm-select select-province" data-index="0">
                <div class="dropdown">
                    <label for="J_province" class="iconfont"></label>
                    <select id="J_province" name="province" validate="required">
                        <option value="2">中国</option>
                    </select>
                    <select id="J_city" name="city" validate="required">
                        <option value="0">城市/地区</option>
                    </select>
                </div>
            </div>
            <div class="xm-select select-city" data-index="1">
                <div class="dropdown">
                    <label for="J_city" class="iconfont"></label>
                    <select id="J_city" name="city" validate="required">
                        <option value="0">城市/地区</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-section clearfix">
            <div class="xm-select select-county" data-index="2">
                <div class="dropdown">
                    <label for="J_county" class="iconfont"></label>
                    <select id="J_county" name="county" validate="required">
                        <option value="0">区/县</option>
                    </select>
                </div>
            </div>
        </div>
        <input type="hidden" value="<?php echo $this->_var['data']['region_id']; ?>" name="area_id"  id="area_id" validate="required" />
        <input type="hidden" value="<?php echo $this->_var['data']['region_id']; ?>" name="area_list"  id="area_list" />
        <input type="hidden" value="<?php echo $this->_var['data']['region_name']; ?>" name="area_name"  id="area_name" />
        <div class="form-section <?php if ($this->_var['data']['address']): ?> form-section-active <?php endif; ?>">
            <label for="user_adress" class="input-label">详细地址</label>
            <textarea placeholder="详细地址，路名或街道名称，门牌号" name="user_adress" id="user_adress" type="text" class="input-text" validate="required"><?php echo $this->_var['data']['address']; ?></textarea>
        </div>
        <div class="form-section <?php if ($this->_var['data']['zipcode']): ?> form-section-active <?php endif; ?>">
            <label for="user_zipcode" class="input-label">邮政编码</label>
            <input type="text" name="user_zipcode" id="user_zipcode" value="<?php echo $this->_var['data']['zipcode']; ?>" class="input-text" validate="required|number|maxlength|minlength" maxlength="6" minlength="6">
        </div>
    </div>
    </form>
    <div class="form-confirm clearfix"> <a id="J_save" class="btn btn-primary" href="javascript:void(0);">保存</a> <a id="J_cancel" class="btn btn-gray" href="javascript:void(0);">取消</a> </div>
</div>

