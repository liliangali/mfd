<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>麦富迪微信海报</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            font: inherit;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: none;
            height: 100%;
        }

        body {
            max-width: 640px;
            min-width: 320px;
            width: 100%;
            font-family: PingFangSC-Regular, Helvetica, "Droid Sans", Arial, sans-serif;
            background: #fffdf6;
            overflow-scrolling: touch;
            overflow-x: hidden;
            margin: 0 auto;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .hb_top {
            background-image: url(static/images/mfd_1.png);
            background-repeat: no-repeat;
            background-size: 100% 100%;
            width: 100%;
            height: 15.4rem;
            position: relative;
        }

        .hb_top_1 {
            width: 2.75rem;
            height: auto;
            position: absolute;
            left: 6.25%;
            bottom: 0.5rem;
        }

        .hb_top_2 {
            width: 3.5rem;
            height: auto;
            position: absolute;
            right: 3.125%;
            bottom: 0.5rem;
        }

        .hb_top_1 p,
        .hb_top_2 p {
            font-size: 0.6rem;
            -webkit-transform-origin-x: 0;
            -webkit-transform: scale(0.83);
            text-align: center;
            line-height: 20px;
        }

        .hb_top_2 p{
            width: 4.2rem;
        }



        .hb_top_2 img {
            box-shadow: 0px 0px 3px #000000;
        }

        .hb_top_3 {
            width: 46.25%;
            height: 7.4rem;
            position: absolute;
            left: 50%;
            bottom: -23.125%;
            margin-left: -23.125%;
            border-radius: 50%;
        }

        .hb_top_3 img{
            border-radius: 50%;
        }

        .hb_xx {
            width: 100%;
            height: 6rem;
            position: relative;
            font-size: 0.5rem;
            margin-bottom: 1rem;
        }

        .hb_xx_left {
            background-image: url(static/images/mfd_6.png);
            background-size: 100% 100%;
            width: 8rem;
            height: 4rem;
            line-height: 1rem;
            position: absolute;
            left: 0.5rem;
            bottom: 0;
            color: #63625f;
        }

        .hb_xx_left p:nth-child(4) {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .hb_xx_right {
            background-image: url(static/images/mfd_7.png);
            background-size: 100% 100%;
            width: 6.5rem;
            height: 3rem;
            line-height: 1rem;
            position: absolute;
            right: 0.5rem;
            bottom: 0;
            text-align: right;
            color: #63625f;
        }

        .hb_xx_right p:nth-child(2) {
            text-indent: 2em;
            display: -webkit-box;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            word-wrap: break-word;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
    </style>
    <script type="text/javascript">
        var docEl = document.documentElement,
                //当设备的方向变化（设备横向持或纵向持）此事件被触发。绑定此事件时，
                //注意现在当浏览器不支持orientationChange事件的时候我们绑定了resize 事件。
                //总来的来就是监听当然窗口的变化，一旦有变化就需要重新设置根字体的值
                resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                recalc = function() {
                    //设置根字体大小
                    var tagFont = 20 * (docEl.clientWidth / 320)
                    if(tagFont > 30) {
                        docEl.style.fontSize = '30px';
                    } else {
                        docEl.style.fontSize = 20 * (docEl.clientWidth / 320) + 'px';
                    }
                };
        //绑定浏览器缩放与加载时间
        window.addEventListener(resizeEvt, recalc, false);
        document.addEventListener('DOMContentLoaded', recalc, false);
    </script>
</head>

<body>
<div class="hb_top">
    <div class="hb_top_1">
        <img src="static/images/mfd_4.png" />
        <p>大是大非</p>
    </div>

    <div class="hb_top_2">
        <img src="static/images/mfd_3.png" />
        <p>长按识别二维码</p>
    </div>

    <div class="hb_top_3">
        <img src="static/images/mfd_5.png" />
    </div>
</div>

<div class="hb_xx">
    <div class="hb_xx_left">
        <p>狗狗：<span>安迪</span></p>
        <p>犬期：<span>成犬期</span></p>
        <p>生日：<span>2016-03-05</span></p>
        <p>功效：<span>营养美味，亮泽毛发，清新口气</span></p>
    </div>

    <div class="hb_xx_right">
        <p>主人寄语</p>
        <p>365天的陪伴，谢谢有你 相信你就是我独一无二的选择</p>
    </div>
</div>

<div class="hb_bottom">
    <img src="static/images/mfd_2.png" />
</div>
</body>

</html>