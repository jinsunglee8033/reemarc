<form method="GET" action="{{ route('treatments.index') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-4">
        <input type="text" name="q" class="design-field" id="q" placeholder="Type name or email.." value="{{ !empty($filter['q']) ? $filter['q'] : old('q') }}">
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="region">
                <option value="">Select Region</option>
                @foreach ($regions_ as $value)
                    <option value="{{ $value }}" @if( $value == $region_) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="role">
                <option value="">Select Role</option>
                @foreach ($roles_ as $key => $value)
                    <option value="{{ $value }}" @if( strtolower($key) == $role_) selected="selected" @endif >
                        {{$key}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <button class="design-white-btn">Apply</button>
        </div>

        <div class="form-group col-md-2">
            <div class="follow" style="float:right; margin-top: -5px; padding-right: 15px;">
                <i class="left" onclick="moveScrollRight()" ></i>
                <i class="right" onclick="moveScrollLeft()" ></i>
            </div>
        </div>

{{--        <div class="form-group col-md-2">--}}
{{--            <a href="{{ url('admin/treatments/create') }}">--}}
{{--                <button type="button" class="design-white-btn"><i class="fas fa-plus"></i> Create</button>--}}
{{--            </a>--}}
{{--        </div>--}}
    </div>
</form>
