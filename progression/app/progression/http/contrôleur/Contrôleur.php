<?php
/*
	This file is part of Progression.

	Progression is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Progression is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;

class Contrôleur extends BaseController
{
	protected function réponse_json($réponse, $code)
	{
		return response()->json(
			$réponse,
			$code,
			[
				"Content-Type" => "application/vnd.api+json",
				"Charset" => "utf-8",
			],
			JSON_UNESCAPED_UNICODE,
		);
	}

	protected function getFractalManager()
	{
		$request = app(Request::class);
		$manager = new Manager();

		// On redéfinit le Serializer pour avoir des liens «relationship» personnalisés

		//JsonApiSerializer ajoute un slash à l'URL de base, on s'assure d'enlèver le slash ultime
		$urlBase = preg_replace("/\/+$/", "", $_ENV["APP_URL"]);
		//$manager->setSerializer(new JsonApiSerializer($urlBase));
		$manager->setSerializer(new JsonApiSerializer($urlBase));
		if (!empty($request->query("include"))) {
			$manager->parseIncludes($request->query("include"));
		}
		return $manager;
	}

	public function item($data, $transformer)
	{
		if ($data == null) {
			return [null];
		}

		$manager = $this->getFractalManager();
		$resource = new Item($data, $transformer, $transformer->type);
		$item = $manager->createData($resource)->toArray();

		return $item;
	}

	public function collection($data, $transformer)
	{
		if ($data == null) {
			return [null];
		}

		$manager = $this->getFractalManager();
		$resource = new Collection($data, $transformer, $transformer->type);
		$item = $manager->createData($resource)->toArray();

		return $item;
	}

	/**
	 * @param LengthAwarePaginator $data
	 * @param $transformer
	 * @return array
	 */
	public function paginate($data, $transformer)
	{
		$manager = $this->getFractalManager();
		$resource = new Collection($data, $transformer, $transformer->type);
		$resource->setPaginator(new IlluminatePaginatorAdapter($data));
		return $manager->createData($resource)->toArray();
	}

	protected function préparer_réponse($réponse, $code = 200)
	{
		$request = app(Request::class);
		if ($réponse != null && $réponse != [null]) {
			Log::info("({$request->ip()}) - {$request->method()} {$request->path()} (" . get_class($this) . ")");
			return $this->réponse_json($réponse, $code);
		} else {
			Log::warning("({$request->ip()}) - {$request->method()} {$request->path()} (" . get_class($this) . ")");
			return $this->réponse_json(["erreur" => "Ressource non trouvée."], 404);
		}
	}
}
