<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Makeroi\Amocrm\Kernel\Auth\KAuth;

class GetSettingBagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bag:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        KAuth::fromJWT('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZGVyZW5kYWV2a29zdGE0NS5hbW9jcm0ucnUiLCJhdWQiOiJodHRwczpcL1wva2VybmVsLm1ha2Vyb2kudGVjaCIsImp0aSI6Ijk4MDYxOTM4LTQ1ODAtNDRlYi1iOWU3LTJhM2Q4MzU4ZDNlOSIsImlhdCI6MTcxMTk4NzAwMywibmJmIjoxNzExOTg3MDAzLCJleHAiOjE3MTE5ODg4MDMsImFjY291bnRfaWQiOjMxNjQ4Nzk4LCJzdWJkb21haW4iOiJkZXJlbmRhZXZrb3N0YTQ1IiwiY2xpZW50X3V1aWQiOiI1NGZmODMwMS05MTM5LTQ2Y2UtYjAyOC1mYTg2MDIwMmY5MTciLCJ1c2VyX2lkIjoxMDgzNDQxNCwiaXNfYWRtaW4iOnRydWV9.CS37pA2yMHHm-JE8IQntuHA4_KyYtYsnYvr6SLmI1WQ');
        dd(KAuth::getSettingsBag());
    }
}
