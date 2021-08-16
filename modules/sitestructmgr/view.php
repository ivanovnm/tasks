<?php

class sitestructmgr_view extends c_view
{
	//отрисовывает структуру сайта
	private function drawChilds($data)
	{
		if (!is_array($data))
		{
			return "" ;
		}
		
		$html = "" ;
		for ($i=0;$i<count($data);$i++)
		{
			$html .= "<div class='struct_block'><span alt='".$data[$i]["description"]."' title='".$data[$i]["description"]."'>".$data[$i]["menu_name"]."</span> (<font color='silver' size='2'>".$data[$i]["path"]."</font>)";
			$html .= "  <span id='span_".$data[$i]["id"]."' >
							<a href='?id=".$data[$i]["id"]."&action=new' ><img src='/images/icon-new.png' alt='Создание страницы' title='Создание страницы'></a>
							<a href='?id=".$data[$i]["id"]."&action=edit' ><img src='/images/icon-edit.png' alt='Редактирование страницы' title='Редактирование страницы'></a>
							<a href='?id=".$data[$i]["id"]."&action=delete' ><img src='/images/icon-delete.png' alt='Удаление страницы' title='Удаление страницы'></a>
							<a href='?id=".$data[$i]["id"]."&action=up' ><img src='/images/icon-up.png' alt='Поднять страницу на один уровень вверх' title='Поднять страницу на один уровень вверх'></a>
							<a href='?id=".$data[$i]["id"]."&action=down'><img src='/images/icon-down.png' alt='на один уровень вниз' title='на один уровень вниз'></a>
						</span>
						<span id='childs_".$data[$i]["id"]."'>
			";
			
			if (isset($data[$i]["childs"]) && count($data[$i]["childs"])>0)
			{
				$html .= $this->drawChilds($data[$i]["childs"]) ;
			}
			$html .= "</span></div>";
		}
		
		return $html ;
	}

	//строит список для select-а
	private function drawChildsEx($data, $deep="-", $selected="0")
	{
		if (!is_array($data))
		{
			return "" ;
		}
		
		$html = "" ;
		for ($i=0;$i<count($data);$i++)
		{
			if ($data[$i]["id"] == $selected)
			{
				$html .= "<option value='".$data[$i]["id"]."' selected>".$deep.$data[$i]["menu_name"]."</option>";
			}
			else
			{
				$html .= "<option value='".$data[$i]["id"]."'>".$deep.$data[$i]["menu_name"]."</option>";
			}
			
			if (isset($data[$i]["childs"]) && count($data[$i]["childs"])>0)
			{
				//print "tut";
				$html .= $this->drawChildsEx($data[$i]["childs"], $deep."-", $selected) ;
			}			
		}
		
		return $html ;
	}	
	
	public function __default($data = "")
	{
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ СТРУКТУРОЙ САЙТА</h1></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>".$this->drawChilds($data)."</div></div>";
		
		return $html ;
	}
	
	//создание новой страницы
	public function newPage($data = array(), $templates = array(), $parents=array())
	{
		
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ СТРУКТУРОЙ САЙТА</h1></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h3>Создание новойстраницы сайта</h3></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>{value:error}</div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; text-align: left'>
			<form name='newpage_form' action='?id={value:id}&action=new' method='post'>
				
				<table border='0' cellspacing='0' cellpadding='5' width='100%' class='table'>
					<tr>
						<td text-align: left'>
							Название:
						</td>
						<td>
							<input type='edit' class='form-control' name='name' value='{value:name}'/> <span class='description'> Введите краткое название страницы.</span>
						</td>
					</tr>
					<tr>
						<td>
							Название в меню:
						</td>
						<td>
							<input type='edit' class='form-control' name='menu_name' value='{value:menu_name}'/> <span class='description'> Укажите краткое название страницы, которое будет отображено в меню. <font color='red'>Обязательное для заполнения!</font></span>
						</td>
					</tr>
					<tr>
						<td>
							Описание:
						</td>
						<td>
							<input type='edit' class='form-control' name='description' value='{value:description}'/> <span class='description'> Укажите подробное описание страницы.</span>
						</td>
					</tr>
					<tr>
						<td>
							URL:
						</td>
						<td>
							<input type='edit' class='form-control' name='url' value='{value:url}'/> <span class='description'> Придумайте URL данной страницы. Внимание! URL может содержать буквы, цифры, знак тире и подчеркивание. <font color='red'>Обязательное для заполнения!</font></span>
						</td>
					</tr>
					<tr>
						<td>
							Страница родитель:
						</td>
						<td>
							<select name='parent_id' class='form-control'>";	
			$html .= $this->drawChildsEx($parents,"-",$data["parent_id"]) ;
							
			
			$html .= "</select> <span class='description'>Укажите родительскую страницу, к которой принадлежит данная страница</span>
						</td>
					</tr>
					<tr>
						<td>
							Шаблон отображения:
						</td>
						<td>
							<select name='template_id' class='form-control'>"; 
			if (is_array($templates))
			{
				for ($i=0;$i<count($templates);$i++)
				{
					if ($data["template_id"] == $templates[$i]["id"])
					{
						$html .= "<option value='".$templates[$i]["id"]."' selected>".$templates[$i]["menu_name"]."</option>" ;
					}
					else
					{
						$html .= "<option value='".$templates[$i]["id"]."'>".$templates[$i]["menu_name"]."</option>" ;
					}
				}
			}
			
			$html .="</select> <span class='description'>Укажите шаблон для отображения страницы</span>
						</td>
					</tr>
					<tr>
						<td>
							Системное:
						</td>
						<td>
							<select name='system' class='form-control'>";
			
			if ($data["system"] == "0")
			{
				$html .="
								<option value='0' SELECTED>Нет</option>
								<option value='1'>Да</option>
				";
			}
			else
			{
				$html .="
								<option value='0'>Нет</option>
								<option value='1' SELECTED>Да</option>
				";
			}
							
			$html .="		</select><span class='description'> Показывать страницу в меню?</span>
						</td>
					</tr>
					<tr>
						<td>
							Title:
						</td>
						<td>
							<input type='edit' class='form-control' name='tag_title' value='{value:tag_title}'/> <span class='description'>Укажите TITLE страницы</span>
						</td>
					</tr>
					<tr>
						<td>
							Keywords:
						</td>
						<td>
							<input type='edit' class='form-control' name='tag_keywords' value='{value:tag_keywords}'/> <span class='description'>Укажите KEYWORDS страницы</span>
						</td>
					</tr>
					<tr>
						<td>
							Description:
						</td>
						<td>
							<input type='edit' class='form-control' name='tag_description' value='{value:tag_description}'/> <span class='description'>Укажите DESCRIPTION страницы</span>
						</td>
					</tr>
					<tr>
						<td colspan='2' style='text-align: center'>
							<input type='submit' value='создать'/> <a href='?'>отмена</a>
						</td>
					</tr>
				</table>
			</form>
			</div></div>
			";
		
		$html = $this->assignValues($html, $data);
		
		return $html ;
	}
	
		//создание новой страницы
	public function editPage($data = array(), $templates = array(), $parents=array())
	{
		
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ СТРУКТУРОЙ САЙТА</h1></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h3>Создание новойстраницы сайта</h3></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>{value:error}</div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; text-align: left'>
			
			<form name='newpage_form' action='?id={value:id}&action=edit' method='post'>
			<table border='0' cellspacing='0' cellpadding='5' width='100%' class='table'>
				<tr>
					<td>
						Название:
					</td>
					<td>
						<input type='edit' name='name'  class='form-control' value='{value:name}'/> <span class='description'> Введите краткое название страницы.</span>
					</td>
				</tr>
				<tr>
					<td>
						Название в меню:
					</td>
					<td>
						<input type='edit' name='menu_name'  class='form-control' value='{value:menu_name}'/> <span class='description'> Укажите краткое название страницы, которое будет отображено в меню. <font color='red'>Обязательное для заполнения!</font></span>
					</td>
				</tr>
				<tr>
					<td>
						Описание:
					</td>
					<td>
						<input type='edit' name='description'  class='form-control' value='{value:description}'/> <span class='description'> Укажите подробное описание страницы.</span>
					</td>
				</tr>
				<tr>
					<td>
						URL:
					</td>
					<td>
						<input type='edit' name='url'  class='form-control' value='{value:url}'/> <span class='description'> Придумайте URL данной страницы. Внимание! URL может содержать буквы, цифры, знак тире и подчеркивание. <font color='red'>Обязательное для заполнения!</font></span>
					</td>
				</tr>
				<tr>
					<td>
						Страница родитель:
					</td>
					<td>
						<select name='parent_id'  class='form-control'>";
		if ($_GET["id"] == "1")
		{
			$html .= "<option value='0' SELECTED>Это главная страница</option>";
		}
		else
		{
			$html .= $this->drawChildsEx($parents,"-",$data["parent_id"]) ;
		}
						
		
		$html .= "</select> <span class='description'>Укажите родительскую страницу, к которой принадлежит данная страница</span>
					</td>
				</tr>
				<tr>
					<td>
						Шаблон отображения:
					</td>
					<td>
						<select name='template_id'  class='form-control'>"; 
		if (is_array($templates))
		{
			for ($i=0;$i<count($templates);$i++)
			{
				if ($data["template_id"] == $templates[$i]["id"])
				{
					$html .= "<option value='".$templates[$i]["id"]."' selected>".$templates[$i]["menu_name"]."</option>" ;
				}
				else
				{
					$html .= "<option value='".$templates[$i]["id"]."'>".$templates[$i]["menu_name"]."</option>" ;
				}
			}
		}
		
		$html .="</select> <span class='description'>Укажите шаблон для отображения страницы</span>
					</td>
				</tr>
				<tr>
					<td>
						Системное:
					</td>
					<td>
						<select name='system'  class='form-control'>";
		
		if ($data["system"] == "0")
		{
			$html .="
							<option value='0' SELECTED>Нет</option>
							<option value='1'>Да</option>
			";
		}
		else
		{
			$html .="
							<option value='0'>Нет</option>
							<option value='1' SELECTED>Да</option>
			";
		}
						
		$html .="		</select><span class='description'> Показывать страницу в меню?</span>
					</td>
				</tr>
				<tr>
					<td>
						Title:
					</td>
					<td>
						<input type='edit'  class='form-control' name='tag_title' value='{value:tag_title}'/> <span class='description'>Укажите TITLE страницы</span>
					</td>
				</tr>
				<tr>
					<td>
						Keywords:
					</td>
					<td>
						<input type='edit'  class='form-control' name='tag_keywords' value='{value:tag_keywords}'/> <span class='description'>Укажите KEYWORDS страницы</span>
					</td>
				</tr>
				<tr>
					<td>
						Description:
					</td>
					<td>
						<input type='edit'  class='form-control' name='tag_description' value='{value:tag_description}'/> <span class='description'>Укажите DESCRIPTION страницы</span>
					</td>
				</tr>
				<tr>
					<td>
						Параметры GET:
					</td>
					<td>
						<input type='edit'  class='form-control' name='get_param_1' value='{value:get_param_1}'/><br/>
						<input type='edit'  class='form-control' name='get_param_2' value='{value:get_param_2}'/><br/>
						<input type='edit'  class='form-control' name='get_param_3' value='{value:get_param_3}'/><br/>
						<input type='edit'  class='form-control' name='get_param_4' value='{value:get_param_4}'/><br/>
						<input type='edit'  class='form-control' name='get_param_5' value='{value:get_param_5}'/>
					</td>
				</tr>			
				<tr>
					<td colspan='2' style='text-align: center'>
						<input type='submit' value='сохранить'/> <a href='?'>отмена</a>
					</td>
				</tr>
			</table>
			</form></div></div>
			";
		
		$data["get_param_1"] = "";
		$data["get_param_2"] = "";
		$data["get_param_3"] = "";
		$data["get_param_4"] = "";
		$data["get_param_5"] = "";
		
		if (isset($data["params"]) && is_array($data["params"]) && count($data["params"])>0)
		{
			for ($i=0;$i<count($data["params"]);$i++)
			{
				$data["get_param_".($i+1)] = $data["params"][$i]["name"] ;
			}
		}
		
		unset($data["params"]) ;
		
		$html = $this->assignValues($html, $data);
		
		return $html ;
	}
	
	public function confirmDelete($data="")
	{
		$html = "
			
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ СТРУКТУРОЙ САЙТА</h1></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>Удалить раздел со всеми его подразделами?</b> <a href='?id=".$data["id"]."&action=delete&accept'><font color='red'>Да!</font></a> <a href='?'>Нет, передумал.</a></div></div>
		";
		
		$html = $this->assignValues($html, $data);
		
		return $html;
	}
}