var json;
function get_wx_list()
{
	var tips = jQuery("#cj_rt_message");
	if(jQuery("#weixin").val() == '' || jQuery("#key").val()=='')
	{
		var error_content = "请输入微信号或KEY";
		tips.html("<p>"+error_content+"</p>");
		tips.show();
		return;
	}
	jQuery.ajax({
		type:"POST",
		url: "http://wx.i3geek.com/weixin.php",
		data:{weixin:jQuery("#weixin").val(),key:jQuery("#key").val()},
		dataType:"jsonp",
		jsonp: "callback",
		jsonpCallback:"success_jsonpCallback",
		beforeSend:function(){
			jQuery("#json_table").hide();
			jQuery("#bt_caiji").hide();
			tips.hide();
			jQuery("#button_caiji").attr('disabled','');
			jQuery("#loading_button_caiji").show();
		},
		complete:function(){
			jQuery("#button_caiji").removeAttr('disabled');
			jQuery("#loading_button_caiji").hide();
		},
		success: function(msg){
			var code = msg.code;
			if(code == 1)//成功
			{
				json = JSON.stringify(msg);
				json2table();
			}
			else if(code == 3)
			{
				var error_content = "公众号不存在或无文章";
				tips.html("<p>"+error_content+"</p>");
				tips.show();
			}
			else if(code == 2 || code == 4)
			{
				var error_content = "KEY无效或积分不够请稍后再试，"+"<a href=\"http://bbs.i3geek.com/forum.php?mod=viewthread&tid=1&extra=page%3D1\" target=\"_blank\">查看获得积分方法</a>";
				tips.html("<p>"+error_content+"</p>");
				tips.show();
			}
			else
			{
				var error_content = "请稍后再试";
				tips.html("<p>"+error_content+"</p>");
				tips.show();
			}
	    }
	});
}

function json2table()
{
	var html_content = "<thead><tr><th style=\"width: 5%;\">全选 <input id=\"cb_caiji_all\" type=\"checkbox\" onClick=\"CheckSelect(this.form);\" > </th><th style=\"width: 30%;\">文章标题</th><th style=\"width: 65%;\">文章链接</th></tr></thead>";
	if(json == '')
	{
		//没有内容 无需转换
	}
	else
	{
		html_content = html_content + "<tbody>";
		var json_array = JSON.parse(json);
		var content = json_array.content;
		jQuery.each(content, function(index, item) {
			html_content = html_content + "<tr><th>"+ "<input name=\"caiji[]\" type=\"checkbox\" value=\"" +item.title +'|y&'+ item.link+"\" />" +"</th><td>"+item.title+"</td><td style=\"word-break: break-all;\"><samp>"+item.link+"</samp></td></tr>";
		});
		html_content = html_content + "</tbody>";
	}
	jQuery("#json_table").html(html_content);
	jQuery("#json_table").show();
	jQuery("#bt_caiji").show();
}

 function CheckSelect(thisform)  
  {
  	var cb_caiji_all = document.getElementById('cb_caiji_all');
    for ( var i = 0; i < thisform.elements.length; i++)  
    {  
      var checkbox = thisform.elements[i];
      if (checkbox.name === "caiji[]" && checkbox.type === "checkbox" && cb_caiji_all.checked === false)  
        checkbox.checked = false;
      else if (checkbox.name === "caiji[]" && checkbox.type === "checkbox" && cb_caiji_all.checked === true)  
        checkbox.checked = true;  
    }  
  }  