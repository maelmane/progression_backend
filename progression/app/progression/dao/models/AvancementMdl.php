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

namespace progression\dao\models;

use progression\domaine\entité\question\État;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvancementMdl extends Model
{
	protected $table = "avancement";
	public $timestamps = false;

	protected $guarded = [];

	protected $appends = ["etat"];

	public function getÉtatAttribute(): État
	{
		$états = array_column(État::cases(), "value");
		return État::from($états[$this->attributes["etat"]]);
	}
	public function setÉtatAttribute(État $état): void
	{
		$états = array_column(État::cases(), "value");
		$this->attributes["etat"] = array_search($état->value, $états);
	}

	public function tentatives_prog(): HasMany
	{
		return $this->hasMany(TentativeProgMdl::class, "avancement_id", "id");
	}

	public function tentatives_sys(): HasMany
	{
		return $this->hasMany(TentativeSysMdl::class, "avancement_id", "id");
	}

	public function sauvegardes(): HasMany
	{
		return $this->hasMany(SauvegardeMdl::class, "avancement_id", "id");
	}
}
?>
