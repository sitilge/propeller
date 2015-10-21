<?php

namespace App\Controllers;

class AdminController extends \Abimo\Services
{	
	public function main($table = null, $action = null, $rowId = null)
	{
		$router = $this->service('router');
        $content = $this->service('template')
                ->set('router', $router);
        
        $adminModel = new \App\Models\adminModel();
        
        $adminModel->getConfig();
        
        $menu = $adminModel->getMenu();
        
        if (null !== $table) {
            $content = $adminModel->getContent($table, $action, $rowId);
        }
                
        return $this->service('template')
				->file('Admin/Template')
				->set('router', $router)
				->set('menu', $menu)
				->set('content', $content->render())
				->render();
        
//		$menu = array(
//			'banners' => array('name' => 'Banners', 'name_clean' => 'banners', 'table' => 'banners'),
//			'slider_home' => array('name' => 'Home Slider', 'name_clean' => 'slider_home', 'table' => 'slider_home'),
//			'menu' => array('name' => 'Menu', 'name_clean' => 'menu', 'table' => 'menu'),
//			'offers' => array('name' => 'Offers', 'name_clean' => 'offers', 'table' => 'offers'),
//			'pages' => array('name' => 'Pages', 'name_clean' => 'pages', 'table' => 'pages'),
//			'services' => array('name' => 'Services', 'name_clean' => 'services', 'table' => 'services'),
//			'shops' => array('name' => 'Shops', 'name_clean' => 'shops', 'table' => 'shops'),
//		);
//		
//		$content = $content
////				->set('menu', $menu)
//				->set('router', $router)
//				->render();
//		var_dump($content);exit;
		
	}
}