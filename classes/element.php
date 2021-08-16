<?php

class c_element
{
	public $uin = null;
	public $name = null;
	public $type = null;
	public $group = null;
	public $order = null;
	public $visible = null;
	public $system = null;
	public $required = null;
	public $value = null ;
	
	function __construct($uin, $name, $type, $group, $order, $visible, $system, $required, $value)
	{
		$this->uin = $uin;
		$this->name = $name;
		$this->type = $type;
		$this->group = $group;
		$this->order = $order;
		$this->visible = $visible;
		$this->system = $system;
		$this->required = $required;
		$this->value = $value ;
	}
	
	public function _show()
	{
		return $this->name." : ".$this->value ;
	}
	
	public function _edit()
	{
		return $this->name." : <inout type=\"edit\" id=".$this->uin."\" name=\"".$this->uin."\"    value=\"".$this->value."\"/>" ;
	}
}