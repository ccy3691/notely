<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\FlashcardTask;
use App\Models\Note;
use DateTime;
use Laravel\Scout\EngineManager;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function flashcardTasks(): HasMany
    {
        return $this->hasMany(FlashcardTask::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    protected static function booted()
    {
        static::retrieved(function (User $user) {

            // Early return if the organization already has a token
            if ($user->meilisearch_token) {
                return;
            }

            // The object belows is used to generate a tenant token that:
            // • applies to all indexes
            // • filters only documents where `organization_id` is equal to this org ID
            $searchRules = (object) [
                '*' => (object) [
                    'filter' => 'user_id = ' . $user->id,
                ]
            ];

            // Replace with your own Search API key and API key UID
            $meiliApiKey = env('MEILISEARCH_KEY');
            $meiliApiKeyUid = env('MEILISEARCH_UID');

            // Generate the token
            $token = self::generateMeiliTenantToken($meiliApiKeyUid, $searchRules, $meiliApiKey);

            // Save the token in the database
            $user->meilisearch_token = $token;
            $user->save();
        });
    }

    protected static function generateMeiliTenantToken($meiliApiKeyUid, $searchRules, $meiliApiKey)
    {
        $meilisearch = resolve(EngineManager::class)->engine();

        return $meilisearch->generateTenantToken(
            $meiliApiKeyUid,
            $searchRules,
            [
                'apiKey' => $meiliApiKey,
                'expiresAt' => new DateTime('2030-12-31'),
            ]
        );
    }
}
