@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>Asset Approval</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/asset_approval') }}">Asset Approval</a></div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Asset Detail | Project ID : {{ $c_id }} | Asset ID : {{ $asset_id }} | {{ ucwords(str_replace('_', ' ', $a_type)) }}</h2>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <p style="float: right">Asset Creator : {{ $asset_creator }}</p>
                            <?php $data = [$asset_detail, $asset_files, $asset_obj['status'], $asset_obj['decline_creative'], $asset_obj['decline_kec'], $asset_obj['decline_copy'], $asset_obj['assignee'], $asset_obj['team_to']]; ?>
                            @include('admin.campaign.asset.'.$a_type, $data)
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <form method="POST" action="{{ route('asset.assign') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Assignee {{ $asset_obj['team_to'] }}</label>
                                    <select class="form-control" name="assignee">
                                        <option value="">Select</option>
                                        <?php if($asset_obj['team_to'] == 'content'){?>
                                            @foreach ($assignees_content as $designer)
                                                <option value="{{ $designer->first_name }}">
                                                    {{ $designer->first_name }}
                                                </option>
                                            @endforeach
                                        <?php }else if($asset_obj['team_to'] == 'web production'){?>
                                            @foreach ($assignees_web as $designer)
                                                <option value="{{ $designer->first_name }}">
                                                    {{ $designer->first_name }}
                                                </option>
                                            @endforeach
                                        <?php }else{?>
                                            @foreach ($assignees_designer as $designer)
                                                <option value="{{ $designer->first_name }}">
                                                    {{ $designer->first_name }}
                                                </option>
                                            @endforeach
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="a_id" value="{{ $asset_id }}">
                            <input type="hidden" name="c_id" value="{{ $c_id }}">
                            <input type="hidden" name="a_type" value="{{ $a_type }}">

                            <div class="card-footer text-right">
                                <button class="btn btn-primary">Assign</button>
                            </div>
                        </form>
                    </div>

                    <div class="card">
                        <form method="POST" action="{{ route('asset.decline_creative') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Decline Reason from Creator:</label>
                                    <textarea class="form-control" id="decline_creative" name="decline_creative" rows="15" cols="100" style="min-height: 200px;"></textarea>
                                </div>
                            </div>

                            <input type="hidden" name="a_id" value="{{ $asset_id }}">
                            <input type="hidden" name="c_id" value="{{ $c_id }}">
                            <input type="hidden" name="a_type" value="{{ $a_type }}">

                            <div class="card-footer text-right">
                                <button class="btn btn-primary">Decline</button>
                            </div>
                        </form>
                    </div>

                </div>


            </div>
        </div>

    </section>



@endsection
