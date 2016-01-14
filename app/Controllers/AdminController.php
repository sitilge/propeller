<?php

namespace App\Controllers;

use Abimo\Factory;
use App\Models\UrlModel;

class AdminController
{
	public $table;

	public $action;

	public $id;

	public function __construct()
	{
		$this->factory = new Factory();
	}

	public function main($table = null, $action = null, $id = null)
	{
		$this->table = $table;
		$this->action = $action;
		$this->id = $id;

        $adminModel = new \App\Models\AdminModel($this);
		$adminModel->getConfig();

		if (null === $table) {
			$content = '';
		} else {
			if (!empty($_POST)) {
				if (null === $action) {
					$adminModel->setOrder();
				} else {
					$adminModel->setData();
				}
			}

			$adminModel->getData();
			$adminModel->getPlugins();

			$content = $adminModel->getContent();
		}

        echo $this->factory->template()
			->file(__DIR__.'/../Views/Admin/Template')
			->set('router', new UrlModel())
			->set('menu', $adminModel->getMenu())
			->set('segment', $this->factory->request()->segment(null, 2))
			->set('content', $content)
			->render();
	}

	public function logout()
	{
		echo "Logout functionality";
	}
}