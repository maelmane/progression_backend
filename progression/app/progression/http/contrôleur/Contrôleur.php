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

use progression\domaine\interacteur\InteracteurFactory;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Laravel\Lumen\Routing\Controller as BaseController;

class Contrôleur extends BaseController
{
    public function __construct(InteracteurFactory $intFactory=null){
        if ($intFactory == null ) {
            $this->intFactory = new InteracteurFactory();
        }
        else {
            $this->intFactory = $intFactory;
        }
    }
    
    protected function réponse_json( $réponse, $code=200 ){
        return response()->json($réponse,
                                $code,
                                ['Content-Type' => 'application/vnd.api+json', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    protected function getFractalManager()
    {
        $request = app(Request::class);
        error_log("INCLUDE".$request->query('include'));
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer($_ENV["APP_URL"]));
        if (!empty($request->query('include'))) {
            $manager->parseIncludes($request->query('include'));
        }
        return $manager;
    }

    
    public function item($data, $transformer, $resourceKey = null)
    {
        $manager = $this->getFractalManager();
        $resource = new Item($data, $transformer, $transformer->type);
        return $manager->createData($resource)->toArray();
    }


    public function collection($data, $transformer, $resourceKey = null)
    {
        $manager = $this->getFractalManager();
        $resource = new Collection($data, $transformer, $transformer->type);
        return $manager->createData($resource)->toArray();
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

}
