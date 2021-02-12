<?php

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Laravel\Lumen\Routing\Controller as BaseController;

class Contrôleur extends BaseController
{
    protected function réponse_json( $réponse, $code=200 ){
        return response()->json($réponse,
                                $code,
                                ['Content-Type' => 'application/vnd.api+json', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    protected function getFractalManager()
    {
        $request = app(Request::class);
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
