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

use League\Fractal\Resource\Collection;
use progression\domaine\entité\{Tentative, TentativeSys};

class TentativeSysTransformer extends BaseTransformer
{
	public $type = "tentative";
	protected array $availableIncludes = ["resultats", "commentaires"];

    /**
     * @return array<mixed>
     */
	public function transform(TentativeSys $tentative):array
	{
		$data_out = (new TentativeTransformer($this->id))->transform($tentative);
		$data_out = array_merge($data_out, [
			"sous-type" => "tentativeSys",
			"conteneur" => $tentative->conteneur,
			"réponse" => $tentative->réponse,
			"tests_réussis" => $tentative->tests_réussis,
		]);
		return $data_out;
	}

	public function includeResultats(TentativeSys $tentative):Collection
	{
        return (new TentativeTransformer($this->id))->includeResultats($tentative);
	}

	public function includeCommentaires(TentativeSys $tentative):Collection
	{
        return (new TentativeTransformer($this->id))->includeCommentaires($tentative);
	}
}
