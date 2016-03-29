<?php

  $pageMax=10;
  $table_page = 1;
  if(!empty($_GET['table_page']))
    $table_page = $_GET['table_page'];
  $total_page = wxcaiji_i3geek_queryCount($pageMax);
  if($table_page < 2) $table_page = 1;
  if($table_page > $total_page) $table_page = $total_page;
  wp_enqueue_script('jquery');
  wp_enqueue_script( 'wxcaiji-i3geek-script' );
?>
<div>  
    <h2>微信文章采集设置</h2>  
		<?php if(!wxcaiji_i3geek_function::readingroot_isWritable()){ ?>
          <div id="message" class="updated" style="border-left-color: #d54e21;background: #fef7f1;">
            <p>目录没有写权限！请设置根目录权限777，或根目录创建文件夹reading并设置目录权限777 <a href="http://www.i3geek.com" target="_blank">查看帮助</a></p>
          </div>
    <?php } ?>

    <?php if(wxcaiji_i3geek_function::getNoticeMsg()!=-1){ ?>
          <div id="notice_msg" class="updated" style="border-left-color: #00a0d2;background: #f7fcfe;">
            <p><strong>公告</strong>： <?php echo wxcaiji_i3geek_function::getNoticeMsg(); ?></p>
          </div>
    <?php } ?>
         <!-- Nav tabs -->
        <h2 class="nav-tab-wrapper" style="border-bottom: 1px solid #ccc;">
          <a class="nav-tab" href="javascript:;" id="tab-title-qiniutek">文章采集</a>
          <a class="nav-tab" href="javascript:;" id="tab-title-local">公众号采集</a>
          <a class="nav-tab" href="javascript:;" id="tab-title-thumb">历史记录</a>
          <a class="nav-tab" href="http://bbs.i3geek.com" target="_blank" id="tab-title-remote">帮助/论坛</a>
        </h2>
        <div id="tab-qiniutek" class="div-tab hidden" style="display: none;">
          <h3>文章采集</h3>
            <?php if($GLOBALS['msg_error'] == 1){ ?>
              <div id="message" class="updated" style="border-left-color: #d54e21;background: #fef7f1;">
                <p>采集失败</p>
              </div>
            <?php }else if($GLOBALS['msg_error'] == 2){ ?>
              <div id="message" class="updated">
                <p>采集完成</p>
              </div>
            <?php } ?>
            <p><strong>* 使用帮助请看：<a href="http://bbs.i3geek.com" target="_blank">查看帮助</a>。</strong></p>
            <table class="form-table">
              <form method="post" action >
              <tbody>
                <tr>
                  <th scope="row">
                    <label for="host">文章名称</label>
                  </th>
                  <td>
                    <input type="text" name="name" class="type-text regular-text">
                    <p><i>文章名称只用于后台记录，便于管理查看。采集后的文章中不做显示。</i></p>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="bucket">链接地址</label>
                  </th>
                  <td>
                    <input type="text" name="link" class="type-text regular-text" value="">
                    <p><i>微信文章的地址连接。如：https://mp.weixin.qq.com/s?__biz=...</i></p>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="access">采集类型</label>
                  </th>
                  <td>
                    <input type="radio" name="article_type" value="blog" checked/>新文章 <input type="radio" name="article_type" value="wx" />原微信样式
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <input type="hidden" name="action" value="wxcaiji_i3geek_insert" /> 
                    <?php wp_nonce_field( 'wxcaiji-i3geek-post' ); ?>
                    <input type="submit" value="采集" class="button-primary" />
                    <img src="<?php echo network_site_url( '/' )."wp-content/plugins/wxcaiji_i3geek/"."loading.gif"; ?>"  style="display:none;"/>
                  </th>
                </tr>
              </tbody>
              </form>
            </table>
        </div>
       

    <div id="tab-local" class="div-tab hidden" style="display: none;">
          
          <h3>公众号采集</h3>
            <p><strong>* 测试阶段，部分功能尚未开发，请关注<a href="http://bbs.i3geek.com" target="_blank">论坛</a>或加入QQ群：194895016，下载最新测试版本。<a href="http://bbs.i3geek.com" target="_blank">查看帮助</a>。</strong></p>
            <table class="form-table">
              <form method="post" action >
              <tbody>
                <tr>
                  <th scope="row">
                    <label for="host">公众微信号</label>
                  </th>
                  <td>
                    <input type="text" name="name" class="type-text regular-text" id="weixin">
                    <p><i>微信公众号（服务号），注：是微信号，不是微信名称</i></p>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="bucket">KEY(<a href="http://wx.i3geek.com" target="_blank">获得KEY</a>)</label>
                  </th>
                  <td>
                    <input type="text" name="link" class="type-text regular-text" value="" id="key">
                    <p><i>用户KEY，具体获得方法可以参考：<a href="http://bbs.i3geek.com" target="_blank">帮助</a></i></p>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <input type="hidden" name="action" value="wxcaiji_i3geek_insert" /> 
                    <input type="button" value="抓取文章" class="button-primary" onclick="get_wx_list()" id="button_caiji"/>
                    <img src="<?php echo network_site_url( '/' )."wp-content/plugins/wxcaiji_i3geek/"."loading.gif"; ?>" id="loading_button_caiji" style="display:none;"/>
                  </th>
                </tr>
              </tbody>
              </form>
            </table>

            <form action method="post"> 
              <div id="cj_rt_message" class="updated" style="border-left-color: #d54e21;background: #fef7f1;display:none;"></div>
              <table id="json_table" border="1px" style="width: 90%; display:none;"></table>
              <div id="bt_caiji" style="display:none;" ><input type="hidden" name="action" value="wxcaiji_i3geek_caiji" /><?php wp_nonce_field( 'wxcaiji-i3geek-post' ); ?><p>采集类型： <input type="radio" name="article_type" value="blog" checked/>新文章 <input type="radio" name="article_type" value="wx" />原微信样式 <input type="submit" value="批量采集" class="button-primary" /></p></div>

            </form>
        </div>


    <div id="tab-thumb" class="div-tab hidden" style="display: none;">
        <h3>历史记录</h3>
        <table border="1">
          <tr><td>ID</td><td>名称</td><td>链接</td></tr>
          <?php
            $results=wxcaiji_i3geek_queryDisplay($table_page,$pageMax);
					  $i=0;
            while ($i< count($results)){
              echo "<tr><td>".$results[$i]->id."</td><td>".esc_html($results[$i]->name)."</td><td>".esc_url($results[$i]->link)."</td></tr>";
              $i++;
            }
          ?>
        </table>
        <div style="display: flex;">
        <div style="float:left">
				  第<?php echo $table_page ?>页/共<?php echo $total_page ?>页
				</div>
				<div style="float:right">
				  	<?php
				  		if($table_page<2){
					  		echo "<button onclick=\"window.location.href='?page=wxcaiji_i3geek&table_page=1'\" class=\"btn btn-primary disabled\">上一页</button>";
						}else{
							echo "<button onclick=\"window.location.href='?page=wxcaiji_i3geek&table_page=". ($table_page-1) ."'\" class=\"btn btn-primary\">上一页</button>";
						}
						if($table_page == $total_page){
					    	echo "<button onclick=\"window.location.href='?page=wxcaiji_i3geek&table_page=1'\" class=\"btn btn-primary disabled\">下一页</button>";
						}else{
							echo "<button onclick=\"window.location.href='?page=wxcaiji_i3geek&table_page=". ($table_page+1) ."'\" class=\"btn btn-primary\">下一页</button>";
						}
					?>
				</div>
				</div>
    </div>


    <hr>
    <div style='text-align:center;'>
      <a href="http://www.i3geek.com/archives/997" target="_blank">插件主页</a> | <a href="http://bbs.i3geek.com" target="_blank">插件论坛</a> | <a href="mailto:yan@i3geek.com" target="_blank">联系作者</a> | <a href="http://www.i3geek.com" target="_blank">作者主页</a> | <a href="http://bbs.i3geek.com" target="_blank">意见反馈</a> | <a href="http://www.i3geek.com" target="_blank">i3geek.com</a> | QQ群：194895016
    </div>
</div>  


<script type='text/javascript'>
  jQuery(function(jQuery){
    if(jQuery('div.div-tab').length){
      var current_tab = '';

      if(jQuery('#current_tab').length){ // 如果是设置页面，获取当前的 current_tab 的值
        current_tab = jQuery('#current_tab').first().val();
      }
      
      if(current_tab == ''){ //设置第一个为当前 tab显示
        current_tab = jQuery('div.div-tab').first()[0].id.replace('tab-','');
      }

      var htitle    = jQuery('#tab-title-'+current_tab).parent()[0].tagName;

      jQuery('div.div-tab').hide();

      jQuery('#tab-title-'+current_tab).addClass('nav-tab-active');
      jQuery('#tab-'+current_tab).show();
      jQuery('#current_tab').val(current_tab);

      jQuery(htitle+' a.nav-tab').on('click',function(){

        var prev_tab  = current_tab;
        current_tab   = jQuery(this)[0].id.replace('tab-title-','');

        jQuery('#tab-title-'+prev_tab).removeClass('nav-tab-active');
        jQuery(this).addClass('nav-tab-active');

        jQuery('#tab-'+prev_tab).hide();
        jQuery('#tab-'+current_tab).show();
        
        if(jQuery('#current_tab').length){
          jQuery('#current_tab').val(current_tab);
        }
      });
    }
  return false;
});
</script>
<?php

?>