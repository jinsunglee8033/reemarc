<?php $asset_type = 'email_blast'; ?>

<form method="POST" action="{{ route('campaign.add_email_blast') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $asset_type }}_c_id" value="{{ $campaign->id }}" />
    <input type="hidden" name="{{ $asset_type }}_asset_type" value="{{ $asset_type }}" />
    <input type="hidden" name="{{ $asset_type }}_author_id" value="{{ Auth::user()->id }}" />
    <input type="hidden" name="{{ $asset_type }}_brand" value="{{ $campaign->campaign_brand }}" />

    <div class="form-group">
        <label>Main Concept:</label>
        <textarea class="form-control" id="{{ $asset_type }}_concept" name="{{ $asset_type }}_concept" rows="5" cols="100"></textarea>
    </div>

    <div class="form-group">
        <label>Main Subject Line: <span class="req" title="Required">*</span></label>
        <input required type="text" name="{{ $asset_type }}_main_subject_line" id="{{ $asset_type }}_main_subject_line" class="form-control" value=""/>
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Main Preheader Line: <span class="req" title="Required">*</span></label>
        <input required type="text" name="{{ $asset_type }}_main_preheader_line" id="{{ $asset_type }}_main_preheader_line" class="form-control" value=""/>
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Remail Subject Line:</label>
        <input type="text" name="{{ $asset_type }}_alt_subject_line" class="form-control" value=""/>
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Remail Preheader Line:</label>
        <input type="text" name="{{ $asset_type }}_alt_preheader_line" class="form-control" value=""/>
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Body Copy:</label>
        {!! Form::textarea($asset_type.'_body_copy', null, ['class' => 'form-control summernote']) !!}
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Secondary Message:</label>
        <textarea class="form-control" name="{{ $asset_type }}_secondary_message" rows="5" cols="100"></textarea>
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Click Through Links:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_click_through_links" class="form-control" placeholder="https://www.example.com" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>Email List To Use:</label>
        <a href="javascript:void(0)" class="kiss-info-icon" tabindex="-1" title="(CTRL or ⌘) + click to add multiple"></a>
        <select multiple name="{{ $asset_type }}_email_list[]" class="form-control" style="height:155px;">
            <?php
                $options = array(
                    'KISS Master List',
                    'KISS VIP List',
                    'imPRESS Master List',
                    'imPRESS VIP List',
                    'JOAH Master List',
                    'JOAH VIP List',
                    'Bejour Master List',
                    'Bejour VIP List',
                );
            ?>
            <?php foreach ($options as $option): ?>
                <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Email Blast Date: (Lead Time 20 Days)</label>
        <input required type="text" name="{{ $asset_type }}_email_blast_date" id="new_email_blast_date"
               class="form-control @error($asset_type.'_email_blast_date') is-invalid @enderror @if (!$errors->has($asset_type.'_email_blast_date') && old($asset_type.'_email_blast_date')) is-valid @endif"
               value="{{ old($asset_type . '_email_blast_date', null) }}">
    </div>

    <div class="form-group">
        <label>Video Link:</label> <span>(if to be featured)</span>
        <input type="text" name="{{ $asset_type }}_video_link" class="form-control" value=""/>
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
    // Lead time +20 days - Email Blast
    $(function() {
        // var lead_time = new Date();
        // lead_time.setDate(lead_time.getDate()+20);

        var count = 20;
        var d = new Date();
        count = count + (parseInt(count/5))*2;
        d.setDate(d.getDate() +count);
        if(d.getDay()>5) {  d.setDate(d.getDate()+ (d.getDay()-5)) ; }

        $('input[name="<?php echo $asset_type; ?>_email_blast_date"]').daterangepicker({
            singleDatePicker: true,
            minDate:d,
            daysOfWeekDisabled: [0,6],
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });
</script>
