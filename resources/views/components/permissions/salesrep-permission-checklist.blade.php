<div class="card">
    <div class="card-header">
        <h5 class="card-title">Sales Representative Permissions</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($permissions->chunk(4) as $chunk)
            <div class="col-md-6">
                @foreach($chunk as $permission)
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                        id="perm-{{ $permission->id }}" {{ in_array($permission->id, $selectedPermissions) ? 'checked' :
                    '' }}
                    >
                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                        {{ __('permissions.' . $permission->name) }}
                    </label>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>
