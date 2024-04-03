<?php

namespace App\Settings;

use Makeroi\Amocrm\Kernel\Settings\SettingsBag;
use Illuminate\Support\Facades\Validator;

class WidgetSettingsBag extends SettingsBag
{
    function isValid(): bool
    {
        return !Validator::make((array) $this->get('main'), $this->rules())->fails();
    }
    function rules(): array
    {
        return [
            "type" => "required|in:contact,all_leads,active_lead",
            "text" => "nullable|string",
            "task_type" => "required|int",
            "task_till" => "required|int",
        ];
    }

    function getType() : string
    {
        return $this->get('main.type');
    }

    function getText() : ?string
    {
        return $this->get('main.text', 'Default MakeROI text.');
    }

    function getTaskType() : ?int
    {
        return $this->get('main.task_type', 0);
    }

    function getTaskTill() : ?int
    {
        $time = $this->get('main.task_till', -1);
        return now()->addMinutes($time)->getTimestamp();
    }
}
