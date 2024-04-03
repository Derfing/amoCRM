<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class MTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'm:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $json = '{
            "main": {
                "type": "active_lead",
                "text": "Позвонить по пропущенному",
                "task_type": 1,
                "task_till": "10"
            }
        }';
        $arr = json_decode($json, 1);
        dd(Validator::make(Arr::get($arr, 'main'), [
            "type" => "required|in:contact,all_leads,active_lead",
            "text" => "nullable|string",
            "task_type" => "required|int",
            "task_till" => "required|int",
        ])->fails());
        return Command::SUCCESS;
    }
}
