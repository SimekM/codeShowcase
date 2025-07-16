<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$currentDomain = $_SERVER['HTTP_HOST'];

		
		$router->addRoute('/<presenter>/<action>', [
			'presenter' => [
				Route::Value => 'Home',
				Route::FilterTable => ['produkty' => 'Products', 'kontakt' => 'Contact'],
			],
			'action' => 'default',
		]);

            
         

		return $router;
	}
    

}
