@php
    use App\Models\Utility;
    $setting = Utility::getAdminPaymentSetting();
@endphp

<form method="post" action="{{ route('plans.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="text-end col-12">
                <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                    data-url="{{ route('generate', ['plan']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Plan Name & Description') }}">
                    <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Name') }}</label><x-required></x-required>
                    <input type="text" class="form-control" id="name" name="name" required />
                </div>
            </div>
            <div class="form-group col-md-3 pt-3 ">
                <div class="form-check form-switch d-inline-block py-4 ">
                    <input type="checkbox" class="form-check-input" name="enable_chatgpt" id="enable_chatgpt">
                    <label class="custom-control-label form-check-label"
                        for="enable_chatgpt">{{ __('Enable Chatgpt') }}</label>
                </div>
            </div>
            <div class="form-group col-md-3 pt-3">
                <div class="form-check form-switch d-inline-block py-4">
                    <input type="checkbox" class="form-check-input" name="status" id="status" checked="checked">
                    <label class="custom-control-label form-control-label" for="status">{{ __('Status') }}</label>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="monthly_price"
                    class="form-control-label">{{ __('Monthly Price') }}</label><x-required></x-required>
                <div class="form-icon-user">
                    <span
                        class="currency-icon bg-primary">{{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}</span>
                    <input class="form-control currency_input" type="number" min="0" id="monthly_price"
                        name="monthly_price" placeholder="{{ __('Monthly Price') }}" step="0.01" required>
                </div>
            </div>

            <div class="form-group col-md-6">
                <label for="annual_price "
                    class="form-control-label">{{ __('Annual Price') }}</label><x-required></x-required>
                <div class="form-icon-user">
                    <span
                        class="currency-icon bg-primary">{{ $setting['currency_symbol'] ? $setting['currency_symbol'] : '$' }}</span>
                    <input class="form-control currency_input" type="number" min="0" id="annual_price"
                        name="annual_price" placeholder="{{ __('Annual Price') }}" step="0.01" required>
                </div>
            </div>

            <div class="form-group col-md-6">
                <label for="storage_limit"
                    class="form-control-label">{{ __('Storage Limit') }}</label><x-required></x-required>
                <div class="input-group">
                    <input type="text" class="form-control mb-0" id="storage_limit" name="storage_limit" required />
                    <div class="input-group-append">
                        <span class="input-group-text">MB</span>
                    </div>
                </div>
                <span class="small">{{ __('Note: upload size (In MB)') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label for="max_workspaces"
                    class="form-label">{{ __('Maximum Workspaces') }}</label><x-required></x-required>
                <input type="number" class="form-control mb-0" id="max_workspaces" name="max_workspaces" required />
                <span><small>{{ __("Note: '-1' for unlimited") }}</small></span>
            </div>
            <div class="form-group col-md-6">
                <div class="form-group">
                    <label for="max_users"
                        class="form-label">{{ __('Maximum Users Per Workspace') }}</label><x-required></x-required>
                    <input type="number" class="form-control mb-0" id="max_users" name="max_users" required />
                    <span><small>{{ __("Note: '-1' for unlimited") }}</small></span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="max_clients"
                    class="form-label">{{ __('Maximum Clients Per Workspace') }}</label><x-required></x-required>
                <input type="number" class="form-control mb-0" id="max_clients" name="max_clients" required />
                <span><small>{{ __("Note: '-1' for unlimited") }}</small></span>
            </div>
            <div class="form-group col-md-6">
                <label for="max_projects"
                    class="form-label">{{ __('Maximum Projects Per Workspace') }}</label><x-required></x-required>
                <input type="number" class="form-control mb-0" id="max_projects" name="max_projects" required />
                <span><small>{{ __("Note: '-1' for unlimited") }}</small></span>
            </div>
            <div class="form-group col-md-12 mb-0">
                <div class="form-group">
                    <label for="description" class="form-label">{{ __('Description') }}</label>
                    <textarea rows="3"class="form-control" id="description" name="description">{{ $plan->description }}</textarea>
                </div>
            </div>
            <div class="form-group col-md-6 d-flex">
                <label for="is_trial_disable">{{ __('Trial is enable(on/off)') }}</label>
                <div class="form-check form-switch custom-switch-v1 mx-2">
                    <input type="hidden" name="is_trial_disable" value="off">
                    <input type="checkbox" name="is_trial_disable" class="form-check-input input-primary pointer"
                        id="is_trial_disable">
                    <label class="form-check-label" for="is_trial_disable"></label>
                </div>
            </div>
            <div class="form-group col-md-6 ps_div d-none">
                <label for="duration"
                    class="form-control-label">{{ __('Trial Days') }}</label><x-required></x-required>
                <input type="number" class="form-control mb-0" id="trial_days" name="trial_days"
                    id="trial_days" />
                <span><small>{{ __("Note: '-1' for unlimited") }}</small></span>
            </div>
        </div>
    </div>
    <div class=" modal-footer">
        <div class="text-end col-auto">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
        </div>
    </div>
</form>
