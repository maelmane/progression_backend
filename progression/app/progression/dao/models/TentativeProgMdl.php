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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class TentativeProgMdl extends Model
{
	protected $table = "reponse_prog";
	public $timestamps = false;

	protected $guarded = [];

	public function avancement(): BelongsTo
	{
		return $this->belongsTo(AvancementMdl::class, "fk_tentative_prog_avancement");
	}

	public function commentaires(): HasMany
	{
		return $this->hasMany(CommentaireMdl::class, "tentative_id", "id");
	}
}

?>
