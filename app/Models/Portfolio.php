<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'notes'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The relationships that should always be eagerly loaded.
     *
     * @var array
     */
    protected $with = ['users'];

    /**
     * The attributes that should be appended.
     *
     * @var array
     */
    protected $appends = ['owner_id'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('owner');
    }

    public function holdings()
    {
        return $this->hasMany(Holding::class, 'portfolio_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeMyPortfolios()
    {
        return $this->whereRelation('users', 'id', auth()->user()->id);
    }

    public function getOwnerIdAttribute()
    {
        return $this->users()->where('owner', 1)->first()?->id;
    }
}
