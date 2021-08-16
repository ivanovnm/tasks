<?php

class cities extends c_pdo
{
	public function __default()
	{
		/*$this->query("insert into `aa_districts` (`id`, `parent_id`, `name`) values ('1', '1', 'Дзержинский район')");
		$this->query("insert into `aa_districts` (`id`, `parent_id`, `name`) values ('2', '1', 'Индустриальный район')");
		$this->query("insert into `aa_districts` (`id`, `parent_id`, `name`) values ('3', '1', 'Кировский район')");
		$this->query("insert into `aa_districts` (`id`, `parent_id`, `name`) values ('4', '1', 'Ленинский район')");
		$this->query("insert into `aa_districts` (`id`, `parent_id`, `name`) values ('5', '1', 'Мотовилихинский район')");
		$this->query("insert into `aa_districts` (`id`, `parent_id`, `name`) values ('6', '1', 'Орджоникидзевский район')");
		$this->query("insert into `aa_districts` (`id`, `parent_id`, `name`) values ('7', '1', 'Свердловский район')");*/
		
		$c = $this->query("select * from `aa_cities` order by `city` asc");
		//print_r($c);
		$d = $this->query("select * from `aa_districts` order by `name` asc");
		//print_r($d);
		
		$city = "<select name=\"city\" id=\"city\" onchange=\"changeCity()\"><option value=\"0\">Выберите город</option>";
		for ($i=0;$i<count($c);$i++)
		{
			$city .= "<option value=\"".$c[$i]["id"]."\">".$c[$i]["city"]."</option>";
		}		
		$city .= "</select>";
		
		$district = "<select name=\"district\" id=\"district\" disabled=\"true\"><option value=\"0\">Выберите район</option>";		
		for ($i=0;$i<count($d);$i++)
		{
			$district .= "<option value=\"".$d[$i]["id"]."\">".$d[$i]["name"]."</option>";
		}		
		$district .= "</select>";
		
		return $city.$district;
	}
}