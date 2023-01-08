<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {
	use HasFactory;

	public $fillable = [
		'content',
		'user_id',
		'post_id'
	];

	public function user() {
		return $this->belongsTo(User::class);
	}
	
	public function post() {
		return $this->belongsTo(Post::class);
	}
}
