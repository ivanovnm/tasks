<?php

class templatemgr extends c_controller
{
	public function __default()
	{
		if (isset($_GET["action"]))
		{
			if ($_GET["action"] == "edit") //редактирование шаблона
			{
				if (isset($_POST) && count($_POST)>1) //сохранимся
				{
					if ($_POST["menu_name"] == "") //ошибочка
					{
						$view = $this->view->editTemplate($_POST) ;
						return $view;
					}
					
					$data = $_POST ;
					$data["id"] = $_GET["id"] ;
					
					$this->model->updateTemplate($data) ;
					header ("Location: ?");
					exit();
				}
				
				$data = $this->model->getTemplate($_GET["id"]) ;
				if (!is_array($data))
				{
					header("Location: ?");
					exit();
				}
				$view = $this->view->editTemplate($data) ;
				return $view ;
			}
			elseif($_GET["action"] == "add")
			{
				if (isset($_POST) && count($_POST)>0)
				{
					if ($_POST["menu_name"] == "")
					{
						return $this->view->addTemplate($_POST);
					}
					
					$this->model->insertTemplate($_POST);
					header("Location: ?");
					exit();
				}
				$data = array("menu_name"=>"", "name"=>"", "description"=>"", "content"=>"") ;
				return $this->view->addTemplate($data);
			}
			elseif ($_GET["action"] == "delete")
			{
				if (isset($_GET["accept"]))
				{
					$this->model->deleteTemplate($_GET["id"]);
					header("Location: ?");
					exit();
				}
				
				return $this->view->deleteTemplate($_GET["id"]) ;
			}
		}
		
		$data = $this->model->loadTemplates();
		$view = $this->view->showTemplates($data) ;
		return $view;
	}
}