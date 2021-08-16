
function changeCity()
{
	
	if($("#city").val() == "1")
	{
		$("#district").removeAttr("disabled");
	}
	else
	{
		$("#district").attr("disabled", "");
	}
}

function showMenuSlider()
{
	$("#menu-slider-ex").css("display","none");
	
	if ($("#menu-slider").css("display") == "none")
	{
		$("#menu-slider").css("display", "block");
	}
	else
	{
		$("#menu-slider").css("display", "none");
	}
	
	return false;
}

function showMenuSliderEx()
{
	$("#menu-slider").css("display","none");
	
	if ($("#menu-slider-ex").css("display") == "none")
	{
		$("#menu-slider-ex").css("display", "block");
	}
	else
	{
		$("#menu-slider-ex").css("display", "none");
	}
	
	return false;
}

function checkType(n)
{
	$("#ch1").css("color", "#36b8ff");
	$("#ch2").css("color", "#36b8ff");
	$("#ch3").css("color", "#36b8ff");
	$("#ch4").css("color", "#36b8ff");
	$("#ch5").css("color", "#36b8ff");
	$("#ch6").css("color", "#36b8ff");
	$("#ch7").css("color", "#36b8ff");
	$("#ch8").css("color", "#36b8ff");
	
	//alert($("#ch"+n).css("color"));
	
	if($("#ch"+n).css("color") == "rgb(54, 184, 255)")
	{
		$("#ch"+n).css("color", "#f27063");
		$("#type").val(n);
	}
	else
	{
		$("#ch"+n).css("color", "#36b8ff");
	}
}

function showBtn(n)
{
	$("#"+n).css("display","block");
}

function hideBtn(n)
{
	$("#"+n).css("display","none");
}

function masketInput(mask, ch)
{
	alert(ch.value.length);
}

function validateAddAdw()
{
	if ($("#type").val() == "0" || $("#type").val() == "")
	{
		alert("Не выбрана категория!");
		return false;
	}
	
	if ($("#name").val() == "")
	{
		alert("Не указано имя лота (заявки)!");
		return false;
	}
	
	if ($("#description").val() == "")
	{
		alert("Не указан список требуемых товаров или услуг!");
		return false;
	}
	
	if ($("#time").val() == "")
	{
		alert("Укажите время требуемое на заключение договора!");
		return false;
	}
	
	if ($("#time_wait").val() == "")
	{
		alert("Укажите время ожидания ответа от подрядчика в днях!");
		return false;
	}
	
	if ($("#city").val() == "0" || $("#city").val() == "")
	{
		alert("Выберите город!");
		return false;
	}
	
	if ($("#address").val() == "")
	{
		alert("Укажите адрес!");
		return false;
	}
	
	if ($("#max_price").val() == "" || $("#max_price").val() == "0")
	{
		alert("Укажите максимальную цену лота!");
		return false;
	}
	
	if ($("#min_price").val() == "" || $("#min_price").val() == "0")
	{
		alert("Укажите максимальный шаг лота!");
		return false;
	}
	
	if ( Math.round($("#min_price").val()) > Math.round($("#max_price").val()) )
	{
		alert("Шаг лота не должен превышать максимальную стоимость! "+Math.round($("#min_price").val())+" > "+Math.round($("#max_price").val()));
		return false;
	}
	
	if ($("#day_start").val() == 0 || $("#month_start").val()  == 0 || $("#year_start").val()  == 0 || 
		$("#day_stop").val() == 0 || $("#month_stop").val() == 0 || $("#year_stop").val() == 0 || 
		$("#day_itog").val() == 0 || $("#month_itog").val() == 0 || $("#year_itog").val() == 0
		)
	{
		alert("В датах: начала или окончания или подведения итогов есть ошибки. Исправьте!");
		return false;
	}
		
	date_start = new Date($("#year_start").val(), $("#month_start").val(), $("#day_start").val(), $("#hour_start").val(), $("#minute_start").val(), 0,0);
	date_stop = new Date($("#year_stop").val(), $("#month_stop").val(), $("#day_stop").val(), $("#hour_stop").val(), $("#minute_stop").val(), 0,0);
	date_itog = new Date($("#year_itog").val(), $("#month_itog").val(), $("#day_itog").val(), $("#hour_itog").val(), $("#minute_itog").val(), 0,0);
	
	if (date_start.getTime() >= date_stop.getTime())
	{
		alert("Дата начала приема ставок не должна быть меньше или равняться дате окончания приема сставок!");
		return false;
	}
	
	if (date_stop.getTime() >= date_itog.getTime())
	{
		alert("Дата подведения итогов должна быть больше даты окончания примема ставок хотбы на пару минут!");
		return false;
	}
	
	return true;
}

function changeMin()
{
	$("#min_price").val(Math.round($("#max_price").val()/100*0.5));
}

function setShag(n)
{
	///alert(n);
	
	$("#shagVal").html(n);
	$("#new_price").val($("#current_price").val()-n);
	$("#new_price_visual").html($("#current_price").val()-n);
	$("#summ").val($("#current_price").val()-n);
	
}

function checkRate()
{
	$("#rate_form").submit();
}

function closeRate()
{
	var cr = confirm("Вы действительно хотите досрочно закрыть лот?");
	return cr;
}

function showTable(t)
{
	
	if (t == 2)
	{
		$("#table_2").css("display", "block");
		$("#table_1").css("display", "none");
		
		$("#customer").css("background-color", "#36b8ff");
		$("#customer").css("color", "#fff");
		$("#customer").css("cursor", "pointer");
		
		$("#supplier").css("background-color", "#f4f4f4");
		$("#supplier").css("color", "#464646");
		$("#supplier").css("cursor", "normal");
	}
	else
	{
		$("#table_1").css("display", "block");
		$("#table_2").css("display", "none");
		
		$("#supplier").css("background-color", "#36b8ff");
		$("#supplier").css("color", "#fff");
		$("#supplier").css("cursor", "pointer");
		
		$("#customer").css("background-color", "#f4f4f4");
		$("#customer").css("color", "#464646");
		$("#customer").css("cursor", "normal");
	}
	
	return false;
}

function sendMessage(sid)
{
	if (event.keyCode == 13)
	{
		$("sender").click();
		refresh(sid, $('#msg').val());
	}
}