<form method="post" action="{{ route('coupons.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="text-end col-12">
                <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                    data-url="{{ route('generate', ['coupon']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Coupon Name') }}">
                    <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
                </a>
            </div>

            <div class="form-group col-md-12">
                <label for="name" class="col-form-label">{{ __('Name') }}</label><x-required></x-required>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="discount" class="col-form-label">{{ __('Discount') }}</label><x-required></x-required>
                <input type="number" name="discount" class="form-control" required step="0.01">
                <span class="small">{{ __('Note: Discount in Percentage') }}</span>
            </div>

            <div class="form-group col-md-6">
                <label for="limit" class="col-form-label">{{ __('Limit') }}</label><x-required></x-required>
                <input type="number" name="limit" class="form-control" required>
            </div>

            <div class="form-group col-md-12" id="auto">
                {{ Form::label('limit', __('Code'), ['class' => 'col-form-label']) }}<x-required></x-required>
                <div class="input-group">
                    {{ Form::text('code', null, ['class' => 'form-control', 'id' => 'auto-code', 'required' => 'required']) }}
                    <button class="btn btn-outline-secondary" type="button" id="code-generate"><i
                            class="fa fa-history pr-1"></i>{{ __(' Generate') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="text-end col-auto">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <input type="submit" value="{{ __('Save Changes') }}" class="btn  btn-primary">
        </div>
    </div>
</form>
