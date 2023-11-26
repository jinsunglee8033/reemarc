<form method="GET" action="{{ route('campaign.index') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-2">
            <input type="text" name="q" class="design-field" id="q" placeholder="Project Name" value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
            <input type="hidden" name="status" id="status" value="active">
        </div>
        <div class="form-group col-md-2">
            <input type="text" name="id" class="design-field" id="id" placeholder="Project ID #" value="{{ !empty($filter['id']) ? $filter['id'] : '' }}">
        </div>
{{--        <div class="form-group col-md-2">--}}
{{--            <select class="design-select" name="brand">--}}
{{--                <option value="">Select Brand</option>--}}
{{--                @foreach ($brands as $key => $value)--}}
{{--                    <option value="{{ $key }}" @if( $key == $brand) selected="selected" @endif >--}}
{{--                        {{$value}}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
        <div class="form-group col-md-2">
            <button class="design-btn"> Apply </button>
        </div>
        <div class="form-group col-md-2">
            <a href="{{ url('admin/campaign/create') }}">
                <button type="button" class="design-white-btn"><i class="fas fa-plus"></i> Create</button>
            </a>
        </div>
    </div>
</form>
