<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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
        'phone',
        'role',
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
    
    /**
     * Връзка с апартаментите, свързани с този потребител.
     */
    public function apartments()
    {
        return $this->belongsToMany(Apartment::class)
            ->withPivot('is_owner')
            ->withTimestamps();
    }
    
    /**
     * Връща апартаментите, на които потребителят е собственик.
     */
    public function ownedApartments()
    {
        return $this->apartments()->wherePivot('is_owner', true);
    }
    
    /**
     * Връща апартаментите, на които потребителят е наемател.
     */
    public function rentedApartments()
    {
        return $this->apartments()->wherePivot('is_owner', false);
    }
    
    /**
     * Връща показанията, въведени от този потребител.
     */
    public function readings()
    {
        return $this->hasMany(Reading::class);
    }
    
    /**
     * Проверява дали потребителят има дадена роля.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    /**
     * Проверява дали потребителят е администратор.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Проверява дали потребителят е домоуправител.
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
