<?php

class templatemgr_view extends c_view
{
	public function __default($data="")
	{
		return "simplemodule default method";
	}
	
	//строит список с шаблонами
	public function showTemplates($data = "")
	{
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УПРАВЛЕНИЕ НАБЛОНАМИ</h1></div>
		";
		
		if (!is_array($data))
		{
			$html .= "<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>нет шаблонов</div></div>";
		}
		else
		{
			$html .= "
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; text-align: left;'>
			<table border='0' cellspacing='5px' cellpadding='5px' style='width: 100%' class='table'>
					<tr>
						<td style='padding: 5px; background-color: silver'>
							фаил
						</td>
						<td style='padding: 5px; background-color: silver'>
							название
						</td>
						<td style='padding: 5px; background-color: silver'>
							описание
						</td>
						<td style='padding: 5px; background-color: silver'>
							действие
						</td>
					</tr>
				" ;
			for ($i=0;$i<count($data);$i++)
			{
				$html .= "<tr>
					<td>
						/template_".$data[$i]["id"].".html
					</td>
					<td>
						".$data[$i]["menu_name"]."
					</td>
					<td>
						".$data[$i]["description"]."
					</td>
					<td>
						<a href='?id=".$data[$i]["id"]."&action=edit'><img src='/images/icon-edit.png' alt='редактировать' title='редактировать' border='0'></a>
						<a href='?id=".$data[$i]["id"]."&action=delete'><img src='/images/icon-delete.png' alt='удалить' title='удалить' border='0'></a>
					</td>
				</tr>";
			}
			$html .= "</table><a href='?action=add'>+ добавить шаблон</a></div></div>";
		}
		
		return $html;
	}
	
	//строит форму редактирования шаблона
	public function editTemplate($data="")
	{
		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>РЕДАКТИРОВАНИЕ ШАБЛОНА</h1></div>
		";
		
		$html .= "
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; text-align: left;'>
			<form name='editTemplate' action='' method='post'>
			<table border='0' cellspacing='5px' cellpadding='5px' style='width: 100%' class='table'>
				<tr>
					<td width='30%'>
						Название в меню:
					</td>
					<td>
						<input type='edit' class='form-control' name='menu_name' value='".$data["menu_name"]."'/><span class='description'>Обязательное для заполнения поле</span>
					</td>
				</tr>
				<tr>
					<td width='30%'>
						Простое название:
					</td>
					<td>
						<input type='edit' class='form-control'  name='name' value='".$data["name"]."'/>
					</td>
				</tr>
				<tr>
					<td width='30%'>
						Описание:
					</td>
					<td>
						<input type='edit' class='form-control' name='description' value='".$data["description"]."'/>
					</td>
				</tr>
				<tr>
					<td width='30%'>
						Содержание:
					</td>
					<td>
						<textarea name='content'  class='form-control'style='width: 100%; height: 400px'>".$data["content"]."</textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='text-align: center'>
						<input type='submit' value='СОХРАНИТЬ'> <a href='?'>отмена</a>
					</td>
				</tr>
			</table>
			</form></div></div>
		";
		
		return $html;
	}
	
	//форма добавления шаблона
	public function addTemplate($data="")
	{

		$html = "
			<div class='row'><div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>ДОБАВЛЕНИЕ ШАБЛОНА</h1></div>
		";
		
		$html .= "<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px; text-align: left;'>
			<form name='addTemplate' action='' method='post'>
			<table border='0' cellspacing='5px' cellpadding='5px' style='width: 100%' class='table'>
				<tr>
					<td>
						Название в меню:
					</td>
					<td>
						<input type='edit' class='form-control' name='menu_name' value='".$data["menu_name"]."'/><span class='description'>Обязательное для заполнения поле</span>
					</td>
				</tr>
				<tr>
					<td >
						Простое название:
					</td>
					<td>
						<input type='edit' class='form-control' name='name' value='".$data["name"]."'/>
					</td>
				</tr>
				<tr>
					<td>
						Описание:
					</td>
					<td>
						<input type='edit' class='form-control' name='description' value='".$data["description"]."'/>
					</td>
				</tr>
				<tr>
					<td >
						Содержание:
					</td>
					<td>
						<textarea class='form-control' name='content' style='width: 100%; height: 400px'>".$data["content"]."</textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='text-align: center'>
						<input type='submit' value='СОХРАНИТЬ'> <a href='?'>отмена</a>
					</td>
				</tr>
			</table>
			</form>
			</div>
			</div>
		";
		
		return $html;
	}
	
	//форма подтверждения удаления шаблона
	public function deleteTemplate($id)
	{
		$html = "
			<div class='row'>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'><h1>УДАЛЕНИЕ ШАБЛОНА</h1></div>
			<div class='col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'  style='margin-top: 20px;'>Вы действительно хотите удалить шаблон? <a href='?id=".$id."&action=delete&accept'>Да!</a> <a href='?'>Нет, передумал ...</a> </div>
			</div>
		";
		
		return $html ;
	}
	
}