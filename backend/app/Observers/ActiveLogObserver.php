<?php

namespace App\Observers;

use App\Models\ActiveLog;

class ActiveLogObserver
{
    public function created(ActiveLog $activeLog): void
    {

    }

    public function updated(ActiveLog $activeLog): void
    {
    }

    public function deleted(ActiveLog $activeLog): void
    {
    }

    public function restored(ActiveLog $activeLog): void
    {
    }
}
