function showMenu(ids)
{
	alert(ids);
	$("#span_"+ids).css ("display", "block");
}

function changeForm(who)
{
	if (who == "module")
	{
		$("#if_module").css("display", "block");
		$("#if_helper").css("display", "none");
		$("#if_text").css("display", "none");
	}
	if (who == "helper")
	{
		$("#if_module").css("display", "none");
		$("#if_helper").css("display", "block");
		$("#if_text").css("display", "none");
	}
	if (who == "text")
	{
		$("#if_module").css("display", "none");
		$("#if_helper").css("display", "none");
		$("#if_text").css("display", "block");
	}
}

function showSubmenu()
{
	$("#submenu").css("display", "block");
	return false;
}

var img_num = 1;

function addImageField()
{
	img_num++;
	$("#images").append ("Краткое название: <input name='image_name_"+img_num+"' id='image_name_"+img_num+"' type='edit' value=''/><br/>Краткое описание: <input name='image_description_"+img_num+"' id='image_description_"+img_num+"' type='edit' value=''/><br/><input type='file' name='image_"+img_num+"' id='image_"+img_num+"' value=''/><hr/>") ;
	return false;
}

function verifyTel()
{
	tel = $("#tel").val();
	if (tel == "")
	{
		return false;
	}
	
	if (tel.length != 10)
	{
		return false;
	}
	
	ch = '0123456789' ;
	for (i=0;i<tel.length;i++)
	{
		if (ch.indexOf(tel.charAt(i)) < 0)
		{
			return false;
		}
	}
	
	return true;
}

function verifyRegisterForm()
{
	if (verifyTel() == false)
	{
		$("#error_field").html("Номер телефона указан не правильно, либо содержит недопустимые символы. Номер телефона вводится без знаков тире или пробелов, а так же без скобок в коде города.") ;
		return false;
	}
	
	
	$("#error_field").html("Проверка данных ...") ;
	return true ;
}

function checkStatus(num)
{
	if (num == '0')
	{
		$("#status_0").css ("display", "block") ;
		$("#status_1").css ("display", "none") ;
		$("#status_2").css ("display", "none") ;
	}
	
	if (num == '1')
	{
		$("#status_0").css ("display", "none") ;
		$("#status_1").css ("display", "block") ;
		$("#status_2").css ("display", "none") ;
	}
	
	if (num == '2')
	{
		$("#status_0").css ("display", "none") ;
		$("#status_1").css ("display", "none") ;
		$("#status_2").css ("display", "block") ;
	}
}

function delete_contact()
{
	$("#delete").val("1");
	frm = $("#add_contact_form").submit();
}

function showProfile(pname)
{
	if ($("#"+pname).css("display") == "none")
	{
		$("#"+pname).css("display", "block");
	}
	else
	{
		$("#"+pname).css("display", "none");
	}
}