<?php

class contentmgr_view extends c_view
{
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
	
	//
	public function __default($data="")
	{
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ СОДЕРЖИМЫМ САЙТА</h1></div></div>
			<form action='' method='get' name='struct_select'>
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; margin-bottom: 20px; text-align: left;'>
			<table class='table' style='width: 100%;'>
				<tr>
					<td>
						Выберите страницу для редактирования: 
					</td><td>
			<select name='id' class='form-control'>
			";
		if (isset($_GET["id"]))
		{
			$struct = $_GET["id"] ;
		}
		else
		{
			$struct = "0" ;
		}
		$html .= $this->drawChildsEx($data, "-", $struct);
		$html .="
			</select> 
			</td></tr>
			<tr><td></td><td><input type='submit'value='  ЗАГРУЗИТЬ  '/></td></tr>
			</table>
			
			</form>
			</div>
		";
		
		return $html;
	}
	
	//отрисуем блоки
	public function blocks ($data="")
	{
		if ($data == "")
		{
			return "";
		}
		
		$html = "
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; margin-bottom: 20px; text-align: left;'>
			<table border='0' cellspacing='5px' cellpadding='5px' width='100%' class='table'>
			<tr>
				<td style='background-color: silver; padding: 3px;'>
					Название блока (Описание блока)
				</td>
				<td style='background-color: silver; padding: 3px;'>
					Содержимое блока (тип содержимого)
				</td>
			</tr>
			";
		
		//print_r($data);
		
		if (count($data["blocks"])==0)
		{
			$html .="
				<tr>
					<td colspan='2'>
						Нет блоков в шаблоне!
					</td>
				</tr>
			";
		}
		else
		{
			for ($i=0;$i<count($data["blocks"]);$i++)
			{	
				
				$html .= "
					<tr>
						<td style='border-bottom: 1px solid silver; width: 40%'>
							".$data["blocks"][$i]["name"]." (".$data["blocks"][$i]["description"].")
						</td>
						<td style='border-bottom: 1px solid silver;  width: 60%'>";
				if (count($data["blocks"][$i]["content"]) == 0)
				{
					$html .= "пусто<br/>" ;
				}
				else
				{
					for ($j=0;$j<count($data["blocks"][$i]["content"]);$j++)
					{
						if ($data["blocks"][$i]["content"][$j]["type"] == "text")
						{
							$html .= substr(htmlspecialchars($data["blocks"][$i]["content"][$j]["content"]),0,128)."...
							<a href='?id=".$data["struct"]["id"]."&action=edit&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-edit.png' alt='редактировать текст' title='редактировать текст' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=delete&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-delete.png' alt='удалить' title='удалить' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=up&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-up.png' alt='вверх' title='вверх' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=down&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-down.png' alt='вниз' title='вниз' border='0'></a>
							<br/>";
						}
						elseif ($data["blocks"][$i]["content"][$j]["type"] == "module")
						{
							$html .=$data["blocks"][$i]["content"][$j]["content"]."(".$data["blocks"][$i]["content"][$j]["type"].")
							<a href='?id=".$data["struct"]["id"]."&action=edit&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-edit.png' alt='редактировать' title='редактировать' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=delete&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-delete.png' alt='удалить' title='удалить' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=up&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-up.png' alt='вверх' title='вверх' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=down&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-down.png' alt='вниз' title='вниз' border='0'></a>
							<br/>";
						}
						elseif ($data["blocks"][$i]["content"][$j]["type"] == "helper")
						{
							$html .=$data["blocks"][$i]["content"][$j]["content"]."(".$data["blocks"][$i]["content"][$j]["type"].")
							<a href='?id=".$data["struct"]["id"]."&action=edit&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-edit.png' alt='редактировать' title='редактировать' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=delete&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-delete.png' alt='удалить' title='удалить' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=up&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-up.png' alt='вверх' title='вверх' border='0'></a>
							<a href='?id=".$data["struct"]["id"]."&action=down&block=".$data["blocks"][$i]["content"][$j]["id"]."'><img src='/images/icon-down.png' alt='вниз' title='вниз' border='0'></a>
							<br/>";
						}
					}
				}
				
				$html .="<a href='?id=".$data["struct"]["id"]."&block=".$data["blocks"][$i]["name"]."&action=add' style='text-decoration: none'>+ добавить содержимое</a></td>
					</tr>
					";
			}
			
		}
		
		$html .="</table></div>";
		
		return $html;
	}
	
	public function addToBlock($data = "")
	{
		$modules = "";
		for ($i=0;$i<count($data["modules"]);$i++)
		{
			if ($i==0)
			{
				$modules .= "<option value='".$data["modules"][$i]."' SELECTED>".$data["modules"][$i]."</option>" ;
			}
			else
			{
				$modules .= "<option value='".$data["modules"][$i]."'>".$data["modules"][$i]."</option>" ;
			}
		}

		$helpers = "";
		for ($i=0;$i<count($data["helpers"]);$i++)
		{
			if ($i==0)
			{
				$helpers .= "<option value='".$data["helpers"][$i]."' SELECTED>".$data["helpers"][$i]."</option>" ;
			}
			else
			{
				$helpers .= "<option value='".$data["helpers"][$i]."'>".$data["helpers"][$i]."</option>" ;
			}
		}
		
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ СОДЕРЖИМЫМ САЙТА</h1></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>
				Добавление содержимого на страницу - &laquo;<b>".$data["struct"]["menu_name"]."</b>&raquo; в блок контента - &laquo;<b>".$data["block"]."</b>
			</div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>
				<font color='red'>{error}</font>
			</div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; text-align: left;'>
			<form name='blockform' action='?id=".$data["struct"]["id"]."&block=".$data["block"]."&action=add' method='post'>
			<table border='0' cellspacing='5px' cellpadding='5px' width='100%' class='table'>
				<tr>
					<td >
						Тип добавляемого контента: 
					</td>
					<td>
						<select name='type' onchange='changeForm(this.value)' class='form-control'>
						<option value='module' SELECTED>Модуль</option>
						<option value='helper'>Хэлпер</option>
						<option value='text'>Текст</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<div id='if_module'>
							<table border='0' cellspacing='0' cellpadding='5px' style='width: 100%'>
							<tr>
							<td width='30%'>Название модуля:</td>
							<td><select name='module_name' class='form-control'>".$modules."</select></td>
							</tr>
							<tr>
							<td width='30%'>Дополнительные параметры:</td>
							<td><input type='edit' class='form-control' name='module_params' value=''/></td>
							</tr>
							</table>
						</div>
						<div id='if_helper' style='display: none'>
							<table border='0' cellspacing='0' cellpadding='5px' style='width: 100%'>
							<tr>
							<td >Название хэлпера:</td>
							<td><select class='form-control' name='helper_name'>".$helpers."</select></td>
							</tr>
							<tr>
							<td>Дополнительные параметры:</td>
							<td><input type='edit' class='form-control' name='helper_params' value=''/></td>
							</tr>
							</table>
						</div>
						<div id='if_text' style='display: none'>
							<table border='0' cellspacing='0' cellpadding='5px' style='width: 100%'>
							<tr>
							<td >Содержание:</td>
							<td><textarea name='content' class='form-control' id='content'></textarea><script language='javascript'>$( 'textarea#content' ).ckeditor();</script></td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='text-align: center'>
						<input type='submit' value='  СОХРАНИТЬ  '/> <a href='?id=".$data["struct"]["id"]."'>отмена</a>
					</td>
				</tr>
			</table>
			</form>
			</div></div>
			";
		$html = str_replace ("{error}", $data["error"], $html)	;
			
		return $html;
	}
	
	//форма редактирования блока
	public function editContentBlock($data="")
	{
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ СОДЕРЖИМЫМ САЙТА</h1></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h3>Редактирование блока</h3></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>
			<form name='editblock' action='' method='post'>
			
			" ;
			
		if ($data["type"] == "text")
		{
			$html .= "
				<table border='0' cellspacing='0' cellpadding='5px' style='width: 100%' class='table'>
				<tr>
				<td >Содержание:</td>
				<td><textarea name='content'  class='form-control'id='content'>".$data["content"]."</textarea><script language='javascript'>$( 'textarea#content' ).ckeditor();</script></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align: center'>
						<input type='submit' value='  СОХРАНИТЬ  '/> <a href='?id=".$data["site_struct_id"]."'>отмена</a>
					</td>
				</tr>
				</table>
			";
		}
		elseif ($data["type"] == "module")
		{
			$content = $data["content"] ;
			$content = explode ("=", $content) ;
			if (count($content)>1)
			{
				$name = $content[0];
				$params = $content[1] ;
			}
			else
			{
				$name = $content[0];
				$params = "" ;
			}
			
			$html .="
			<table border='0' cellspacing='0' cellpadding='5px' style='width: 100%' class='table'>
			<tr>
			<td >Дополнительные параметры:</td>
			<td><input type='edit' class='form-control' name='params' value='".$params."'/><input type='hidden' name='name' value='".$content[0]."'/></td>
			</tr>
			<tr>
				<td colspan='2' style='text-align: center'>
					<input type='submit' value='  СОХРАНИТЬ  '/> <a href='?id=".$data["site_struct_id"]."'>отмена</a>
				</td>
			</tr>
			</table>
			";
		}
		elseif ($data["type"] == "helper")
		{
			$content = $data["content"] ;
			$content = explode ("=", $content) ;
			if (count($content)>1)
			{
				$name = $content[0];
				$params = $content[1] ;
			}
			else
			{
				$name = $content[0];
				$params = "" ;
			}
			
			$html .="
			<table border='0' cellspacing='0' cellpadding='5px' style='width: 100%' class='table'>
			<tr>
			<td>Дополнительные параметры:</td>
			<td><input type='edit' class='form-control' name='params' value='".$params."'/><input type='hidden' name='name' value='".$content[0]."'/></td>
			</tr>
			<tr>
				<td colspan='2' style='text-align: center'>
					<input type='submit' value='  СОХРАНИТЬ  '/> <a href='?id=".$data["site_struct_id"]."'>отмена</a>
				</td>
			</tr>
			</table>
			";
		}
		
		$html .="</form></div></div>";
		
		return $html;
	}
}