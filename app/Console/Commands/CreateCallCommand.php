<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Makeroi\Amocrm\Kernel\Auth\KAuth;
use Ramsey\Uuid\Uuid;
use AmoCRM\Models\CallModel;
use AmoCRM\Models\Interfaces\CallInterface;

class CreateCallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test call';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        KAuth::fromJWT('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZGVyZW5kYWV2a29zdGE0NS5hbW9jcm0ucnUiLCJhdWQiOiJodHRwczpcL1wva2VybmVsLm1ha2Vyb2kudGVjaCIsImp0aSI6ImE4MmQyMTc4LTRhMjgtNDM4YS1hYTA3LThlZTdiODk5NGZkOCIsImlhdCI6MTcxMjE1Nzc2MiwibmJmIjoxNzEyMTU3NzYyLCJleHAiOjE3MTIxNTk1NjIsImFjY291bnRfaWQiOjMxNjQ4Nzk4LCJzdWJkb21haW4iOiJkZXJlbmRhZXZrb3N0YTQ1IiwiY2xpZW50X3V1aWQiOiI1NGZmODMwMS05MTM5LTQ2Y2UtYjAyOC1mYTg2MDIwMmY5MTciLCJ1c2VyX2lkIjoxMDgzNDQxNCwiaXNfYWRtaW4iOnRydWV9.5tmyhurX79vQucMxDz1AdW084fPjYPHo666oXpupyEY');
        $apiClient = KAuth::getApiClient();

        $call = new CallModel();
        $call
            ->setPhone('89000000004')
            ->setCallStatus(CallInterface::CALL_STATUS_FAIL_NOT_PHONED)
            ->setCallResult('Разговор не состоялся')
            ->setUniq(Uuid::uuid4())
            ->setLink('https://test/test.mp3')
            ->setDuration(1)
            ->setSource('call_tasks')
            ->setResponsibleUserId(10834414)
            ->setDirection(CallInterface::CALL_DIRECTION_IN);

        $apiClient->calls()->addOne($call);

        return Command::SUCCESS;
    }
}

