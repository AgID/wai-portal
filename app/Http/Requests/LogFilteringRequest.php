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

/**
 * Log data request.
 */
class LogFilteringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * NOTE: always return true since authorization logic is performed using middleware.
     *
     * @return bool true
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array the validation rules
     */
    public function rules(): array
    {
        return [
            'draw' => 'required|integer',
            'start' => 'required|integer',
            'length' => 'required|integer',
            'message' => 'nullable|string|min:3',
            'order.0.dir' => 'nullable|in:asc,desc',
            'start_date' => [
                'nullable',
                'date_format:d/m/Y',
                Rule::requiredIf($this->filled('start_time')),
            ],
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => [
                'nullable',
                'date_format:d/m/Y',
                Rule::requiredIf($this->filled('end_time')),
            ],
            'end_time' => 'nullable|date_format:H:i',
            //NOTE: can't force public administration existence
            //      since Public Administration purge force delete it
            //      See: ProcessPendingWebsites:handle()
            'pa_ipa_code' => [
                'nullable',
                Rule::requiredIf($this->filled('pa')),
            ],
            //NOTE: can't force website existence
            //      since Website purge force delete it
            //      See: ProcessPendingWebsites:handle()
            'website_id' => [
                'nullable',
                Rule::requiredIf($this->filled('website')),
            ],
            'user_uuid' => [
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
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator the validator
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('start_time') && $this->filled('end_time')) {
                if (!$this->filled('start_date') || !$this->filled('end_date')) {
                    //NOTE: On 'start_date' or 'end_date' missing, a validation error is already reported
                    //      and before/after validation cannot be done
                    return;
                }
                $startTime = Carbon::createFromFormat('d/m/Y H:i', $this->input('start_date') . ' ' . $this->input('start_time'));
                $endTime = Carbon::createFromFormat('d/m/Y H:i', $this->input('end_date') . ' ' . $this->input('end_time'));
                if (!$startTime->isBefore($endTime)) {
                    $validator->errors()->add('start_time', __('validation.before', ['attribute' => __('validation.attributes.start_time'), 'date' => $this->input('end_time')]));
                    $validator->errors()->add('end_time', __('validation.after', ['attribute' => __('validation.attributes.end_time'), 'date' => $this->input('start_time')]));
                }
            }
        });
    }
}
