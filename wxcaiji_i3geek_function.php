<?php
define("I3GEEK_READING_ROOT",ABSPATH."reading/");
define("I3GEEK_READING_PLUGIN_ROOT",WP_PLUGIN_DIR."/wxcaiji_i3geek/");
ini_set('max_execution_time', '100');

if(!class_exists('Snoopy')){
	require_once("Snoopy.class.php"); 
}
if(!class_exists('simple_html_dom_node')){
	require_once("simple_html_dom.php"); 
}
if(!class_exists('FileUtil')){
	require_once('fileutil.php'); 
}
class wxcaiji_i3geek_function{

	//复制内容
	public static function reading_root_init(){
		if(is_dir(I3GEEK_READING_ROOT."css/") && file_exists(I3GEEK_READING_ROOT."qrcode"))
		{
			//完成，没问题
		}
		else
		{

			FileUtil::copyDir(I3GEEK_READING_PLUGIN_ROOT."reading/css",I3GEEK_READING_ROOT."css",true);
			FileUtil::copyFile(I3GEEK_READING_PLUGIN_ROOT."reading/qrcode",I3GEEK_READING_ROOT."qrcode",true);
		}
	}
	//判断入口
	public static function readingroot_isWritable(){
		if (is_writable(I3GEEK_READING_ROOT) )
			{
				wxcaiji_i3geek_function::reading_root_init();
				return true;
			}
		else
			return false;
	}
	function down2file($url,$site_url){
		$_path = I3GEEK_READING_ROOT;
		$snoopy = new Snoopy;
		// $url = "http://mp.weixin.qq.com/s?__biz=MjM5MTQ2MjA3Ng==&mid=404586426&idx=1&sn=4c9d30cc097f4f5dffb94e4918e53749#rd";
		$snoopy->fetch($url); //获取所有内容
		$timestamp = time();
		//解析内容
		$myhtml = str_get_html($snoopy->results);
		if($myhtml == "") return false;
		$title = $myhtml->find ('h2[id=activity-name]',0 )->plaintext; //显示标题
		$time = $myhtml->find ('em[id=post-date]',0 )->plaintext; //显示时间

		$content = $myhtml->find ('div.rich_media_content',0 )->innertext; 
		$myhtml = str_get_html($content);//显示内容
		$ret = $myhtml->find('img');
		$img_array = $this->downloadImg($ret,$timestamp,$_path);

		//保存页面
		$result_url = $this->wirteHTML($timestamp,$title,$time,$myhtml,$_path);
		return $site_url .'reading/' . $result_url;
	}
	function down2blog($url,$site_url){
		if(!function_exists('is_user_logged_in'))   
			require (ABSPATH . WPINC . '/pluggable.php');
		$_path = I3GEEK_READING_ROOT;
		$snoopy = new Snoopy;
		$snoopy->fetch($url); //获取所有内容
		$timestamp = time();
		//解析内容
		$myhtml = str_get_html($snoopy->results);
		if($myhtml == "") return false;
		$title = $myhtml->find ('h2[id=activity-name]',0 )->plaintext; //显示标题
		$content = $myhtml->find ('div.rich_media_content',0 )->innertext; 
		$myhtml = str_get_html($content);//显示内容
		$ret = $myhtml->find('img');
		$img_array = $this->downloadImg2Blog($ret,$timestamp,$_path,$site_url);
		$myhtml = str_replace("<br />", "", $myhtml);
		$myhtml = $myhtml . '<p style="text-align: right;"><em>--by <a href="http://www.i3geek.com" target="_blank">i3geek</a></em></p>';
		$my_post = array(
			'post_title' => $title,
			'post_content' => $myhtml,
			'post_status' => 'publish'
		);
      	$rt = wp_insert_post( $my_post );
      	if($rt != 0)//成功
      	{
        	$permalink = get_permalink($rt);
        	if($permalink != false) //成功
        		return $permalink;
        	else
        		return -1;
      	}
      	else//失败
        	return -1;
	}
	//写入文件
	function wirteHTML($name,$title,$date,$content,$_path){
		FileUtil::createdir($_path.'article/',0777);
	  	$path_file = $_path.'article/'.$name.'.html';
	  	$static1 = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>';
	  	$static2 = '</title><link rel="stylesheet" type="text/css" href="../css/page_mp_article_improve2a7a3f.css"><link rel="stylesheet" type="text/css" href="../css/page_mp_article_improve_combo2a7a3f.css"><link rel="stylesheet" type="text/css" href="../css/not_in_mm2a7a3f.css"></head><body id="activity-detail" class="zh_CN mm_appmsg not_in_mm" ontouchstart=""><div id="js_article" class="rich_media"><div class="rich_media_inner"><div id="page-content"><div id="img-content" class="rich_media_area_primary"><h2 class="rich_media_title" id="activity-name">';
	  	$static3 = '</h2><div class="rich_media_meta_list"><em id="post-date" class="rich_media_meta rich_media_meta_text">';
	  	$static4 = '</em> <em class="rich_media_meta rich_media_meta_text">爱上极客</em> <a class="rich_media_meta rich_media_meta_link rich_media_meta_nickname" href="#" id="post-user">爱上极客</a></div><div class="rich_media_content" id="js_content">';
	  	$static5 = '</div></div></div></div><div id="js_pc_qr_code" class="qr_code_pc_outer" style="display:block"><div class="qr_code_pc_inner"><div class="qr_code_pc"><img id="js_pc_qr_code_img" class="qr_code_pc_img" src="../qrcode"><p>微信扫一扫<br>关注该公众号</p></div></div></div></div></body></html>';
		$display = $static1.$title.$static2.$title.$static3.$date.$static4.$content.$static5;
	  	$fopen = fopen($path_file,'wb');//新建文件命令
	  	fputs($fopen,$display);//向文件中写入内容; 
	  	fclose($fopen); 
	  	return 'article/'.$name.'.html';
	}
	//下载图片2BLOG
	function downloadImg2Blog($img_array,$name,$_path,$www_root){
		$result_array = array();
		$date_time_array = getdate(time());
		$path_file = $_path.'images/'.$date_time_array[year].'/'.$date_time_array[mon].'/'.$date_time_array[mday].'/'.$name;
		FileUtil::createdir($path_file,0777);
		foreach ($img_array as $k => $_src) {
			$img_src = $_src->getAttribute('data-src');
			$img_type = $_src->getAttribute('data-type');	
			$path = $path_file . '/' . $k . '.' . $img_type;
			$this->saveImgURL($img_src,$path);
			$_src->removeAttribute('data-src');
			$_src->removeAttribute('data-type');
			$_src->removeAttribute('data-ratio');
			$_src->removeAttribute('data-w');
			$_src->setAttribute('src',$www_root.'reading/images/'.$date_time_array[year].'/'.$date_time_array[mon].'/'.$date_time_array[mday].'/'.$name. '/' . $k . '.' . $img_type);
			$result_array[] = $path;
		}
		return $result_array;
	}
	//下载图片
	function downloadImg($img_array,$name,$_path){
		$result_array = array();
		$date_time_array = getdate(time());
		$path_file = $_path.'images/'.$date_time_array[year].'/'.$date_time_array[mon].'/'.$date_time_array[mday].'/'.$name;
		FileUtil::createdir($path_file,0777);
		foreach ($img_array as $k => $_src) {
			$img_src = $_src->getAttribute('data-src');
			$img_type = $_src->getAttribute('data-type');	
			$path = $path_file . '/' . $k . '.' . $img_type;
			$this->saveImgURL($img_src,$path);
			$_src->removeAttribute('data-src');
			$_src->removeAttribute('data-type');
			$_src->removeAttribute('data-ratio');
			$_src->removeAttribute('data-w');
			$_src->setAttribute('src','../images/'.$date_time_array[year].'/'.$date_time_array[mon].'/'.$date_time_array[mday].'/'.$name. '/' . $k . '.' . $img_type);
			$result_array[] = $path;
		}
		return $result_array;
	}
	function saveImgURL($url,$path) {        
        if (is_file($path)) {
            unlink($path);//若存在则删除
        }
        $imgFile = file_get_contents($url);
        $flag = file_put_contents($path, $imgFile);
        return $flag;
	}

	public static function getNoticeMsg(){
		$handle = fopen("http://wx.i3geek.com/notice.php","rb");
		if($handle == '')
			return "请更新至最新版，或请关注 <a href=\"http://www.i3geek.com\" target=\"_blank\">论坛</a>";
		$content = "";
		while (!feof($handle)) 
			$content .= fread($handle, 10000);
		fclose($handle);
		$content = json_decode($content);
		if($content->{'code'} == 0)
			return -1;
		else{
			return $content->{'msg'};
		}
	}
}
?>