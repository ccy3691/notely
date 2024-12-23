<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Note extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'user_id',
        'category',
        'content',
        'content_html',
        'title',
    ];

    public function getContentHtmlAttribute()
    {
        $content = json_decode($this->content, true);
        
        if (!$content) {
            return null;
        }

        // Convert Delta format to HTML using the QuillDeltaToHtml class
        $lexer = new \nadar\quill\Lexer($content);

        $cont = $lexer->render();

        return $cont;
    }

    /**
     * Relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toSearchableArray(): array
    {
        return array_merge(
            $this->toArray(),
            [
                // Cast id to string and turn created_at into an int32 timestamp
                // in order to maintain compatibility with the Typesense index definition below
                'id' => (string) $this->id,
                'created_at' => $this->created_at->timestamp,
                'content_html' => strip_tags($this->content_html),
                'user_id' => (string) $this->user_id
            ]
        );
    }

    public function searchableAs(): string
    {
        return 'notes';
    }
}
