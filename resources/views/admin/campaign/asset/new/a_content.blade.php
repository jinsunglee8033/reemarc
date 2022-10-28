<?php $asset_type = 'a_content'; ?>

<form method="POST" action="{{ route('campaign.add_a_content') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $asset_type }}_c_id" value="{{ $campaign->id }}" />
    <input type="hidden" name="{{ $asset_type }}_asset_type" value="{{ $asset_type }}" />
    <input type="hidden" name="{{ $asset_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label>Product Line:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_product_line" class="form-control" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>Launch Date: (Lead Time 27 Days)</label>
        <input type="text" name="{{ $asset_type }}_launch_date" id="{{ $asset_type }}_launch_date"
               class="form-control @error($asset_type.'_launch_date') is-invalid @enderror @if (!$errors->has($asset_type.'_launch_date') && old($asset_type.'_launch_date')) is-valid @endif"
               value="{{ old($asset_type.'_launch_date', null) }}">
    </div>

    <div class="form-group">
        <label>Invision Link:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_invision_link" class="form-control" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>SKUs / Description (File Attachment):</label>
        <input type="file" data-asset="default" name="{{ $asset_type }}_c_attachment[]" class="form-control c_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="create asset" style="margin-top:10px;" class="btn btn-primary submit"/>
    </div>

</form>

<script type="text/javascript">
    // Lead time +27 days - A+ Content (exclude weekend)
    $(function() {
        var count = 27;
        var today = new Date();
        for (let i = 1; i <= count; i++) {
            today.setDate(today.getDate() + 1);
            if (today.getDay() === 6) {
                today.setDate(today.getDate() + 2);
            }
            else if (today.getDay() === 0) {
                today.setDate(today.getDate() + 1);
            }
        }
        $('input[name="<?php echo $asset_type; ?>_launch_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
    });
</script>
