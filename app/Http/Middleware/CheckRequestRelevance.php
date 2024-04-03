<?php

namespace App\Http\Middleware;

use AmoCRM\Models\Interfaces\CallInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class CheckRequestRelevance
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        dd($request);
        $data = $request->all();

        $dataToValidate = $this->collectData($data);

        $res = $this->validateData($dataToValidate);

        return $res === true ? $next($request) : $res;
    }

    private function collectData($data): Collection
    {
        $note = head(data_get($data, "*.note.0.note", null));
        $noteAsText = data_get($note, 'text', '');
        $noteAsArray = is_string($noteAsText) ? json_decode($noteAsText, true) : $noteAsText;

        return collect([
            'entityType' => data_get($note, 'element_type'),
            'text' => $noteAsArray,
            'type' => data_get($note, 'note_type')
        ]);

    }

    private function validateData(Collection $noteData): bool
    {
        if ($noteData->get('type') != '10') { // 10 - NoteFactory::NOTE_TYPE_CODE_CALL_IN
            logger()->debug('Не интересующий тип премечания', ['type' => $noteData->get('type')]);
            abort(200, 'Не интересующий тип премечания');
        }

        if (data_get($noteData->get('text'), 'call_status') != CallInterface::CALL_STATUS_FAIL_NOT_PHONED) {
            logger()->debug('Не интересующий тип звонка', ['type' => data_get($noteData->get('text'), 'call_status')]);
            abort(200, 'Не интересующий тип звонка');
        }

        return true;
    }
}
