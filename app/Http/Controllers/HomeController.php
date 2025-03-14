<?php

namespace App\Http\Controllers;

use App\Models\ClientProject;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Utility;
use App\Models\Workspace;
use Dflydev\DotAccessData\Util;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Facades\Module;
use Illuminate\Http\Request;
use Lab404\Impersonate\Models\Impersonate;


class HomeController extends Controller
{

public function testapi(){
     
   $data =  User::verifyEmail("kadirdurmazlar@gmail.com");
   return   view('testapi',compact('data'));
}

    public function landingPage()
    {
        if (!file_exists(storage_path() . "/installed")) {

            header('location:install');
            die;
        }
        $setting = Utility::getAdminPaymentSetting();

        // $currentWorkspace = Utility::getWorkspaceBySlug('');
        $paymentSetting = Utility::getAdminPaymentSetting();



        if ($setting['display_landing'] == 'on' && \Schema::hasTable('landing_page_settings')) {

            $plans = Plan::get();
            return view('landingpage::layouts.landingpage');
            // return view('layouts.landing', compact('plans', 'currentWorkspace', 'paymentSetting'));

        } else {

            return redirect('login');

        }
    }



    public function privacypage(){
     return view('landingpage::privacypage');

    }

public function becomepartner(){
     return view('landingpage::becomepartner');
     
}

    

    public function LoginWithAdmin(Request $request, User $user, $id)
    {
        $user = User::find($id);
        $from = \Auth::user();
        if ($user && auth()->check()) {
            $manager = app('impersonate');
            $manager->take($from, $user);

            return redirect('dashboard');
        }
    }

    public function ExitAdmin(Request $request)
    {
        Auth::user()->leaveImpersonation($request->user());
        return redirect('/home');
    }

    public function check()
    {
        $user = Auth::user();

        if ($user->type != 'admin') {
            $workspace = Workspace::find($user->currant_workspace);
            if ($workspace->created_by == $user->id) {
                if ($user->is_trial_done < 2 && !empty($user->plan_expire_date)) {
                    if ($user->is_trial_done == 1 && $user->plan_expire_date < date('Y-m-d')) {
                        $user->is_trial_done = 2;
                        $user->save();
                    }
                }

                if ((empty($user->plan_expire_date) || $user->plan_expire_date < date('Y-m-d')) && $user->plan != 1) {
                    $plan = Plan::find(1);
                    $user->assignPlan(1);
                    if (!empty($plan)) {
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                        Order::create([
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => 0,
                            'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'usd',
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $user->id,
                        ]);
                    }

                    //$error = $user->is_trial_done ? __('Your Plan is expired.') : ($user->plan_expire_date < date('Y-m-d') ? __('Please upgrade your plan') : '');

                    return redirect()->route('home')->with('success', __('Free Plan Activated Successfully.!'));
                }
            }
        }

        return redirect()->route('home');
    }


    public function index($slug = '')
    {

        $userObj = Auth::user();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($userObj->type == 'admin') {
            $totalUsers = User::where('type', '!=', 'admin')->count();
            $totalPaidUsers = User::where('type', '!=', 'admin')->where('plan', '!=', 1)->count();
            $totalOrderAmount = Order::where('payment_status', '=', 'succeeded')->sum('price');
            $totalOrders = Order::where('payment_status', '=', 'succeeded')->count();
            $totalPlans = Plan::count();
            $mostPlans = Plan::where('id', function ($query) {
                $query->select('plan_id')->from('orders')->groupBy('plan_id')->orderBy(\DB::raw('COUNT(plan_id)'))->limit(1);
            })->first();

            $chartData = $this->getOrderChart(['duration' => 'week']);


            return view('home', compact('totalUsers', 'totalPaidUsers', 'totalOrders', 'totalOrderAmount', 'totalPlans', 'mostPlans', 'chartData'));
        } elseif ($currentWorkspace) {
            $doneStage = Stage::where('workspace_id', '=', $currentWorkspace->id)->where('complete', '=', '1')->first();
            if ($userObj->getGuard() == 'client') {

                $totalProject = ClientProject::join("projects", "projects.id", "=", "client_projects.project_id")->where("client_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $totalBugs = ClientProject::join("bug_reports", "bug_reports.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $totalTask = ClientProject::join("tasks", "tasks.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->count();
                $completeTask = ClientProject::join("tasks", "tasks.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->where('tasks.status', '=', $doneStage->id)->count();
                $tasks = Task::select([
                    'tasks.*',
                    'stages.name as status',
                    'stages.complete',
                ])->join("client_projects", "tasks.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
                $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();
                $projectProcess = ClientProject::join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');

                $arrProcessPer = [];
                $arrProcessLabel = [];
                foreach ($projectProcess as $lable => $process) {
                    $arrProcessLabel[] = $lable;
                    if ($totalProject == 0) {
                        $arrProcessPer[] = 0.00;
                    } else {
                        $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
                    }
                }
                $arrProcessClass = [
                    'text-success',
                    'text-primary',
                    'text-danger',
                ];
                $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
                    'workspace_id' => $currentWorkspace->id,
                    'duration' => 'week',
                ]);

                $tasksUsers = Task::orderBy('tasks.id', 'desc')
                    ->join("client_projects", "tasks.project_id", "=", "client_projects.project_id")
                    ->join("projects", "projects.id", "=", "client_projects.project_id")
                    ->join("stages", "stages.id", "=", "tasks.status")
                    ->join("users", function ($join) {
                        $join->on("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(tasks.assign_to, ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -2), ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -1), ',', 1) AS SIGNED)"));
                    })
                    ->where("client_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->limit(5)
                    ->select(['users.name', 'users.id'])
                    ->distinct()
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray();
                return view('home', compact('currentWorkspace', 'totalProject', 'totalBugs', 'totalTask', 'totalMembers', 'arrProcessLabel', 'arrProcessPer', 'arrProcessClass', 'completeTask', 'tasks', 'chartData', 'tasksUsers'));

            } else {
                $totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();

                $workspace_owner = isset($currentWorkspace) ? $currentWorkspace->owner($currentWorkspace->id) : '';
                $users = User::find($workspace_owner->id);
                $plan = Plan::find($users->plan);
                $storage_limit = $plan->storage_limit > 0 ? (($users->storage_limit / $plan->storage_limit) * 100) : 0;
                $storage_limit = number_format($storage_limit, 2);
                if ($currentWorkspace->permission == 'Owner') {
                    $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                    $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                    $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.status', '=', $doneStage->id)->count();
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                        'tasks.assign_to as users',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
                } else {
                    $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('bug_reports.assign_to', '=', $userObj->id)->count();
                    $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->count();
                    $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->where('tasks.status', '=', $doneStage->id)->count();
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
                }

                $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();

                $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');


                $arrProcessPer = [];
                $arrProcessLabel = [];
                foreach ($projectProcess as $lable => $process) {
                    $arrProcessLabel[] = $lable;
                    if ($totalProject == 0) {
                        $arrProcessPer[] = 0.00;
                    } else {
                        $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
                    }
                }
                $arrProcessClass = [
                    'text-success',
                    'text-primary',
                    'text-danger',
                ];

                $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
                    'workspace_id' => $currentWorkspace->id,
                    'duration' => 'week',
                ]);

                $tasksUsers = Task::orderBy('tasks.id', 'desc')
                    ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->join("stages", "stages.id", "=", "tasks.status")
                    ->join("users", function ($join) {
                        $join->on("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(tasks.assign_to, ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -2), ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -1), ',', 1) AS SIGNED)"));
                    })
                    ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->limit(5)
                    ->select(['users.name', 'users.id'])
                    ->distinct()
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray();

                return view('home', compact('currentWorkspace', 'totalProject', 'totalBugs', 'totalTask', 'totalMembers', 'arrProcessLabel', 'arrProcessPer', 'arrProcessClass', 'completeTask', 'tasks', 'chartData', 'storage_limit', 'users', 'plan', 'tasksUsers'));
            }
        } else {
            return view('home', compact('currentWorkspace'));
        }
    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if (isset($arrParam['duration']) && $arrParam['duration'] == 'week') {

            $previous_week = Utility::getFirstSeventhWeekDay(-1);
            foreach ($previous_week['datePeriod'] as $dateObject) {
                $arrDuration[$dateObject->format('Y-m-d')] = $dateObject->format('D');
            }
        }

        $arrTask = [];
        $arrTask['label'] = [];
        $arrTask['data'] = [];
        foreach ($arrDuration as $date => $label) {
            $data = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][] = $data->total;
        }

        return $arrTask;
    }
}
