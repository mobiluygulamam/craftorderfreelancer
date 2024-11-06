{{ Form::open(['url' => 'email_template', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('name', __('Name')) }}<x-required></x-required>
        {{ Form::text('name', null, ['class' => 'form-control ', 'required' => 'required']) }}
    </div>
    <div class="form-group col-md-12 text-right">
        {{--        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button> --}}
        {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
    </div>
</div>
{{ Form::close() }}
