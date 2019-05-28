<?php

namespace App\Http\Requests;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Enums\Logs\JobType;
use BenSampo\Enum\Rules\EnumValue;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Monolog\Logger;

class LogFilteringRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'draw' => 'required|integer',
            'start' => 'required|integer',
            'length' => 'required|integer',
            'message' => 'nullable|string|min:3',
            'order.0.dir' => 'nullable|in:asc,desc',
            'date' => [
                'nullable',
                'date_format:d/m/Y',
                Rule::requiredIf($this->filled('start_time') || $this->filled('end_time')),
            ],
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            //NOTE: can't force public administration existence
            //      since Public Administration purge force delete it
            //      See: ProcessPendingWebsites:handle()
            'ipa_code' => [
                'nullable',
                Rule::requiredIf($this->filled('pa')),
            ],
            //NOTE: can't force website existence
            //      since Website purge force delete it
            //      See: ProcessPendingWebsites:handle()
            'slug' => [
                'nullable',
                Rule::requiredIf($this->filled('website')),
            ],
            'uuid' => [
                'nullable',
                Rule::requiredIf($this->filled('user')),
                'exists:users,uuid',
            ],
            'event' => [
                'nullable',
                'integer',
                new EnumValue(EventType::class, false),
            ],
            'exception' => [
                'nullable',
                'integer',
                new EnumValue(ExceptionType::class, false),
                Rule::requiredIf($this->filled('event') && EventType::EXCEPTION === ((int) $this->input('event'))),
            ],
            'job' => [
                'nullable',
                'integer',
                new EnumValue(JobType::class, false),
            ],
            'severity' => [
                'nullable',
                'integer',
                Rule::in(Logger::getLevels()),
            ],
        ];
    }

    /**
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('start_time') && $this->filled('end_time')) {
                //NOTE: if 'date' is missing, validate 'start_time' and 'end_time' fields using current date.
                //      'date' field missing is already reported as error
                $startTime = Carbon::createFromFormat('d/m/Y H:i', $this->input('date', Carbon::now()->format('d/m/Y')) . ' ' . $this->input('start_time'));
                $endTime = Carbon::createFromFormat('d/m/Y H:i', $this->input('date', Carbon::now()->format('d/m/Y')) . ' ' . $this->input('end_time'));
                if (!$startTime->isBefore($endTime)) {
                    $validator->errors()->add('start_time', __('validation.before', ['attribute' => __('validation.attributes.start_time'), 'date' => $this->input('end_time')]));
                    $validator->errors()->add('end_time', __('validation.after', ['attribute' => __('validation.attributes.end_time'), 'date' => $this->input('start_time')]));
                }
            }
        });
    }
}
