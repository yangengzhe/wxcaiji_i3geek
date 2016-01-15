<?php  
/*
Plugin Name: 微信文章采集
Plugin URI: http://www.i3geek.com/
Description: 微信文章采集，微信公众号（服务号）全部历史文章采集，自动采集。
Version: 1.0.0
Author: yan.i3geek
Author URI: http://www.i3geek.com/
License: GPL
*/
require_once 'wxcaiji_i3geek_function.php'; 
register_activation_hook( __FILE__, 'wxcaiji_i3geek_install');   
register_deactivation_hook( __FILE__, 'wxcaiji_i3geek_remove');  
$msg_error = 0;
$cmd = $_POST['action'];
switch ($cmd) {
    case 'wxcaiji_i3geek_insert':
          $GLOBALS['msg_error']=0;
          if(wxcaiji_i3geek_insert($_POST['name'],$_POST['link'],$_POST['article_type']))
            $GLOBALS['msg_error'] =2;
          else
            $GLOBALS['msg_error'] = 1;
        break;
    case 'wxcaiji_i3geek_caiji':
          $GLOBALS['msg_error']=0;
          if(!empty($_POST["caiji"])){
            $array_caiji = $_POST['caiji'];
          }
          for($i=0; $i< count($array_caiji); $i++){
            $_pos = strpos($array_caiji[$i],"|");
            $_title = substr($array_caiji[$i],0,$_pos);
            $_link = substr($array_caiji[$i],$_pos+3);
            if($_title != '' && $_link != '')
              if(wxcaiji_i3geek_insert($_title,$_link,$_POST['article_type']))
                continue;
            $GLOBALS['msg_error']=1;
            break;
          }
          if($i == count($array_caiji))
            $GLOBALS['msg_error']=2;
        break;
    case 'wxcaiji_i3geek_delete':
        wxcaiji_i3geek_delete($_POST['id']);
        break;
}

//安装，初始化数据库
function wxcaiji_i3geek_install() {
	global $wpdb;
 	$table_name = $wpdb->prefix . "wxcaiji_i3geek";  //获取表前缀，并设置新表的名称
    if($wpdb->get_var("show tables like $table_name") != $table_name) {  //判断表是否已存在
        $sql = "CREATE TABLE " . $table_name . " (
          id mediumint(11) NOT NULL AUTO_INCREMENT,
          name VARCHAR(255) NOT NULL,
          link VARCHAR(10000) NOT NULL,
          source VARCHAR(10000),
          PRIMARY KEY (id)
        )ENGINE = INNODB DEFAULT CHARSET = utf8;";
	require_once(ABSPATH . "wp-admin/includes/upgrade.php");  //引用wordpress的内置方法库
	dbDelta($sql);
	}
}
//卸载，删除数据库
function wxcaiji_i3geek_remove() {  
     global $wpdb;
	 $table_name = $wpdb->prefix . "wxcaiji_i3geek";  //获取表前缀，并设置新表的名称
	 $wpdb->query("DROP TABLE IF EXISTS ".$table_name.";");
}
//插入数据
function wxcaiji_i3geek_insert($name='undefined',$source,$type){
	global $wpdb;
  $site_url = network_site_url( '/' ); //站点URL
  if(readingroot_isWritable() == true)
  {
      if($type == 'wx')
        $link = down2file($source,$site_url);
      else
        $link = down2blog($source,$site_url);
  }
  else
      return false;
  if(!$link) return false;
	$table = $wpdb->prefix . "wxcaiji_i3geek";
	$data_array = array(  
		'name' => $name,  
		'link' => $link,
    'source' => $source
	);  
	$wpdb->insert($table,$data_array); 
  return true;
}
//删除数据
function wxcaiji_i3geek_delete($id){
	global $wpdb;
	$table = $wpdb->prefix . "wxcaiji_i3geek";
	$wpdb->query("DELETE FROM ".$table." WHERE id = ".$id.";");
}
//查询数据
function wxcaiji_i3geek_queryAll(){
	global $wpdb;
	$table = $wpdb->prefix . "wxcaiji_i3geek";
    $querystr = "SELECT id,name,link FROM ".$table." ORDER BY id DESC;";
	return $wpdb->get_results($querystr);
}
//查询数据(分页)
function wxcaiji_i3geek_queryDisplay($page,$pageMax){
	global $wpdb;
	$table = $wpdb->prefix . "wxcaiji_i3geek";
	$start = ($page-1)*$pageMax;
    $querystr = "SELECT id,name,link FROM ".$table." ORDER BY id DESC LIMIT ".$start.",".$pageMax.";";//第n+1条开始，显示m条
	return $wpdb->get_results($querystr);
}
//获得数据总数
function wxcaiji_i3geek_queryCount($pageMax){
	global $wpdb;
	$table = $wpdb->prefix . "wxcaiji_i3geek";
    $querystr = "SELECT COUNT(id) AS count FROM ".$table;
	$results = $wpdb->get_results($querystr);
	return ceil($results[0]->count/$pageMax);
}
//添加菜单
if( is_admin() ) {
    add_action('admin_menu', 'wxcaiji_i3geek_menu');
}
//菜单
function wxcaiji_i3geek_menu() {
    add_options_page('微信文章采集设置', '微信文章采集', 'administrator','wxcaiji_i3geek', 'wxcaiji_i3geek_html_page');
}
//设置界面
function wxcaiji_i3geek_html_page() {
	$pageMax=10;
	$table_page = $_GET['table_page'];
	$total_page = wxcaiji_i3geek_queryCount($pageMax);
	if($table_page < 2) $table_page = 1;
	if($table_page > $total_page) $table_page = $total_page;

  require_once('html.php');
}  

?>