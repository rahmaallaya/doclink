<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    // Relations
    public function questions()
    {
        return $this->hasMany(Question::class, 'category_id');
    }

    public function doctors()
    {
        return User::where('role', 'medecin')
            ->where('specialty', $this->name)
            ->get();
    }
}