<div class="modal-body">
    <table class="table  mb-0" id="dataTable-1">
        <thead>
            <tr>
                <th>{{ __('Module') }}</th>
                <th>{{ __('Permissions') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ __('Milestone') }}</td>
                <td>
                    <div class="row ">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission3"
                                @if (in_array('create milestone', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="create milestone">
                            <label for="permission3" class="custom-control-label">{{ __('Create') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission4"
                                @if (in_array('edit milestone', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="edit milestone">
                            <label for="permission4" class="custom-control-label">{{ __('Edit') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission5"
                                @if (in_array('delete milestone', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="delete milestone">
                            <label for="permission5" class="custom-control-label">{{ __('Delete') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission2"
                                @if (in_array('show milestone', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show milestone">
                            <label for="permission2" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                        <div class="col"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>{{ __('Task') }}</td>
                <td>
                    <div class="row">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission7"
                                @if (in_array('create task', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="create task">
                            <label for="permission7" class="custom-control-label">{{ __('Create') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission8"
                                @if (in_array('edit task', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="edit task">
                            <label for="permission8" class="custom-control-label">{{ __('Edit') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission9"
                                @if (in_array('delete task', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="delete task">
                            <label for="permission9" class="custom-control-label">{{ __('Delete') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission6"
                                @if (in_array('show task', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show task">
                            <label for="permission6" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission10"
                                @if (in_array('move task', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="move task">
                            <label for="permission10" class="custom-control-label">{{ __('Move') }}</label><br>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>{{ __('Bug Report') }}</td>
                <td>
                    <div class="row cust-checkbox-row">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission17"
                                @if (in_array('create bug report', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="create bug report">
                            <label for="permission17" class="custom-control-label">{{ __('Create') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission18"
                                @if (in_array('edit bug report', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="edit bug report">
                            <label for="permission18" class="custom-control-label">{{ __('Edit') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission19"
                                @if (in_array('delete bug report', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="delete bug report">
                            <label for="permission19" class="custom-control-label">{{ __('Delete') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission20"
                                @if (in_array('show bug report', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show bug report">
                            <label for="permission20" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission21"
                                @if (in_array('move bug report', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="move bug report">
                            <label for="permission21" class="custom-control-label">{{ __('Move') }}</label><br>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>{{ __('Expenses') }}</td>
                <td>
                    <div class="row cust-checkbox-row">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission17"
                                @if (in_array('create expenses', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="create expenses">
                            <label for="permission17" class="custom-control-label">{{ __('Create') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission18"
                                @if (in_array('edit expenses', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="edit expenses">
                            <label for="permission18" class="custom-control-label">{{ __('Edit') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission19"
                                @if (in_array('delete expenses', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="delete expenses">
                            <label for="permission19" class="custom-control-label">{{ __('Delete') }}</label><br>
                        </div>
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission20"
                                @if (in_array('show expenses', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show expenses">
                            <label for="permission20" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>{{ __('Activity') }}</td>
                <td>
                    <div class="row cust-checkbox-row">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission1"
                                @if (in_array('show activity', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show activity">
                            <label for="permission1" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>{{ __('Time Sheet') }}</td>
                <td>
                    <div class="row cust-checkbox-row">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission16"
                                @if (in_array('show timesheet', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show timesheet">
                            <label for="permission16" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>{{ __('Gantt Chart') }}</td>
                <td>
                    <div class="row cust-checkbox-row">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission22"
                                @if (in_array('show gantt', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show gantt">
                            <label for="permission22" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>{{ __('Uploading') }}</td>
                <td>
                    <div class="row cust-checkbox-row">
                        <div class="form-check form-switch d-inline-block col">
                            <input class="form-check-input" id="permission15"
                                @if (in_array('show uploading', $permissions)) checked="checked" @endif name="permissions[]"
                                type="checkbox" value="show uploading">
                            <label for="permission15" class="custom-control-label">{{ __('Show') }}</label><br>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
