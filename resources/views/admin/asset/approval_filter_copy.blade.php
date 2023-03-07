<form method="GET" action="{{ route('asset.approval_copy') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-2">
            <input type="text" name="q" class="design-field" id="q" placeholder="Search by Project Name" value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="brand_id">
                <option value="">Select Brand</option>
                @foreach ($brands as $key => $value)
                    <option value="{{ $key }}" @if( $key == $brand_id) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <input type="text" name="campaign_id" class="design-field" id="q" placeholder="Search by Project ID" value="{{ !empty($filter['campaign_id']) ? $filter['campaign_id'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            <input type="text" name="asset_id" class="design-field" id="q" placeholder="Search by Asset ID" value="{{ !empty($filter['asset_id']) ? $filter['asset_id'] : '' }}">
        </div>
{{--        <div class="form-group col-md-2">--}}
{{--        </div>--}}
{{--        <div class="form-group col-md-2">--}}
{{--        </div>--}}
        <div class="form-group col-md-2">
            <button class="design-btn">Apply</button>
        </div>
    </div>
</form>
