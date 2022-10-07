<?php $asset_type = 'social_ad'; ?>

<form method="POST" action="{{ route('campaign.add_social_ad') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $asset_type }}_c_id" value="{{ $campaign->id }}" />
    <input type="hidden" name="{{ $asset_type }}_asset_type" value="{{ $asset_type }}" />
    <input type="hidden" name="{{ $asset_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Run From: (Lead Time 12 Days)</label>
                <input type="text" name="{{ $asset_type }}_date_from" id="date_from" placeholder="Start date"
                       class="form-control @error('date_from') is-invalid @enderror @if (!$errors->has('date_from') && old('date_from')) is-valid @endif"
                       value="{{ old('date_from', null) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Run To: </label>
                <input type="text" name="{{ $asset_type }}_date_to" id="date_to" placeholder="End date"
                       class="form-control datepicker @error('date_to') is-invalid @enderror @if (!$errors->has('date_to') && old('date_to')) is-valid @endif"
                       value="{{ old('date_to', null) }}">
            </div>
        </div>
    </div>


    <div class="form-group checkboxes">
        <label>Include Formats: </label>
        <a href="javascript:void(0)" class="kiss-info-icon" tabindex="-1" title="Choose one or more"></a><br/>
        <?php foreach($social_ad_fields as $checkbox_field): ?>
            <input
                    type="checkbox"
                    name="{{ $asset_type }}_include_formats[]"
                    value="{{ $checkbox_field }}"
            />
            <span> <?php echo $checkbox_field; ?></span><br/>
        <?php endforeach; ?>
    </div>

    <div class="form-group">
        <label>Text:</label>
        <input type="text" name="{{ $asset_type }}_text" class="form-control" value="">
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Headline: <b style="color: #b91d19">(Max 40 characters)</b></label>
        <input type="text" name="{{ $asset_type }}_headline" class="form-control" value="">
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Note:</label>
        <textarea class="form-control" id="{{ $asset_type }}_note" name="{{ $asset_type }}_note" rows="5" cols="100"></textarea>
    </div>

    <div class="form-group">
        <label>Newsfeed:</label>
        <input type="text" name="{{ $asset_type }}_newsfeed" class="form-control" value="">
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Products Featured:</label>
        <input type="text" name="{{ $asset_type }}_products_featured" class="form-control" value="">
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Destination URL:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_click_through_links" class="form-control" placeholder="https://www.example.com" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>UTM Code:</label>
        <input type="text" name="{{ $asset_type }}_utm_code" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Promo Code:</label>
        <input type="text" name="{{ $asset_type }}_promo_code" class="form-control" value="">
    </div>


    <div class="form-group">
        <label>Upload Visual References:</label>
        <input type="file" data-asset="default" name="{{ $asset_type }}_c_attachment[]" class="form-control c_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="create asset" style="margin-top:10px;" class="btn btn-primary submit"/>
    </div>

</form>

<script type="text/javascript">
    // Lead time +12 days - Social Ads (exclude weekend)
    $(function() {
        var count = 12;
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
        $('input[name="<?php echo $asset_type; ?>_date_from"]').daterangepicker({
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
