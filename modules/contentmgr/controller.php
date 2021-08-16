<?php

class contentmgr extends c_controller
{
	public function title()
	{
		return "title method";
	}
	
	public function __default($data="")
	{
		//print_r($_GET);
		//print_r($_POST);
		//exit();
		
		$data = $this->model->__default() ;	
		$view = $this->view->__default($data) ;
		
		if (isset($_GET["id"])) //если выбрана структура
		{
			if (isset($_GET["action"]))
			{
				if ($_GET["action"] == "add") //добавление блока
				{
					$info = $this->model->getStructBlockInfo($_GET["id"], $_GET["block"]) ;
					
					if (isset($_POST) && count($_POST)>0) //если есть пост данные - сохранимся
					{
						switch($_POST["type"])
						{
							case "module":
									if ($_POST["module_name"] == "")
									{
										$info["error"] = "Не выбран модуль!";
									}
									else
									{
										$this->model->saveBlock($_GET["id"], $_GET["block"], $_POST["type"], $_POST["module_name"], $_POST["module_params"]) ;
										header ("Location: ?id=".$_GET["id"]);
										exit();
									}
								break;
							case "helper":
									if ($_POST["helper_name"] == "")
									{
										$info["error"] = "Не выбран хэлпер!";
									}
									else
									{
										$this->model->saveBlock($_GET["id"], $_GET["block"], $_POST["type"], $_POST["helper_name"], $_POST["helper_params"]) ;
										header ("Location: ?id=".$_GET["id"]);
										exit();
									}
								break;
							case "text":
									$this->model->saveBlock($_GET["id"], $_GET["block"], $_POST["type"], $_POST["content"], "") ;
									header ("Location: ?id=".$_GET["id"]);
									exit();
								break;									
						}
					}
					else
					{
						$info["error"] = "";
					}
					
					
					$view = $this->view->addToBlock($info);
					return $view ;
				}
				elseif ($_GET["action"] == "delete") //удаление блока
				{
					$this->model->deleteContentBlock($_GET["block"]);
					header ("Location: ?id=".$_GET["id"]);
					exit();
				}
				elseif($_GET["action"] == "up")
				{
					$this->model->upContentBlock($_GET["block"]);
					header ("Location: ?id=".$_GET["id"]);
					exit();
				}
				elseif($_GET["action"] == "down")
				{
					$this->model->downContentBlock($_GET["block"]);
					header ("Location: ?id=".$_GET["id"]);
					exit();
				}
				elseif($_GET["action"] == "edit")
				{
					$block = $this->model->getContentBlock($_GET["block"]);
					//print_r($block);
					
					if (isset($_POST["name"]) || isset($_POST["content"])) //сохранимся
					{
						$this->model->editContentBlock($block, $_POST);
						header ("Location: ?id=".$_GET["id"]);
						exit();
					}
					
					$view = $this->view->editContentBlock($block);
					return $view ;
				}

			}
			
			$blocks = $this->model->getBlocks($_GET["id"]);
			$view .= $this->view->blocks($blocks);
		}
		
		return $view ;
	}
}