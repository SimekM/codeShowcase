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
		$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
		/* $router->addRoute('/<presenter>/<action>', [
			'presenter' => [
				Route::Value => 'Home',
				Route::FilterTable => [
					"o-nas" => "About",
					"aktuality" => "News",
					"realizace" => "Realization",
					"produkty" => "Products"
				],
			],
			'action' => [
				Route::Value => 'default',
				Route::FilterTable => [
					'dvere' => 'door',
					'skrine' => 'wardrope',
					'kuchyne' => 'kitchen',
					'ostatni' => 'other'
				],
			],
		]); */
		return $router;
	}

}
