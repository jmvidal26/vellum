<?php

namespace App\Models;

use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Colecao;
use App\Models\QuizAttempt;
use App\Models\Badge;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function inscricaoClube(): HasOne
    {
        return $this->hasOne(ClubeMembro::class);
    }

    public function getIsMembroClubeAttribute(): bool
    {
        $this->loadMissing('inscricaoClube');

        return $this->inscricaoClube !== null;
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badge');
    }

    public function leiturasFinalizadas(): HasMany
    {
        return $this->hasMany(UsuarioLeitura::class)->where('status', 'finalizado');
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(LivroFavorito::class);
    }

    public function comentariosClube(): HasMany
    {
        return $this->hasMany(ClubeComentario::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailCustom);
    }

    public function colecoes()
    {
        return $this->hasMany(Colecao::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

}
