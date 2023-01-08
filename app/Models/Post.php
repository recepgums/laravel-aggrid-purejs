<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
	use HasFactory;

	public function messages() {
		return $this->hasMany(Message::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}
}
