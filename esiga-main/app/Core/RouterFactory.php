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

          
		$router[] = new Route('cz', function() {
			$httpResponse = new Nette\Http\Response;
			$httpResponse->redirect('https://e-siga.cz', 301); // 301 = permanent redirect
			exit;
		});

		$router[] = new Route('cz/eshop', function() {
			$httpResponse = new Nette\Http\Response;
			$httpResponse->redirect('https://e-siga.cz/products', 301); // 301 = permanent redirect
			exit;
		});
		
		$router[] = new Route('cz/eshop/tag/5', function() {
			$httpResponse = new Nette\Http\Response;
			$httpResponse->redirect('https://e-siga.cz/products', 301); // 301 = permanent redirect
			exit;
		});
             
		$router->addRoute('produkty/<category>', 'Products:default');

		$router->addRoute('produkty/<category>/<productCode>', 'Products:detail');

		
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
