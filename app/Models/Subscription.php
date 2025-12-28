<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscription';

    protected $fillable = [
        'user_id',
        'email',
        'isFreeTrial',
        'isSubscribe',
        'trial_started_at',
        'trial_ends_at',
    ];

    protected $casts = [
        'isFreeTrial' => 'boolean',
        'isSubscribe' => 'boolean',
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    public function hasTrialExpired(): bool
    {
        if (!$this->trial_ends_at) {
            return false;
        }

        return Carbon::now()->greaterThan($this->trial_ends_at);
    }
}


