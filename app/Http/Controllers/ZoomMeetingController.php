<?php

namespace App\Http\Controllers;

use App\Models\ClientProject;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProject;
use App\Models\Utility;
use App\Models\ZoomMeeting;
use App\Traits\ZoomMeetingTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\GoogleCalendar\Event;

class ZoomMeetingController extends Controller
{
    use ZoomMeetingTrait;
    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {

        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if (isset($objUser) && isset($currentWorkspace) && $objUser->type !== 'admin') {
            if ($objUser->getGuard() == 'client') {
                $meetings = ZoomMeeting::whereRaw("find_in_set(" . \Auth::user()->id . ",client_id)")->where('workspace_id', $currentWorkspace->id)->get();
            } elseif ($currentWorkspace->permission == 'Owner') {
                $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->where('workspace_id', $currentWorkspace->id)->get();
            } else {
                $meetings = ZoomMeeting::whereRaw("find_in_set(" . \Auth::user()->id . ",member_ids)")->where('workspace_id', $currentWorkspace->id)->get();
            }
            $this->statusUpdate($slug);

            return view('zoom_meeting.index', compact('meetings', 'currentWorkspace'));

        } else {

            return redirect()->back()->with('error', __("Permission Denied!!!."));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($slug)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $assign_pro_ids = UserProject::where('user_id', $objUser->id)->pluck('project_id');
        $projects = Project::with('task')->select(['name', 'id', 'workspace'])->whereIn('id', $assign_pro_ids)->where('workspace', $currentWorkspace->id)->pluck('name', 'id');

        return view('zoom_meeting.create', compact('projects', 'currentWorkspace'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($slug, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $client_ids = ClientProject::getByProjects($request->project_id);

        $data['topic'] = $request->title;
        $data['start_time'] = date('y:m:d H:i:s', strtotime($request->start_date));
        $data['duration'] = (int) $request->duration;
        $data['password'] = $request->password;
        $data['host_video'] = 0;
        $data['client_id'] = $client_ids;
        $data['participant_video'] = 0;
        $data['workspace'] = $slug;
        try {
            $meeting_create = $this->createmitting($data);
            \Log::info('Meeting');
            \Log::info((array) $meeting_create);
            if (isset($meeting_create['success']) && $meeting_create['success'] == true) {

                $meeting_id = isset($meeting_create['data']['id']) ? $meeting_create['data']['id'] : 0;
                $start_url = isset($meeting_create['data']['start_url']) ? $meeting_create['data']['start_url'] : '';
                $join_url = isset($meeting_create['data']['join_url']) ? $meeting_create['data']['join_url'] : '';
                $status = isset($meeting_create['data']['status']) ? $meeting_create['data']['status'] : '';


                $new = new ZoomMeeting();
                $new->title = $request->title;
                $new->workspace_id = $currentWorkspace->id;
                $new->meeting_id = $meeting_id;
                $new->client_id = implode(',', $client_ids);
                $new->project_id = $request->project_id;
                $new->member_ids = implode(',', $request->members);
                $new->start_date = date('y:m:d H:i:s', strtotime($request->start_date));
                $new->duration = $request->duration;
                $new->start_url = $start_url;
                $new->password = $request->password;
                $new->join_url = $join_url;
                $new->status = $status;
                $new->created_by = \Auth::user()->id;
                $new->save();


                if ($request->get('synchronize_type') == 'google_calender') {
                    $type = 'zoom_meeting';
                    $request1 = new ZoomMeeting();
                    $request1->title = $request->title;
                    $request1->start_date = $request->start_date;
                    $request1->end_date = date('m/d/y H:i', strtotime(+$request->duration . "minutes", strtotime($request->start_date)));
                    Utility::addCalendarData($request1, $type, $slug);
                }

                return redirect()->back()->with('success', __('Meeting created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Meeting not created.'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __("invalid token."));
        }
    }

    public function show($slug, $id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $ZoomMeeting = ZoomMeeting::where('id', $id)->where('workspace_id', $currentWorkspace->id)->first();

        if ($ZoomMeeting->workspace_id == $currentWorkspace->id) {

            return view('zoom_meeting.show', compact('ZoomMeeting'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function calender($slug, Request $request)
    {

        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $meetings = ZoomMeeting::whereRaw("find_in_set(" . \Auth::user()->id . ",client_id)")->where('workspace_id', $currentWorkspace->id)->get();
        } elseif ($currentWorkspace->permission == 'Owner') {
            $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->where('workspace_id', $currentWorkspace->id)->get();
        } else {
            $meetings = ZoomMeeting::whereRaw("find_in_set(" . \Auth::user()->id . ",member_ids)")->where('workspace_id', $currentWorkspace->id)->get();
        }

        $arrMeeting = [];
        foreach ($meetings as $meeting) {
            $arr['id'] = $meeting['id'];
            $arr['title'] = $meeting['title'];
            $arr['workspace_id'] = $meeting['workspace_id'];
            $arr['meeting_id'] = $meeting['meeting_id'];
            $arr['start'] = $meeting['start_date'];
            $arr['duration'] = $meeting['duration'];
            $arr['start_url'] = $meeting['start_url'];
            $arr['className'] = 'bg-red';

            if (\Auth::guard('client')->check()) {
                $arr['url'] = route('zoom_meetings.show', [$slug, $meeting['id']]);
            } else {
                $arr['url'] = route('zoom_meeting.show', [$slug, $meeting['id']]);
            }

            $arrMeeting[] = $arr;
        }

        $calandar = array_merge($arrMeeting);
        $calandar = str_replace('"[', '[', str_replace(']"', ']', json_encode($calandar)));

        return view('zoom_meeting.calender', compact('calandar', 'currentWorkspace', 'meetings'));
    }

    public function destroy($slug, ZoomMeeting $zoomMeeting, $id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $zoomMeeting = ZoomMeeting::where('id', $id)->where('workspace_id', $currentWorkspace->id)->delete();

        return redirect()->back()->with('success', __('Meeting deleted successfully.'));
    }
    public function statusUpdate($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $meetings = ZoomMeeting::where('workspace_id', $currentWorkspace->id)->pluck('meeting_id');

        try {
            foreach ($meetings as $meeting) {

                $data = $this->get($meeting, $slug);

                if (isset($data['data']) && !empty($data['data'])) {
                    $meeting = ZoomMeeting::where('meeting_id', $meeting)->update(['status' => $data['data']['status']]);
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __("Meeting does not exist"));
        }
    }

    public function export_event(Request $request, $slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $events = Event::get();

        foreach ($events as $value) {
            $check = Event::where('name', $value->summary)->where('start_date', $value->startDateTime)->where('end_date', $value->endDateTime)->first();
            if ($check) {
                $store = Event::where('id', $check->id)->first();
            } else {
                $store = new Event();
            }
            $store->name = $value->summary;
            $store->start_date = $value->startDateTime;
            $store->end_date = $value->endDateTime;
            $store->save();
        }
        return redirect()->route('new_calendar');
    }

    public function get_event_data($slug, Request $request)
    {

        $objUser = \Auth::user();

        $arrayJson = [];
        if ($request->get('calender_type') == 'google_calendar') {

            $type = 'zoom_meeting';
            $arrayJson = Utility::getCalendarData($slug, $type);
        } else {

            $currentWorkspace = Utility::getWorkspaceBySlug($slug);

            if ($objUser->getGuard() == 'client') {
                $meetings = ZoomMeeting::whereRaw("find_in_set(" . \Auth::user()->id . ",client_id)")->where('workspace_id', $currentWorkspace->id)->get();
            } elseif ($currentWorkspace->permission == 'Owner') {
                $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->where('workspace_id', $currentWorkspace->id)->get();
            } else {
                $meetings = ZoomMeeting::whereRaw("find_in_set(" . \Auth::user()->id . ",member_ids)")->where('workspace_id', $currentWorkspace->id)->get();
            }
            $arrMeeting = [];
            foreach ($meetings as $meeting) {
                $arr['id'] = $meeting['id'];
                $arr['title'] = $meeting['title'];
                $arr['workspace_id'] = $meeting['workspace_id'];
                $arr['meeting_id'] = $meeting['meeting_id'];
                $arr['start'] = $meeting['start_date'];
                $arr['duration'] = $meeting['duration'];
                $arr['start_url'] = $meeting['start_url'];
                $arr['className'] = 'bg-red';

                if (\Auth::guard('client')->check()) {
                    $arr['url'] = route('zoom_meetings.show', [$slug, $meeting['id']]);
                } else {
                    $arr['url'] = route('zoom_meeting.show', [$slug, $meeting['id']]);
                }
                $arrayJson[] = $arr;
            }
            $calandar = array_merge($arrMeeting);
            $calandar = str_replace('"[', '[', str_replace(']"', ']', json_encode($calandar)));
        }
        return $arrayJson;
    }
}
