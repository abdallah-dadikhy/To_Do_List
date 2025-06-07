<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description',
        'is_completed', 'due_date',
        'priority_id', 'category_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function priority(){
        return $this->belongsTo(Priority::class);
    }
}

