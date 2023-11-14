@component('mail::message')

# Hi {{ $details['who'] }},
<b>Today</b>
<span style="color:#ffffff; font-size: medium;background-color: #933434;border-radius: 6px;">
&nbsp;{{ $details['due'] }}&nbsp;
</span> &nbsp; is
<b style="color: #b91d19">{{ $details['asset_status'] }} Start Date</b> for Asset

@component('mail::table')
| TYPE          | Asset ID  |
| :------------: | :---------:|
| {{ $details['asset_type'] }}   | {{ $details['a_id'] }} |
@endcomponent

@component('mail::panel')
{{ $details['task_name'] }}
@endcomponent

@component('mail::button', ['url' => url($details['url']),'color' => 'error'])
Go to Asset
@endcomponent

Thanks,<br>
REEMARC

<small><i>This email address will not receive replies. If you have any questions, please contact Mo Tuhin or Vincent "Vinny" Cerone.</i></small>
@endcomponent
