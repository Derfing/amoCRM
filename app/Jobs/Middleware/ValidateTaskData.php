<?php

namespace App\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class ValidateTaskData
{

    /**
     * Обработать задание в очереди.
     *
     * @param  Closure(object): void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        $job->typeOfEntity = head(data_get($job->data, '*.note.0.note.element_type', -1));

    }
}
