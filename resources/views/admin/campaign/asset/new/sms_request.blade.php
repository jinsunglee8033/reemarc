<?php $asset_type = 'sms_request'; ?>

<form method="POST" action="{{ route('campaign.add_sms_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $asset_type }}_c_id" value="{{ $campaign->id }}" />
    <input type="hidden" name="{{ $asset_type }}_asset_type" value="{{ $asset_type }}" />
    <input type="hidden" name="{{ $asset_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label class="form-label">Team:</label>
        <div class="selectgroup w-100">
            <label class="selectgroup-item">
                <input type="radio" name="{{ $asset_type }}_team_to" value="creative" class="selectgroup-input" checked="">
                <span class="selectgroup-button">Creative</span>
            </label>
            <label class="selectgroup-item">
                <input type="radio" name="{{ $asset_type }}_team_to" value="content" class="selectgroup-input">
                <span class="selectgroup-button">Content</span>
            </label>
            <label class="selectgroup-item">
                <input type="radio" name="{{ $asset_type }}_team_to" value="web production" class="selectgroup-input">
                <span class="selectgroup-button">Web Production</span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Title:</label>
        <textarea class="form-control" maxlength="255" name="{{ $asset_type }}_title" rows="5" cols="100"></textarea>
    </div>

    <div class="form-group">
        <label>Launch Date: (Lead Time 22 Days)</label>
        <input type="text" name="{{ $asset_type }}_launch_date" id="{{ $asset_type }}_launch_date"
               class="form-control @error($asset_type.'_launch_date') is-invalid @enderror @if (!$errors->has($asset_type.'_launch_date') && old($asset_type.'_launch_date')) is-valid @endif"
               value="{{ old($asset_type.'_launch_date', null) }}">
    </div>

    <div class="form-group">
        <label>Details:</label>
        <textarea class="form-control" id="{{ $asset_type }}_details" name="{{ $asset_type }}_details" rows="5" cols="100"></textarea>
    </div>

    <div class="form-group">
        <label>Products Featured:</label>
        <input type="text" name="{{ $asset_type }}_products_featured" class="form-control" value="">
    </div>

    <div class="form-group">
        <hr>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="{{ $asset_type }}_no_copy_necessary" value="on" class="custom-switch-input">
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description">No Copy Necessary</span>
        </label>
    </div>

    <div class="form-group">
        <label>Note / Copy: <b style="color: #b91d19">(160 characters Maximum)</b></label>
        <textarea class="form-control" name="{{ $asset_type }}_copy" style="height:100px;" onkeyup="new_sms_request_limit(this)"></textarea>
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
        <p id="new_sms_request_charsLeft"></p>
    </div>

    <div class="form-group">
        <label>Developer Proof:</label>
        <input type="text" name="{{ $asset_type }}_developer_url" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Upload Visual References: <b style="color: #b91d19">(5mb in size)</b></label>
        <input type="file" data-asset="default" name="{{ $asset_type }}_c_attachment[]" class="form-control c_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="create asset" style="margin-top:10px;" class="btn btn-primary submit"/>
    </div>

</form>

<script type="text/javascript">
    // Lead time +22 days - sms_request (exclude weekend)
    $(function() {
        var count = 22;
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

    var max_chars = 160;
    // var charsLeftDisplay = document.getElementById("charsLeft");

    function new_sms_request_limit(element) {
        if (element.value.length > max_chars) {
            element.value = element.value.slice(0, -1);
            return false;
        }
        var charsLeftDisplay = document.getElementById("new_sms_request_charsLeft");
        charsLeftDisplay.innerHTML = (max_chars - element.value.length) + " characters left...";
    }

</script>
