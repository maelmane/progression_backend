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

namespace progression\http\transformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\Item;

class Transformer {
    protected function getFractalManager($includes)
    {
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer("https://progression.dti.crosemont.quebec")); //À CHANGER. Dans .env? est-ce qu'on peut le trouver automatiquement?
        $manager->parseIncludes($includes);
        return $manager;
    }

    public function item($data, $transformer, $includes, $resourceKey = null)
    {
        $manager = $this->getFractalManager($includes);
        $resource = new Item($data, $transformer, $transformer->type);
        return $manager->createData($resource)->toArray();
    }
}

?>
