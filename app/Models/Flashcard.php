<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FlashcardTask;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'flashcard_job_id',
    ];

    public function flashcardTask()
    {
        return $this->belongsTo(FlashcardTask::class, 'flashcard_job_id');
    }
}
