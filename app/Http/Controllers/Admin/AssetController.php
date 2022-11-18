<?php

namespace App\Http\Controllers\Admin;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\AssetEmailBlastRequest;
use App\Http\Requests\Admin\AssetLandingPageRequest;
use App\Http\Requests\Admin\AssetMiscRequest;
use App\Http\Requests\Admin\AssetProgrammaticBannersRequest;
use App\Http\Requests\Admin\AssetSocialAdRequest;
use App\Http\Requests\Admin\AssetTopcategoriesCopyRequest;
use App\Http\Requests\Admin\AssetWebsiteBannersRequest;
use App\Http\Requests\Admin\AssetWebsiteChangesRequest;
use App\Http\Requests\Admin\CampaignRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Mail\AssetMessage;
use App\Mail\Todo;
use App\Models\CampaignAssetIndex;
use App\Models\CampaignNotes;

use App\Models\CampaignTypeAssetAttachments;
use App\Models\CampaignTypeEmailBlast;
use App\Models\CampaignTypeLandingPage;
use App\Models\CampaignTypeMisc;
use App\Models\CampaignTypeProgrammaticBanners;
use App\Models\CampaignTypeSocialAd;
use App\Models\CampaignTypeTopcategoriesCopy;
use App\Models\CampaignTypeWebsiteBanners;
use App\Models\CampaignTypeWebsiteChanges;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\CampaignAssetIndexRepository;
use App\Repositories\Admin\CampaignNotesRepository;
use App\Repositories\Admin\CampaignRepository;
use App\Repositories\Admin\CampaignBrandsRepository;
use App\Repositories\Admin\CampaignTypeAContentRepository;
use App\Repositories\Admin\CampaignTypeAssetAttachmentsRepository;
use App\Repositories\Admin\CampaignTypeEmailBlastRepository;
use App\Repositories\Admin\CampaignTypeImageRequestRepository;
use App\Repositories\Admin\CampaignTypeLandingPageRepository;
use App\Repositories\Admin\CampaignTypeMiscRepository;
use App\Repositories\Admin\CampaignTypeProgrammaticBannersRepository;
use App\Repositories\Admin\CampaignTypeRollOverRepository;
use App\Repositories\Admin\CampaignTypeSocialAdRepository;
use App\Repositories\Admin\CampaignTypeStoreFrontRepository;
use App\Repositories\Admin\CampaignTypeTopcategoriesCopyRepository;
use App\Repositories\Admin\CampaignTypeWebsiteBannersRepository;
use App\Repositories\Admin\CampaignTypeWebsiteChangesRepository;
use App\Repositories\Admin\Interfaces\CampaignAssetIndexRepositoryInterface;
use App\Repositories\Admin\PermissionRepository;

use App\Repositories\Admin\Interfaces\CampaignBrandsRepositoryInterface;
use App\Repositories\Admin\Interfaces\CampaignNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Admin\Interfaces\PermissionRepositoryInterface;

use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Log;

use Mail;

class AssetController extends Controller
{

    private $permissionRepository;
    private $campaignRepository;
    private $campaignBrandsRepository;
    private $campaignNotesRepository;
    private $campaignTypeAssetAttachmentsRepository;
    private $campaignTypeEmailBlastRepository;
    private $campaignTypeSocialAdRepository;
    private $campaignTypeWebsiteBannersRepository;
    private $campaignTypeWebsiteChangesRepository;
    private $campaignTypeLandingPageRepository;
    private $campaignTypeMiscRepository;
    private $campaignTypeTopcategoriesCopyRepository;
    private $campaignTypeProgrammaticBannersRepository;
    private $campaignTypeImageRequestRepository;
    private $campaignTypeRollOverRepository;
    private $campaignTypeStoreFrontRepository;
    private $campaignTypeAContentRepository;
    private $userRepository;
    private $assetNotificationUserRepository;
    private $campaignAssetIndexRepository;

    public function __construct(CampaignRepository $campaignRepository,
                                CampaignBrandsRepository $campaignBrandsRepository,
                                CampaignNotesRepository $campaignNotesRepository,
                                CampaignTypeAssetAttachmentsRepository $campaignTypeAssetAttachmentsRepository,
                                CampaignTypeEmailBlastRepository $campaignTypeEmailBlastRepository,
                                CampaignTypeSocialAdRepository $campaignTypeSocialAdRepository,
                                CampaignTypeWebsiteBannersRepository $campaignTypeWebsiteBannersRepository,
                                CampaignTypeWebsiteChangesRepository $campaignTypeWebsiteChangesRepository,
                                CampaignTypeLandingPageRepository $campaignTypeLandingPageRepository,
                                CampaignTypeMiscRepository $campaignTypeMiscRepository,
                                CampaignTypeTopcategoriesCopyRepository $campaignTypeTopcategoriesCopyRepository,
                                CampaignTypeProgrammaticBannersRepository $campaignTypeProgrammaticBannersRepository,
                                CampaignTypeImageRequestRepository $campaignTypeImageRequestRepository,
                                CampaignTypeRollOverRepository $campaignTypeRollOverRepository,
                                CampaignTypeStoreFrontRepository $campaignTypeStoreFrontRepository,
                                CampaignTypeAContentRepository $campaignTypeAContentRepository,
                                CampaignAssetIndexRepository $campaignAssetIndexRepository,
                                UserRepository $userRepository,
                                AssetNotificationUserRepository $assetNotificationUserRepository,
                                PermissionRepository $permissionRepository)
    {
        parent::__construct();

        $this->campaignRepository = $campaignRepository;
        $this->campaignBrandsRepository = $campaignBrandsRepository;
        $this->campaignNotesRepository = $campaignNotesRepository;
        $this->campaignTypeAssetAttachmentsRepository = $campaignTypeAssetAttachmentsRepository;
        $this->campaignTypeEmailBlastRepository = $campaignTypeEmailBlastRepository;
        $this->campaignTypeSocialAdRepository = $campaignTypeSocialAdRepository;
        $this->campaignTypeWebsiteBannersRepository = $campaignTypeWebsiteBannersRepository;
        $this->campaignTypeWebsiteChangesRepository = $campaignTypeWebsiteChangesRepository;
        $this->campaignTypeLandingPageRepository = $campaignTypeLandingPageRepository;
        $this->campaignTypeMiscRepository = $campaignTypeMiscRepository;
        $this->campaignTypeTopcategoriesCopyRepository = $campaignTypeTopcategoriesCopyRepository;
        $this->campaignTypeProgrammaticBannersRepository = $campaignTypeProgrammaticBannersRepository;
        $this->campaignTypeImageRequestRepository = $campaignTypeImageRequestRepository;
        $this->campaignTypeRollOverRepository = $campaignTypeRollOverRepository;
        $this->campaignTypeStoreFrontRepository = $campaignTypeStoreFrontRepository;
        $this->campaignTypeAContentRepository = $campaignTypeAContentRepository;
        $this->campaignAssetIndexRepository = $campaignAssetIndexRepository;
        $this->userRepository = $userRepository;
        $this->assetNotificationUserRepository =$assetNotificationUserRepository;
        $this->permissionRepository = $permissionRepository;

    }

    public function index(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset';
        return view('admin.asset.index', $this->data);
    }

    public function asset_approval(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_approval';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';

        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_complete_assets_list($str, $asset_id, $campaign_id);
        $this->data['filter'] = $params;

        return view('admin.asset.approval', $this->data);
    }

    public function asset_detail($a_id, $c_id, $a_type)
    {
        $this->data['currentAdminMenu'] = 'asset_approval';
        $this->data['asset_id'] = $a_id;
        $this->data['a_type'] = $a_type;
        $this->data['c_id'] = $c_id;
        $this->data['asset_detail'] = $asset_detail = $this->campaignRepository->get_asset_detail($a_id, $c_id, $a_type);
        $author_id = $asset_detail[0]->author_id;
        $user_obj = $this->userRepository->findById($author_id);
        $this->data['asset_creator'] = $user_obj->first_name . ' ' . $user_obj->last_name;
        $this->data['asset_files'] = $this->campaignTypeAssetAttachmentsRepository->findAllByAssetId($a_id);

        $params['role'] = 'graphic designer';
        $options = [
            'order' => [
                'first_name' => 'asc',
            ],
            'filter' => $params,
        ];

        $this->data['assignees'] = $this->userRepository->findAll($options);

        return view('admin.asset.detail', $this->data);
    }

    public function asset_assign(Request $request)
    {

        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['campaign_id'] = $param['c_id'];
        $params['type'] = $param['a_type'];
        $params['status'] = 'to_do';
        $params['assignee'] = $param['assignee'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' has been Assigned to ' . $params['assignee'] , null);

        // TODO notification
        $notify = new NotifyController();
        $notify->to_do($c_id, $param['a_id'], $param['assignee']);
//        Log::info('email sent!!');
        ////////////
        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = ucwords(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/asset_approval')
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been Approved and Assigned to '.$param['assignee'].'.'));
    }

    public function asset_assign_change(Request $request)
    {

        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['campaign_id'] = $param['c_id'];
        $params['type'] = $param['a_type'];
        $params['status'] = 'to_do';
        $params['assignee'] = $param['assignee'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' has been Assigned to ' . $params['assignee'] , null);

        // TODO notification
        $notify = new NotifyController();
        $notify->to_do($c_id, $param['a_id'], $param['assignee']);
//        Log::info('email sent!!');
        ////////////
        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = ucwords(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$c_id.'/edit#'.$param['a_id'])
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been Assigned to '.$param['assignee'].'.'));
    }

    public function asset_decline_copy(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['status'] = 'copy_requested';
        $params['decline_copy'] = $param['decline_copy'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], 'Decline From Copy Review', $params['decline_copy']);

        // TODO notification
        // Send notification to copywriter via email
        // Do action - decline from copy
        $notify = new NotifyController();
        $notify->decline_from_copy($c_id, $param['a_id'], $params);

        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = ucwords(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/asset_approval')
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been Declined.'));
    }

    public function asset_decline_creative(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['status'] = 'copy_review';
        $params['decline_creative'] = $param['decline_creative'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], 'Decline from Creative', $params['decline_creative']);

        // TODO notification
        // Send notification to task creator via email
        // Do action - decline from creative
        $notify = new NotifyController();
        $notify->decline_from_creative($c_id, $param['a_id'], $params);

        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = ucwords(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/asset_approval')
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been Declined.'));
    }

    public function asset_decline_kec(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $params['c_id'] = $param['c_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['status'] = 'to_do';
        $params['decline_kec'] = $param['decline_kec'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], 'Decline from KEC', $params['decline_kec']);

        // TODO notification
        // send notification to designer via email
        // Do action - decline from KEC
        $notify = new NotifyController();
        $notify->decline_from_kec($c_id, $param['a_id'], $params);

        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = ucwords(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$params['c_id'].'/edit')
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been Declined.'));
    }

    public function asset_jira(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'asset_jira';

//        $user = auth()->user();
//        if($user->team == 'Creative' && $user->role == 'graphic designer') {
//            if(isset($_GET['q'])){ // if search with nothing..
//                $str = $param['q'];
//            }else{ // if come first time..
//                $str = $param['q'] = $user->first_name;
//            }
//        }else{
//            $str = !empty($param['q']) ? $param['q'] : '';
//        }


        $params['role'] = 'graphic designer';
        $options = [
            'order' => [
                'first_name' => 'asc',
            ],
            'filter' => $params,
        ];

        $this->data['designers'] = $this->userRepository->findAll($options);

        if(isset($_GET['designer'])){
            $designer = $param['designer'];
        }else{
            $designer = '';
        }

        if(isset($_GET['brand'])) {
            $brand_id = $param['brand'];
        }else{
            $brand_id = !empty($param['brand']) ? $param['brand'] : '';
        }

        if(isset($_GET['asset_id'])) {
            $asset_id = $param['asset_id'];
        }else{
            $asset_id = !empty($param['asset_id']) ? $param['asset_id'] : '';
        }

        $this->data['brand'] = $brand_id;
        $this->data['designer'] = $designer;
        $this->data['asset_id'] = $asset_id;

        $this->data['filter'] = $param;

        $this->data['asset_list_todo'] = $this->campaignAssetIndexRepository->get_asset_jira_to_do_creative($designer, $brand_id, $asset_id);
        $this->data['asset_list_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_in_progress_creative($designer, $brand_id, $asset_id);
        $this->data['asset_list_done'] = $this->campaignAssetIndexRepository->get_asset_jira_waiting_final_approval_creative($designer, $brand_id, $asset_id);
        $this->data['asset_list_finish'] = $this->campaignAssetIndexRepository->get_asset_jira_finish_creative($designer, $brand_id, $asset_id);

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        return view('admin.asset.jira', $this->data);
    }

    public function asset_jira_kec(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'asset_jira_kec';

        $user = auth()->user();

        if($user->team == 'KEC' && $user->role != 'copywriter') {
            if(isset($_GET['q'])){ // if search with nothing..
                $str = $param['q'];
            }else{ // if come first time..
                $str = $param['q'] = $user->first_name;
            }
        }else{
            $str = !empty($param['q']) ? $param['q'] : '';
        }

        if(isset($_GET['brand'])) {
            $brand_id = $param['brand'];
        }else{
            $brand_id = !empty($param['brand']) ? $param['brand'] : '';
        }

        if(isset($_GET['asset_id'])) {
            $asset_id = $param['asset_id'];
        }else{
            $asset_id = !empty($param['asset_id']) ? $param['asset_id'] : '';
        }

        $this->data['brand_'] = $brand_id;
        $this->data['asset_id'] = $asset_id;

        $this->data['filter'] = $param;
        $this->data['asset_list_copy_request'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_request($str, $brand_id, $asset_id);
        $this->data['asset_list_copy_review'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_review($str, $brand_id, $asset_id);
        $this->data['asset_list_copy_complete'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_complete($str, $brand_id, $asset_id);
        $this->data['asset_list_to_do'] = $this->campaignAssetIndexRepository->get_asset_jira_to_do($str, $brand_id, $asset_id);
        $this->data['asset_list_in_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_in_progress($str, $brand_id, $asset_id);
        $this->data['asset_list_waiting_final_approval'] = $this->campaignAssetIndexRepository->get_asset_jira_waiting_final_approval($str, $brand_id, $asset_id);

//        $this->data['asset_list_waiting_asset_completed'] = $this->campaignAssetIndexRepository->get_asset_jira_asset_completed($str, $brand_id);

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        return view('admin.asset.jira_kec', $this->data);
    }

    public function asset_jira_copywriter(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_jira_copywriter';
        $this->data['asset_list_copy_request'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_request_copywriter();
        $this->data['brands_assigned_copywriters'] = $this->userRepository->getBrandsAssignedWriters();

        return view('admin.asset.jira_copywriter', $this->data);
    }

    public static function get_writers_by_brand($brand)
    {
        $rs = UserRepository::getWritersNameByBrand($brand);
        $list = '';
        foreach ($rs as $writer){
            $list .= $writer->first_name . ' ';
        }
        return $list;
    }

    public function copyReview($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'copy_review';
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, 'Copy Review', null);

            // TODO notification
            // send email to asset creator
            // Do action - copy review
            // email asset creator
            $notify = new NotifyController();
            $notify->copy_review($c_id, $a_id);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }

    }

    public function copyComplete($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        if($campaignAssetIndex->type == 'topcategories_copy'){ // Top Category Copy should not go to Creative. -> go to Done.
            $this->finalApproval($id);
            return;
        }else{
            $param['status'] = 'copy_complete';
        }
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, 'Copy Completed', null);

            // TODO notification
            // send email to creative director
            // Do action - copy complete
            $notify = new NotifyController();
            $notify->copy_complete($c_id, $a_id);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }

    }

    public function add_asset_correspondence($c_id, $asset_type, $asset_id, $status, $decline)
    {
        // Insert into campaign note for correspondence
        $user = auth()->user();
        $asset_type_ =  ucwords(str_replace('_', ' ', $asset_type));
        $change_line  = "<p>$user->first_name $status for $asset_type_ (#$asset_id)</p>";

        if(!empty($decline)) {
            $change_line .= "<div class='change_label'><p>Decline Reason:</p></div>"
                . "<div class='change_to'><p>$decline</p></div>";
        }
        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $c_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['asset_id'] = $asset_id;
        $campaign_note['type'] = $asset_type;
        $campaign_note['note'] = $change_line;
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();
    }

    public function inProgress($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, 'In Progress', null);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }

    }

    public function done($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'done';
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, 'Done', null);

            // TODO notification
            // send notification to Asset creator via email
            // Do action - for final approval
            $notify = new NotifyController();
            $notify->final_approval($c_id, $a_id);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }
    }

    public function finalApproval($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'final_approval';
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, 'Final Approval', null);

            if($this->check_all_approval($c_id)){
                $param['status'] = 'archived';
                $param['updated_at'] = Carbon::now();
                $this->campaignRepository->update($c_id, $param);
            }

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }
    }

    public function check_all_approval($c_id)
    {
        $approval_assets = $this->campaignAssetIndexRepository->get_assets_final_approval_by_campaing_id($c_id);

        foreach ($approval_assets as $asset){
            if($asset->status != 'final_approval'){
                return false;
            }
        }

        return true;
    }

    public function asset_notification_user(Request $request)
    {
        $param = $request->all();

        $c_id = $param['c_id'];
        $params['asset_id'] = $param['a_id'];

        if (isset($param['user_id_list'])) {
            $params['user_id_list'] = implode(', ', $param['user_id_list']);
        } else {
            $params['user_id_list'] = '';
        }

        $params['updated_at'] = Carbon::now();

        if($this->assetNotificationUserRepository->getByAssetId($params['asset_id'])->count() !== 0){
            $this->assetNotificationUserRepository->update($params['asset_id'], $params);
        }else{
            $this->assetNotificationUserRepository->create($params);
        }

        $this->data['currentAdminMenu'] = 'campaign';

        return redirect('admin/campaign/'.$c_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function asset_add_note(Request $request)
    {
        $param = $request->all();
        $user = auth()->user();

        $c_id = $param['c_id'];
        $email_list = $param['email_list'];

        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $c_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['type'] = 'note';
        $campaign_note['note'] = $param['create_note'];
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();

        $new_note = preg_replace("/<p[^>]*?>/", "", $param['create_note']);
        $new_note = str_replace("</p>", "\r\n", $new_note);

        if($email_list){
            $details = [
                'who' => $user->first_name,
                'c_id' => $c_id,
                'message' => $new_note,
                'url' => '/admin/campaign/'.$c_id.'/edit',
            ];
            //send to receivers
            $cc_list = explode(',', $email_list);
            Mail::to($user->email)->cc($cc_list)->send(new AssetMessage($details));
        }

        $this->data['currentAdminMenu'] = 'campaign';

        return redirect('admin/campaign/'.$c_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

}
