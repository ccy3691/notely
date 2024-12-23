<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;


class FlashcardTask extends Model
{
    use HasFactory;
    protected $table = 'flashcard_jobs';

    protected $fillable = [
        'category',
        'total_requested',
        'total_processed',
        'status',
        'user_id'
    ];

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class, 'flashcard_job_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->chaperone();
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($this->attributes['created_at'])->format('F j, Y, g:i a'); // Example format: January 1, 2022, 5:00 pm
    }
}
