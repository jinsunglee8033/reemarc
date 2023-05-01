<?php

namespace App\Repositories\Admin;

use Carbon\Carbon;
use DB;

use App\Repositories\Admin\Interfaces\CampaignAssetIndexRepositoryInterface;

use App\Models\CampaignAssetIndex;
use Illuminate\Database\Eloquent\Model;

class CampaignAssetIndexRepository implements CampaignAssetIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $campaignAssetIndex = new CampaignAssetIndex();

        if ($id) {
            $campaignAssetIndex = $campaignAssetIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $campaignAssetIndex = $campaignAssetIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $campaignAssetIndex->paginate($perPage);
        }

        $campaignAssetIndex = $campaignAssetIndex->get();

        return $campaignAssetIndex;
    }

    public function findById($id)
    {
        return campaignAssetIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $campaignAssetIndex = campaignAssetIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $campaignAssetIndex;
        });
    }

    public function update($id, $params = [])
    {
        $campaignAssetIndex = campaignAssetIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $campaignAssetIndex) {
            $campaignAssetIndex->update($params);

            return $campaignAssetIndex;
        });
    }

    public function delete($id)
    {
        $campaignAssetIndex  = campaignAssetIndex::findOrFail($id);

        return $campaignAssetIndex->delete();
    }

    public function deleteByCampaignId($c_id)
    {
        $campaignAssetIndex =  new campaignAssetIndex();
        $obj = $campaignAssetIndex->where('campaign_id', $c_id)->delete();
        return $obj;
    }

    public function get_assets_final_approval_by_campaing_id($c_id)
    {
        $campaignAssetIndex =  new campaignAssetIndex();
        $obj = $campaignAssetIndex->where('campaign_id', $c_id)->get();
        return $obj;
    }

    public function get_complete_assets_list($str, $asset_id, $campaign_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status = "copy_complete"
            and cai.team_to = "creative"
            ' . $filter_1 . $filter_2 . $filter_3 . '
            order by due asc');
    }

    public function get_kpi_assets_list($str, $asset_id, $campaign_id, $designer)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';
        $filter_4 = !empty($designer) ? ' and cai.assignee = "'.$designer.'" ' : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.assignee,
                    cai.assigned_at,
                    cai.target_at,
                    cai.delay,
                    cai.start_at,
                    cai.done_at,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status in ("final_approval")
            and cai.team_to = "creative"
            and cai.target_at is not null
            and cai.assignee is not null
            ' . $filter_1 . $filter_2 . $filter_3 . $filter_4 . '
            order by due desc');
    }

    public function get_request_assets_list_copy($str, $asset_id, $campaign_id, $brand_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';
        $filter_4 = !empty($brand_id) ? ' and ci.campaign_brand ='.$brand_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    cb.campaign_name as brand,
                    ci.campaign_brand as brand_id,
                    due,
                    ci.name as name,
                    cai.team_to,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_requested"
            ' . $filter_1 . $filter_2 . $filter_3 . $filter_4 . '
            order by due asc');
    }

    public function get_complete_assets_list_content($str, $asset_id, $campaign_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status = "copy_complete"
            and cai.team_to = "content"
            ' . $filter_1 . $filter_2 . $filter_3 . '
            order by due asc');
    }

    public function get_complete_assets_list_web($str, $asset_id, $campaign_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status = "copy_complete"
            and cai.team_to = "web production"
            ' . $filter_1 . $filter_2 . $filter_3 . '
            order by due asc');
    }

    public function get_asset_jira_todo($str)
    {
        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "to_do"
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_progress($str)
    {
        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "in_progress"
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_done($str)
    {
        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "done"
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_finish_creative($str, $brand_id, $asset_id, $team_to = null)
    {

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.updated_at
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "final_approval"
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            and cai.updated_at >= DATE_ADD(CURDATE(), INTERVAL -7 DAY)
            order by updated_at asc');
    }

    public function get_asset_jira_copy_request($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {

        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_requested"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
              ' . $copy_writer_filter . '
            and date_created > "2022-01-01 00:00:00"
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_copy_to_do($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {

        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_to_do"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
              ' . $copy_writer_filter . '
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_copy_in_progress($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {

        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_in_progress"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
              ' . $copy_writer_filter . '
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_copy_request_copywriter($brand_id)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cb.id as brand_id
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_requested"
              ' . $brand_filter . '
            and ci.name is not null
            order by due asc');
    }

    public function get_asset_jira_copy_review($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {
        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_review"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
              ' . $copy_writer_filter . '
            and date_created > "2021-01-01 00:00:00"
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_copy_complete($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_complete"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
            and date_created > "2022-03-01 00:00:00"
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_to_do($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status in ("to_do")
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_to_do_creative($str, $brand_id, $asset_id, $team_to = null)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status in ("to_do")
            and ci.name is not null
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_in_progress($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status in ("in_progress")
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_in_progress_creative($str, $brand_id, $asset_id, $team_to = null)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status in ("in_progress")
            and ci.name is not null
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_waiting_final_approval($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "done"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $team_filter . '
              ' . $asset_id_filter . '
            and u.first_name like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_waiting_final_approval_creative($str, $brand_id, $asset_id, $team_to = null)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "done"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_asset_completed($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cai.author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cb.campaign_name,
                    cai.updated_at,
                    cai.team_to
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "final_approval"
                ' . $brand_filter . '
                ' . $team_filter . '
                ' . $asset_id_filter . '
            and u.first_name like "%'.$str.'%"
            and cai.updated_at >= DATE_ADD(CURDATE(), INTERVAL -14 DAY)
            order by updated_at asc');
    }

    public function get_target_date($a_id, $asset_type)
    {
        $res = DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
					select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.id ='.$a_id);

        $due = $res[0]->due;

        // Get Target_At
        if ($asset_type == 'email_blast') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-10 weekday'));
        } else if ($asset_type == 'social_ad') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-10 weekday'));
        } else if ($asset_type == 'website_banners') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-11 weekday'));
        } else if ($asset_type == 'landing_page') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-18 weekday'));
        } else if ($asset_type == 'misc') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-9 weekday'));
        } else if ($asset_type == 'sms_request') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-9 weekday'));
        } else if ($asset_type == 'programmatic_banners') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-10 weekday'));
        } else if ($asset_type == 'image_request') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-2 weekday'));
        } else if ($asset_type == 'roll_over') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-3 weekday'));
        } else if ($asset_type == 'store_front') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-13 weekday'));
        } else if ($asset_type == 'a_content') {
            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-13 weekday'));
        }

        // Check for Assign is late or not..
        $today = date('Y-m-d');
        if ($asset_type == 'email_blast') {
            $assign_due = date('Y-m-d', strtotime($due . '-22 weekday'));
        } else if ($asset_type == 'social_ad') {
            $assign_due = date('Y-m-d ', strtotime($due . '-22 weekday'));
        } else if ($asset_type == 'website_banners') {
            $assign_due = date('Y-m-d', strtotime($due . '-23 weekday'));
        } else if ($asset_type == 'landing_page') {
            $assign_due = date('Y-m-d', strtotime($due . '-40 weekday'));
        } else if ($asset_type == 'misc') {
            $assign_due = date('Y-m-d', strtotime($due . '-21 weekday'));
        } else if ($asset_type == 'sms_request') {
            $assign_due = date('Y-m-d', strtotime($due . '-21 weekday'));
        } else if ($asset_type == 'programmatic_banners') {
            $assign_due = date('Y-m-d', strtotime($due . '-22 weekday'));
        } else if ($asset_type == 'image_request') {
            $assign_due = date('Y-m-d', strtotime($due . '-14 weekday'));
        } else if ($asset_type == 'roll_over') {
            $assign_due = date('Y-m-d', strtotime($due . '-15 weekday'));
        } else if ($asset_type == 'store_front') {
            $assign_due = date('Y-m-d', strtotime($due . '-35 weekday'));
        } else if ($asset_type == 'a_content') {
            $assign_due = date('Y-m-d', strtotime($due . '-35 weekday'));
        }

        $delay = 0;

        if($today >= $assign_due){
            $from = Carbon::parse($today);
            $to = Carbon::parse($assign_due);
            $delay = $to->diffInWeekdays($from);
        }

        return [$target_at, $delay];
    }
}
