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

use League\Fractal;
use progression\domaine\entité\Avancement;
use progression\util\Encodage;

class AvancementTransformer extends Fractal\TransformerAbstract
{
	public $type = "avancement";

	protected $availableIncludes = ["tentatives"];

	public function transform(Avancement $avancement)
	{
		$data_out = [
			"id" =>
			"{$avancement->username}/" .
				Encodage::base64_encode_url($avancement->question_uri),
			"username" => $avancement->username,
			"état" => $avancement->etat,
			"links" => [
				"self" =>
				"{$_ENV["APP_URL"]}avancement/{$avancement->username}/" .
					Encodage::base64_encode_url($avancement->question_uri)
			],
		];

		return $data_out;
	}
}
