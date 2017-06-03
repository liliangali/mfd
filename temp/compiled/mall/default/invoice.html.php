          <div class="mt clearfix">
            <h2>发票信息</h2>
            <div class="clearfix fapiao" id="fapiao">
                <p class="tip">发票内容：购买商品明细<br>发票抬头：请确认单位名称正确,以免因名称错误耽搁您的报销。</p>
                <ul class="tabBtn">
                    <li data-id="0" class="cur">不要发票</li>
                    <li data-id="1">索要发票</li>
                </ul>
                <input type="hidden" name="order[invoice][id]" value="0" id="invoiceType" /> 
                <div class="tabLayer">
                    <div class="taitou hide">
                        <label><i>*</i>发票抬头：</label>
                        <input type="text" name="order[invoice][1][title]" />
                    </div>
                </div>
            </div>
           </div> 
