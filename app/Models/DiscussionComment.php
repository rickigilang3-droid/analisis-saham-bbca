<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionComment extends Model
{
    protected $fillable = ['discussion_id', 'user_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}