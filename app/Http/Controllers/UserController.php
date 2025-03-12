<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BugComment;
use App\Models\BugFile;
use App\Models\BugReport;
use App\Models\Calendar;
use App\Models\ClientProject;
use App\Models\ClientWorkspace;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Events;
use App\Models\Mail\SendLoginDetail;
use App\Models\Mail\SendWorkspaceInvication;
use App\Models\Message;
use App\Models\Milestone;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Project;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\Tax;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\UserEmailTemplate;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Utility;
use App\Models\Workspace;
use App\Models\EmailTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\LoginDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    // public function companyInfo(Request $request,$id){
    //   $companyId = $id;
    //   $companyWorkspace = Workspace::where('created_by','=',$companyId)->get();
    //   foreach($companyWorkspace as $workspace){
    //     $workspaceUser = UserWorkspace::where([
    //         ['workspace_id', '=', $workspace->id],
    //         ['permission', '!=', 'Owner']
    //     ])->get();
    //   }
    //   return view('users.companyinfo',compact('companyWorkspace','workspaceUser'));

    public function companyInfo(Request $request, $id)
    {
        if (!empty($id)) {
            $data = $this->Counter($id);
            if ($data['is_success']) {
                $users_data = $data['response']['users_data'];
                $workspce_data = $data['response']['workspce_data'];
                return view('users.companyinfo', compact('id', 'users_data', 'workspce_data'));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function Counter($id)
    {
        $response = [];
        if (!empty($id)) {
            $workspces = Workspace::where('created_by', $id)
                ->selectRaw('COUNT(*) as total_workspace, SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as disable_workspace, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_workspace')
                ->first();
            $workspaces = WorkSpace::where('created_by', $id)->get();
            $users_data = [];
            foreach ($workspaces as $workspce) {
                // $users = User::where('created_by', $id)->where('workspace_id', $workspce->id)->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();
                $users = UserWorkspace::where('workspace_id', $workspce->id)->where('permission', '!=', 'Owner')->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users')->first();

                $users_data[$workspce->name] = [
                    'workspace_id' => $workspce->id,
                    'total_users' => !empty($users->total_users) ? $users->total_users : 0,
                    'disable_users' => !empty($users->disable_users) ? $users->disable_users : 0,
                    'active_users' => !empty($users->active_users) ? $users->active_users : 0,
                ];
            }
            $workspce_data = [
                'total_workspace' => $workspces->total_workspace,
                'disable_workspace' => $workspces->disable_workspace,
                'active_workspace' => $workspces->active_workspace,
            ];
            $response['users_data'] = $users_data;
            $response['workspce_data'] = $workspce_data;

            return [
                'is_success' => true,
                'response' => $response,
            ];
        }
        return [
            'is_success' => false,
            'error' => 'Plan is deleted.',
        ];
    }

    public function UserUnable(Request $request)
    {
        if (!empty($request->id) && !empty($request->company_id)) {
            if ($request->name == 'user') {
                if ($request->is_disable == 0) {

                    $userWorkspace = UserWorkspace::where('id', $request->id)->first();
                    $userWorkspace->is_active = $request->is_disable;
                    $userWorkspace->save();

                    $user = User::where('id', $request->user_id)->first();
                    $remaningUserWorkspace = UserWorkspace::where('user_id', $request->user_id)->where('is_active', 1)->get();
                    if (count($remaningUserWorkspace) == 0) {
                        $userWorkspace->is_active = $request->is_disable;
                        $userWorkspace->save();
                    } else {
                        $getFirstWorkspace = UserWorkspace::where('user_id', $request->user_id)->where('is_active', 1)->first();
                        $user->currant_workspace = $getFirstWorkspace->workspace_id;
                        $user->save();
                    }
                }

                if ($request->is_disable == 1) {
                    $userWorkspace = UserWorkspace::where('id', $request->id)->first();
                    $userWorkspace->is_active = $request->is_disable;
                    $userWorkspace->save();

                    $user = User::where('id', $request->user_id)->first();
                    $user->currant_workspace = $userWorkspace->workspace_id;
                    $user->save();
                }


                // $remaningUserWorkspace = UserWorkspace::where('id',$request->id)->where('is_active',1);
                $data = $this->Counter($request->company_id);
            } elseif ($request->name == 'workspace') {
                $companyAllWorkspaces = Workspace::where('created_by', $request->company_id)->where('is_active', 1)->get();
                if ($request->is_disable == 0) {

                    // $all = UserWorkspace::where('workspace_id',$request->id)->where('permission','member')->get();
                    if (count($companyAllWorkspaces) > 1) {
                        $currentWorkspace = Workspace::where('id', $request->id)->first();
                        $currentWorkspace->is_active = $request->is_disable;
                        $currentWorkspace->save();

                        $users = UserWorkspace::where('workspace_id', $currentWorkspace->id)
                            ->where('permission', 'Member')
                            ->get();
                        foreach ($users as $user) {
                            $user->is_active = $request->is_disable;
                            $user->save();

                            $userInOtherWorkspace = UserWorkspace::where('user_id', $user->user_id)->where('permission', 'member')->where('is_active', 1)->get();
                            // dump(count($userInOtherWorkspace));
                            $otherFirstWorkspace = UserWorkspace::where('user_id', $user->user_id)->where('permission', 'member')->where('is_active', 1)->first();

                            if (count($userInOtherWorkspace) == 0) {
                                $user->is_active = $request->is_disable;
                                $user->save();
                            } else {
                                $userData = User::find($user->user_id);
                                $userData->currant_workspace = $otherFirstWorkspace->workspace_id;
                                $userData->save();
                            }
                        }

                        // for client
                        $workspaceClient = ClientWorkspace::where('workspace_id', $request->id)->get();
                        foreach ($workspaceClient as $client) {
                            $client->is_active = $request->is_disable;
                            $client->save();

                            $clientInOtherWorkspace = ClientWorkspace::where('client_id', $client->client_id)->where('is_active', 1)->get();
                            $clientOtherFirstWorkspace = ClientWorkspace::where('client_id', $client->client_id)->where('is_active', 1)->first();
                            if (count($clientInOtherWorkspace) == 0) {
                                $client->is_active = $request->is_disable;
                                $client->save();
                            } else {
                                $clientData = Client::find($client->client_id);
                                $clientData->currant_workspace = $clientOtherFirstWorkspace->workspace_id;
                                $clientData->save();
                            }
                        }

                        $companyFirstWorkspaces = Workspace::where('created_by', $request->company_id)->where('is_active', 1)->first();
                        $company = User::where('id', $request->company_id)->first();
                        $company->currant_workspace = $companyFirstWorkspaces->id;
                        $company->save();
                    } else {
                        return response()->json(['error' => __('You Can Not Disable Current Workspace !!!')]);
                    }
                }


                if ($request->is_disable == 1) {
                    $currentWorkspace = Workspace::where('id', $request->id)->first();
                    $currentWorkspace->is_active = $request->is_disable;
                    $currentWorkspace->save();
                    $users = UserWorkspace::where('workspace_id', $currentWorkspace->id)
                        ->where('permission', 'Member')
                        ->get();
                    foreach ($users as $user) {
                        $user->is_active = $request->is_disable;
                        $user->save();
                    }


                    // for client
                    $workspaceClient = ClientWorkspace::where('workspace_id', $request->id)->get();
                    foreach ($workspaceClient as $client) {
                        $client->is_active = $request->is_disable;
                        $client->save();
                    }
                }


                $data = $this->Counter($request->company_id);
            }
            if ($data['is_success']) {
                $users_data = $data['response']['users_data'];
                $workspce_data = $data['response']['workspce_data'];
            }

            if ($request->is_disable == 1) {
                return response()->json(['success' => __('Successfully Unable.'), 'users_data' => $users_data, 'workspce_data' => $workspce_data]);
            } else {
                return response()->json(['success' => __('Successfull Disable.'), 'users_data' => $users_data, 'workspce_data' => $workspce_data]);
            }
        }
        return response()->json('error');
    }

    public function index($slug = null)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace) {

            $users = User::select('users.*', 'user_workspaces.permission', 'user_workspaces.is_active')->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id');
            $users->where('user_workspaces.workspace_id', '=', $currentWorkspace->id);
            $users = $users->get();
        } else {
            // $users = User::where('type', '!=', 'admin')->with('getPlan')->get();
            $users = User::where('type', '!=', 'admin')
                ->where([
                    ['type', '=', 'user'],
                    ['plan', '!=', 'NULL'],

                ])
                ->with('getPlan')
                ->get();



        }
        $user = auth()->user(); // Giriş yapan kullanıcı bilgisi
    $planName = optional($user->plan)->name; // Kullanıcının plan ismini al
    if ($planName !== 'demo'||$planName !== 'free') {
     // Eğer kullanıcı demo planında değilse, hiçbir kısıtlama yapmadan sayfayı göster
    $planRestricted = false;
     return view('users.index', compact('currentWorkspace', ['users','planRestricted']));
 }
 $workspaces = UserWorkspace::where('user_id', $user->id)->get();
    $workspaceCount = $workspaces->pluck('workspace_id')->unique()->count();
    $memberCount = 0;
    if ($workspaceCount == 1) {
        $workspaceId = $workspaces->first()->workspace_id;
        $memberCount = UserWorkspace::where('workspace_id', $workspaceId)
                        ->where('permission', 'Member')
                        ->count();
    }

    return view('users.index', ['users','userPlan',
        'workspaceCount' => $workspaceCount,
        'memberCount' => $memberCount,
        'planRestricted' => true, 
    ]);
    }


    public function export()
    {
        $name = 'Users_' . date('Y-m-d i:h:s');
        $data = Excel::download(new UsersExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }


    public function importFile()
    {
        return view('users.import');
    }

    public function import(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $Users = (new UsersImport())->toArray(request()->file('file'))[0];

        $totalCustomer = count($Users) - 1;
        $errorArray = [];



        for ($i = 1; $i <= count($Users) - 1; $i++) {
            $user = $Users[$i];
            $userByEmail = User::where('email', $user[1])->first();


            if (!empty($userByEmail)) {
                $userData = $userByEmail;
            } else {
                $userData = new User();
                // $userData->id = $this->UserNumber();

            }
            $userData->name = $user[0];
            $userData->email = $user[1];
            $userData->password = Hash::make($user[2]);
            $userData->type = $user[3];
            $userData->email_verified_at = date('Y-m-d H:i:s');


            if ((Auth::user()->type == 'admin')) {

                $userData->assignPlan(1, 'monthly');
            }

            if (empty($userData)) {
                $errorArray[] = $userData;
            } else {
                $userData->save();
            }

            // $objWorkspace = new Workspace();
            // $objWorkspace->created_by = $userData->id;
            // $objWorkspace->name = $userData->name;
            // $objWorkspace->slug = $userData->name;
            // $objWorkspace->lang = 'en';
            // $objWorkspace->currency_code = 'USD';
            // $objWorkspace->paypal_mode = 'sandbox';

            $objWorkspace = Workspace::create(

                [
                    'created_by' => $userData->id,
                    'name' => $userData->name,
                    'currency_code' => 'USD',
                    'paypal_mode' => 'sandbox',
                    'lang' => (env('DEFAULT_ADMIN_LANG')) ? env('DEFAULT_ADMIN_LANG') : 'en',
                ]
            );

            if (empty($objWorkspace)) {
                $errorArray[] = $objWorkspace;
            } else {
                $objWorkspace->save();
            }


            $userData->currant_workspace = $objWorkspace->id;
            $userData->save();

            $userWorkspace = new UserWorkspace();
            $userWorkspace->user_id = $userData->id;
            $userWorkspace->workspace_id = $objWorkspace->id;
            $userWorkspace->permission = 'Owner';

            if (empty($userWorkspace)) {
                $errorArray[] = $userWorkspace;
            } else {
                $userWorkspace->save();
            }

            User::userDefaultDataRegister($userData);
        }

        $errorRecord = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg'] = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg'] = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');


            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);
            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function create(Request $request)
    {
        if (Auth::user()->type == 'admin') {
            return view('users.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->type == 'admin') {
            $validation = [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'workspace' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users',
                ],
            ];

            $adminData = Utility::getAdminPaymentSetting();
            $validator = \Validator::make($request->all(), $validation);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $passwordSwitch = 0;
            if (!empty($request->password_switch) && $request->password_switch == 'on') {
                $passwordSwitch = 1;
                $validator = Validator::make($request->all(), ['password' => 'required|min:6']);
                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }
            }
            $userPassword = $request->password;
            $password = !is_null($userPassword) ? Hash::make($userPassword) : null;
            do {
                $code = rand(100000, 999999);
            } while (DB::table('users')->where('referral_code', $code)->exists());
            $objUser = User::create(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $password,
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'lang' => (env('DEFAULT_ADMIN_LANG')) ? env('DEFAULT_ADMIN_LANG') : 'en',
                    'is_enable_login' => $passwordSwitch,
                    'referral_code' => $code
                ]
            );
            #default plan
            $assignPlan = $objUser->assignPlan(4);

            if (!$assignPlan['is_success']) {
                return redirect()->back()->with('error', __($assignPlan['error']));
            }


            $objWorkspace = Workspace::create(

                [
                    'created_by' => $objUser->id,
                    'name' => $request->workspace,
                    'theme_color' => $adminData['color'],
                    'color_flag' => $adminData['color_flag'],
                    'currency_code' => 'USD',
                    'paypal_mode' => 'sandbox',
                    'lang' => (env('DEFAULT_ADMIN_LANG')) ? env('DEFAULT_ADMIN_LANG') : 'en',
                ]
            );


            $objUser->currant_workspace = $objWorkspace->id;
            $objUser->save();

            UserWorkspace::create(
                [
                    'user_id' => $objUser->id,
                    'workspace_id' => $objWorkspace->id,
                    'permission' => 'Owner',
                ]
            );

            $emailTemplate = EmailTemplate::all();

            foreach ($emailTemplate as $emailtemp) {
                UserEmailTemplate::create(
                    [
                        'template_id' => $emailtemp->id,
                        'user_id' => $objUser->id,
                        'workspace_id' => $objUser->currant_workspace,
                        'is_active' => 0,
                    ]
                );
            }
            Utility::setMailConfig();
            try {
                // event(new Registered($objUser));
                $objUser->password = $request->password;
                Mail::to($objUser->email)->send(new SendLoginDetail($objUser));
                // Append the success message
                $msg = 'Mail sent successfully. ';
            } catch (\Exception $e) {
                $msg = __('E-Mail has been not sent due to SMTP configuration');
            }
            // return redirect()->route('admin.users.index', '')->with('success', __('User Created Successfully' . $msg));
            return redirect()->route('admin.users.index', '')->with('success', __('Company Created Successfully' . ((isset($msg)) ? ' <br> <span class="text-danger">' . $msg . '</span>' : '')));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function account()
    {
        $user = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        return view('users.account', compact('currentWorkspace', 'user'));
    }

    public function edit($slug, $id)
    {
        $user = User::find($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        return view('users.edit', compact('currentWorkspace', 'user'));
    }

    public function deleteAvatar()
    {
        $setting = Utility::getAdminPaymentSetting();
        $logo = Utility::get_file('users-avatar/');
        $objUser = Auth::user();

        if (Storage::disk($setting['storage_setting'])->exists('/users-avatar/' . $objUser->avatar)) {
            $file_path = 'users-avatar/' . $objUser->avatar;
            $result = Utility::changeStorageLimit($objUser, $file_path);
            // Storage::disk($setting['storage_setting'])->delete('uploads/invoice_receipt/' .$payments->receipt);
        }
        //  if(\File::exists($logo.$objUser->avatar))
        //     {
        //      \File::delete($logo.$objUser->avatar);
        //     }
        $objUser->avatar = '';
        $objUser->save();

        return redirect()->back()->with('success', 'Avatar deleted successfully');
    }
    public function update($slug = null, $id = null, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_owner = isset($currentWorkspace) ? $currentWorkspace->owner($currentWorkspace->id) : '';
        if ($id) {
            $objUser = User::find($id);
        } else {
            $objUser = Auth::user();
        }
        $validation = [];
        $validation['name'] = 'required';
        $validation['email'] = 'required|email|max:100|unique:users,email,' . $objUser->id . ',id';

        if ($request->has('avatar')) {
            $validation['avatar'] = 'required';
        }

        $validator = \Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $objUser->name = $request->name;
        $objUser->email = $request->email;
        $dir = 'users-avatar/';

        $logo = Utility::get_file('users-avatar/');
        if ($request->has('avatar')) {
            // if(\File::exists($logo.$objUser->avatar))
            // {
            //  \File::delete($logo.$objUser->avatar);
            // }
            if ($objUser->type != 'admin') {

                $image_size = $request->file('avatar')->getSize();
                $result = Utility::updateStorageLimit($workspace_owner->id, $image_size);
                if ($result == 1) {
                    Utility::changeStorageLimit($workspace_owner->id, $objUser->avatar);
                    $logoName = uniqid() . '.png';
                    $path = Utility::upload_file($request, 'avatar', $logoName, $dir, []);
                    if ($path['flag'] == 1) {
                        $avatar = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            } else {

                $logoName = uniqid() . '.png';
                $path = Utility::upload_file($request, 'avatar', $logoName, $dir, []);
                if ($path['flag'] == 1) {
                    $avatar = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            // $request->avatar->storeAs('avatars', $logoName);
            $objUser->avatar = $logoName;
        }

        $objUser->save();

        return redirect()->back()->with('success', __('User Updated Successfully!') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
    }

    public function destroy($user_id)
    {
        $user = User::find($user_id);

        // $count_member = UserWorkspace::where('workspace_id', $user->currant_workspace)->where('permission', 'Member')->get();
        // $count_client = Client::where('currant_workspace', $user->currant_workspace)->get();

        $allWorkspace = Workspace::where('created_by', $user->id)->get();
        $countMembers = 0;
        $countClient = 0;
        foreach ($allWorkspace as $workspace) {
            $countMembers += UserWorkspace::where('workspace_id', $workspace->id)
                ->where('permission', 'Member')
                ->count();

            $countClient += ClientWorkspace::where('workspace_id', $workspace->id)->count();
        }


        if ($countClient > 0 && $countClient != 0) {
            return redirect()->back()->with('error', __('Please Delete All Clients of this company.'));
        }

        if ($countMembers > 0 && $countMembers != 0) {
            return redirect()->back()->with('error', __('Please Delete All Members of this company.'));
        } else {
            if ($user_id != 1) {
                $workspaces = Workspace::where('created_by', '=', $user->id)->get();
                foreach ($workspaces as $workspace) {
                    Tax::where('workspace_id', '=', $workspace->id)->delete();
                    UserWorkspace::where('workspace_id', '=', $workspace->id)->delete();
                    ClientWorkspace::where('workspace_id', '=', $workspace->id)->delete();
                    Note::where('workspace', '=', $workspace->id)->delete();
                    if ($projects = $workspace->projects) {
                        foreach ($projects as $project) {
                            UserProject::where('project_id', '=', $project->id)->delete();
                            ClientProject::where('project_id', '=', $project->id)->delete();
                            Milestone::where('project_id', '=', $project->id)->delete();
                            Timesheet::where('project_id', '=', $project->id)->delete();
                            Task::where('project_id', '=', $project->id)->delete();
                            $project->delete();
                        }
                    }
                    $logindetail = LoginDetail::where('created_by', $workspace->id);
                    $logindetail->delete();
                    $workspace->delete();
                }
                $user = User::find($user_id);
                $user->delete();

                return redirect()->back()->with('success', __('User Deleted Successfully!'));
            } else {
                return redirect()->back()->with('error', __('Some Thing Is Wrong!'));
            }
        }
    }

    public function changePlan($user_id)
    {
        $user = Auth::user();
        if ($user->type == 'admin') {
            $plans = Plan::where('is_plan_enable', 1)->get();
            $user = User::find($user_id);

            return view('users.change_plan', compact('plans', 'user'));
        } else {
            return redirect()->back()->with('error', __('Some Thing Is Wrong!'));
        }
    }

    public function updatePlan(Request $request, $user_id)
    {
        $user = Auth::user();
        if ($user->type == 'admin') {
            $objUser = User::find($user_id);
            $plan = Plan::find($request->plan);

            $assignPlan = $objUser->assignPlan($plan->id);

            if (!empty($plan) && $assignPlan['is_success'] == true) {
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                Order::create(
                    [
                        'order_id' => $order_id,
                        'name' => null,
                        'card_number' => null,
                        'card_exp_month' => null,
                        'card_exp_year' => null,
                        'plan_name' => $plan->name,
                        'plan_id' => $plan->id,
                        'price' => $plan->monthly_price,
                        "price_currency" => env('CURRENCY') != '' ? env('CURRENCY') : '',
                        'txn_id' => '',
                        'payment_type' => __('Manually Upgrade By Super Admin'),
                        'payment_status' => 'succeeded',
                        'receipt' => null,
                        'user_id' => $user_id,
                    ]
                );

                return redirect()->back()->with('success', __('Plan Updated Successfully!'));
            } else {
                return redirect()->back()->with('error', __($assignPlan['error']));
            }
        } else {
            return redirect()->back()->with('error', __('Some Thing Is Wrong!'));
        }
    }

    public function resetPassword($user_id)
    {
        $user = Auth::user();
        if ($user->type == 'admin' || $user->type == 'user') {
            return view('users.reset_password', compact('user_id'));
        } else {
            return redirect()->back()->with('error', __('Some Thing Is Wrong!'));
        }
    }

    public function changePassword($id, Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'admin' || $user->type == 'user') {
            // $request->validate(
            //     [
            //         'password' => 'required|same:password_confirmation',
            //         'password_confirmation' => 'required',
            //     ]
            // );

            $validator = \Validator::make($request->all(), [
                'password' => 'required|same:password_confirmation',
                'password_confirmation' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $user = User::find($id);
            if ($request->login_enable == true) {
                $user->password = Hash::make($request['password']);
                $user->is_enable_login = 1;
                $user->save();
            } else {
                $user->password = Hash::make($request['password']);
                $user->save();
            }
            return redirect()->back()->with('success', __('Password Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __('Some Thing Is Wrong!'));
        }
    }


    public function updatePassword(Request $request)
    {
        if (Auth::Check()) {
            //   $rules =  $request->validate(
            //         [
            //             'old_password' => 'required',
            //             'password' => 'required|same:password_confirmation',
            //             'password_confirmation' => 'required'
            //         ]
            //     );
            $validator = \Validator::make($request->all(), [
                'old_password' => 'required',
                'password' => 'required|same:password_confirmation',
                'password_confirmation' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $objUser = Auth::user();
            $request_data = $request->All();
            $current_password = $objUser->password;

            if (Hash::check($request_data['old_password'], $current_password)) {
                $objUser->password = Hash::make($request_data['password']);
                $objUser->save();

                return redirect()->back()->with('success', __('Password Updated Successfully!'));
            } else {
                return redirect()->back()->with('error', __('Please Enter Correct Current Password!'));
            }
        } else {
            return redirect()->back()->with('error', __('Some Thing Is Wrong!'));
        }
    }


    public function getUserJson($workspace_id)
    {
        $return = [];
        $objdata = UserWorkspace::select('user.email')->join('users', 'users.id', '=', 'user_workspaces.user_id')->where('user_workspaces.is_active', '=', 1)->where('users.id', '!=', auth()->id())->get();
        foreach ($objdata as $data) {
            $return[] = $data->email;
        }

        return response()->json($return);
    }

    public function getProjectUserJson($projectID)
    {
        $project = Project::find($projectID);

        return $project->users->toJSON();
    }

    public function getProjectMilestoneJson($projectID)
    {
        $project = Project::find($projectID);

        return $project->milestones->toJSON();
    }

    public function invite($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        return view('users.invite', compact('currentWorkspace'));
    }

    public function inviteUser($slug, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $post = $request->all();
        $name = $post['username'];
        $email = $post['useremail'];
        $password = !is_null($post['userpassword']) ? Hash::make($post['userpassword']) : null;

        $passwordSwitch = 0;
        if (!empty($request->password_switch) && $request->password_switch == 'on') {
            $passwordSwitch = 1;
        }


        $verify = date('Y-m-d i:h:s');
        $registerUsers = User::where('email', $email)->first();

        if ($registerUsers) {
            return json_encode(
                [
                    'code' => 400,
                    'status' => 'Error',
                    'error' => __('Email is Already Exits.'),
                ]
            );
        } else {
            $objUser = \Auth::user();
            $plan = Plan::find($objUser->plan);
            if ($plan) {
                $totalWS = $objUser->countUsers($currentWorkspace->id);
                if ($totalWS < $plan->max_users || $plan->max_users == -1) {
                    $arrUser = [];
                    $arrUser['name'] = $name;
                    $arrUser['email'] = $email;
                    $arrUser['plan'] = NULL;
                    $arrUser['password'] = $password;
                    $arrUser['currant_workspace'] = $currentWorkspace->id;
                    $arrUser['email_verified_at'] = $verify;
                    $arrUser['lang'] = $currentWorkspace->lang;
                    $arrUser['is_enable_login'] = $passwordSwitch;
                    $registerUsers = User::create($arrUser);
                    $registerUsers->password = $request->userpassword;
                    // $assignPlan = $registerUsers->assignPlan(1);

                    // if (!$assignPlan['is_success']) {
                    //     return json_encode(
                    //         [
                    //             'code' => 400,
                    //             'status' => 'Error',
                    //             'error' => __($assignPlan['error']),
                    //         ]
                    //     );
                    // }
                    Utility::setMailConfig();
                    try {
                        Mail::to($email)->send(new SendLoginDetail($registerUsers));
                    } catch (\Exception $e) {
                        $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                    }
                } else {
                    return json_encode(
                        [
                            'code' => 400,
                            'status' => 'Error',
                            'error' => __('Your user limit is over, Please upgrade plan.'),
                        ]
                    );
                }
            } else {
                return json_encode(
                    [
                        'code' => 400,
                        'status' => 'Error',
                        'error' => __('Default plan is deleted.'),
                    ]
                );
            }
        }

        // assign workspace first
        $is_assigned = false;
        foreach ($registerUsers->workspace as $workspace) {
            if ($workspace->id == $currentWorkspace->id) {
                $is_assigned = true;
            }
        }

        if (!$is_assigned) {
            UserWorkspace::create(
                [
                    'user_id' => $registerUsers->id,
                    'workspace_id' => $currentWorkspace->id,
                    'permission' => 'Member',
                ]
            );

            try {
                Mail::to($registerUsers->email)->send(new SendWorkspaceInvication($registerUsers, $currentWorkspace));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }
        }

        return json_encode(
            [
                'code' => 200,
                'status' => 'Success',
                'url' => route('users.index', $currentWorkspace->slug),
                'success' => __('Users Invited Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''),
            ]
        );
    }

    // public function removeUser($slug, $id)
    // {
    //     $currentWorkspace = Utility::getWorkspaceBySlug($slug);
    //     $userWorkspace = UserWorkspace::where('user_id', '=', $id)->where('workspace_id', '=', $currentWorkspace->id)->first();
    //     if ($userWorkspace) {
    //         $user = User::find($id);
    //         $userProjectCount = $user->countProject($currentWorkspace->id);
    //         if ($userProjectCount == 0) {
    //             $userWorkspace->delete();
    //         } else {
    //             return redirect()->back()->with('error', __('Please Remove User From Project!'));
    //         }

    //         return redirect()->route('users.index', $currentWorkspace->slug)->with('success', __('User Removed Successfully!'));
    //     } else {
    //         return redirect()->back()->with('error', __('Something is wrong.'));
    //     }
    // }

    public function removeUser($slug, $id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $userWorkspace = UserWorkspace::where('user_id', '=', $id)->where('workspace_id', '=', $currentWorkspace->id)->first();
        $userAllWorkspace = UserWorkspace::where('user_id', '=', $id)->get();
        if (count($userAllWorkspace) > 1) {
            $user = User::find($id);
            $userProjectCount = $user->countProject($currentWorkspace->id);
            if ($userProjectCount == 0) {
                $userWorkspace->delete();
                $remaningWorkspace = UserWorkspace::where('user_id', '=', $id)->first();
                $user->currant_workspace = $remaningWorkspace->workspace_id;
                $user->save();
            } else {
                return redirect()->back()->with('error', __('Please Remove User From Project!'));
            }
            return redirect()->route('users.index', $currentWorkspace->slug)->with('success', __('User Removed Successfully!'));
        } else {
            $user = User::find($id);
            $userProjectCount = $user->countProject($currentWorkspace->id);

            if ($userProjectCount == 0) {
                $remaningWorkspace = UserWorkspace::where('user_id', '=', $id)->first();
                $userWorkspace->delete();
                $user->delete();
                return redirect()->back()->with('Sussess', __('User Removed Successfully!'));
            } else {
                return redirect()->back()->with('error', __('Please Remove User From Project!'));
            }
        }
    }

    public function chatIndex($slug = '')
    {
        if (env('CHAT_MODULE') == 'yes') {
            $objUser = Auth::user();
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            if ($currentWorkspace) {
                $users = User::select('users.*', 'user_workspaces.permission', 'user_workspaces.is_active')->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id');
                $users->where('user_workspaces.workspace_id', '=', $currentWorkspace->id)->where('users.id', '!=', $objUser->id);
                $users = $users->get();
            } else {
                $users = User::where('type', '!=', 'admin')->get();
            }

            return view('chats.index', compact('currentWorkspace', 'users'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

    public function getMessage($currentWorkspace, $user_id)
    {
        $workspace = Workspace::find($currentWorkspace);
        Utility::getWorkspaceBySlug($workspace->slug);
        $my_id = Auth::id();

        // Make read all unread message
        Message::where(
            [
                'workspace_id' => $currentWorkspace,
                'from' => $user_id,
                'to' => $my_id,
                'is_read' => 0,
            ]
        )->update(['is_read' => 1]);

        // Get all message from selected user
        $messages = Message::where(
            function ($query) use ($currentWorkspace, $user_id, $my_id) {
                $query->where('workspace_id', $currentWorkspace)->where('from', $user_id)->where('to', $my_id);
            }
        )->oRwhere(
                function ($query) use ($currentWorkspace, $user_id, $my_id) {
                    $query->where('workspace_id', $currentWorkspace)->where('from', $my_id)->where('to', $user_id);
                }
            )->get();

        return view('chats.message', ['messages' => $messages]);
    }

    public function sendMessage(Request $request)
    {
        $from = Auth::id();
        $currentWorkspace = Workspace::find($request->workspace_id);
        $to = $request->receiver_id;
        $message = $request->message;

        $data = new Message();
        $data->workspace_id = $currentWorkspace->id;
        $data->from = $from;
        $data->to = $to;
        $data->message = $message;
        $data->is_read = 0; // message will be unread when sending message
        $data->save();

        // pusher
        $options = array(
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        );

        // $pusher = new Pusher(
        //     env('PUSHER_APP_KEY'),
        //     env('PUSHER_APP_SECRET'),
        //     env('PUSHER_APP_ID'),
        //     $options
        // );

        $setting = Utility::getAdminPaymentSettings();
        $pusher = new Pusher(
            $setting['pusher_app_key'],
            $setting['pusher_app_secret'],
            $setting['pusher_app_id'],
            $options
        );

        $data = [
            'from' => $from,
            'to' => $to,
        ]; // sending from and to user id when pressed enter
        $pusher->trigger($currentWorkspace->slug, 'chat', $data);

        return response()->json($data, 200);
    }

    public function notificationSeen($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user = Auth::user();
        Notification::where('workspace_id', '=', $currentWorkspace->id)->where('user_id', '=', $user->id)->update(['is_read' => 1]);

        return response()->json(['is_success' => true], 200);
    }

    public function getMessagePopup($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user = Auth::user();
        $messages = Message::whereIn(
            'id',
            function ($query) use ($currentWorkspace, $user) {
                $query->select(\DB::raw('MAX(id)'))->from('messages')->where('workspace_id', '=', $currentWorkspace->id)->where('to', $user->id)->where('is_read', '=', 0);
            }
        )->orderBy('id', 'desc')->get();

        return view('chats.popup', compact('messages', 'currentWorkspace'));
    }

    public function messageSeen($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user = Auth::user();
        Message::where('workspace_id', '=', $currentWorkspace->id)->where('to', '=', $user->id)->update(['is_read' => 1]);

        return response()->json(['is_success' => true], 200);
    }

    // public function deleteMyAccount()
    // {
    //     $user = Auth::user();

    //     ActivityLog::where('user_id', $user->id)->delete();
    //     BugComment::where('created_by', $user->id)->delete();
    //     BugFile::where('created_by', $user->id)->delete();
    //     BugReport::where('assign_to', $user->id)->delete();
    //     Calendar::where('created_by', $user->id)->delete();
    //     Comment::where('created_by', $user->id)->delete();
    //     Events::where('created_by', $user->id)->delete();
    //     SubTask::where('created_by', $user->id)->delete();
    //     TaskFile::where('created_by', $user->id)->delete();

    //     $workspaces = Workspace::where('created_by', '=', $user->id)->get();
    //     foreach ($workspaces as $workspace) {
    //         Tax::where('workspace_id', '=', $workspace->id)->delete();
    //         UserWorkspace::where('workspace_id', '=', $workspace->id)->delete();
    //         ClientWorkspace::where('workspace_id', '=', $workspace->id)->delete();
    //         Note::where('workspace', '=', $workspace->id)->delete();
    //         if ($projects = $workspace->projects) {
    //             foreach ($projects as $project) {
    //                 UserProject::where('project_id', '=', $project->id)->delete();
    //                 ClientProject::where('project_id', '=', $project->id)->delete();
    //                 Milestone::where('project_id', '=', $project->id)->delete();
    //                 Timesheet::where('project_id', '=', $project->id)->delete();
    //                 Task::where('project_id', '=', $project->id)->delete();
    //                 $project->delete();
    //             }
    //         }
    //         $workspace->delete();
    //     }
    //     $user->delete();

    //     return redirect()->route('login');
    // }
    public function deleteMyAccount()
    {
        $user = Auth::user();

        $companyWorkspace = UserWorkspace::where('user_id', '=', $user->id)->where('permission', 'Owner')->get();
        foreach ($companyWorkspace as $data) {
            $companyMember = UserWorkspace::where('workspace_id', $data->workspace_id)->where('permission', 'Member')->get();
            if (count($companyMember) >= 1) {
                return redirect()->back()->with('error', 'Please Remove All Workspaces Users..');
            }
        }

        $userWorkspace = UserWorkspace::where('user_id', '=', $user->id)->where('permission', 'Member');
        $userWorkspace->delete();

        ActivityLog::where('user_id', $user->id)->delete();
        BugComment::where('created_by', $user->id)->delete();
        BugFile::where('created_by', $user->id)->delete();
        BugReport::where('assign_to', $user->id)->delete();
        Calendar::where('created_by', $user->id)->delete();
        Comment::where('created_by', $user->id)->delete();
        Events::where('created_by', $user->id)->delete();
        SubTask::where('created_by', $user->id)->delete();
        TaskFile::where('created_by', $user->id)->delete();

        $workspaces = Workspace::where('created_by', '=', $user->id)->get();
        foreach ($workspaces as $workspace) {
            Tax::where('workspace_id', '=', $workspace->id)->delete();
            UserWorkspace::where('workspace_id', '=', $workspace->id)->delete();
            ClientWorkspace::where('workspace_id', '=', $workspace->id)->delete();
            Note::where('workspace', '=', $workspace->id)->delete();
            if ($projects = $workspace->projects) {
                foreach ($projects as $project) {
                    UserProject::where('project_id', '=', $project->id)->delete();
                    ClientProject::where('project_id', '=', $project->id)->delete();
                    Milestone::where('project_id', '=', $project->id)->delete();
                    Timesheet::where('project_id', '=', $project->id)->delete();
                    Task::where('project_id', '=', $project->id)->delete();
                    $project->delete();
                }
            }
            $workspace->delete();
        }
        $user->delete();

        return redirect()->route('login');
    }

    public function checkUserExists(Request $request, $slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $authuser = Auth::user();
        $authusername = User::where('id', '=', $authuser->id)->first();
        $superAdminEmails = User::where('type', '=', 'admin')->pluck('email')->toArray();
        $setting = Utility::fetchAdminPaymentSetting();


        if ($request->has('email')) {
            $email = $request->email;
            if (in_array($email, $superAdminEmails)) {
                $response = [
                    'code' => 202,
                    'status' => 'error',
                    'message' => __('You Can Not Add Super Admin As A Normal User.'),
                ];

                return json_encode($response);
            }
            $registerUsers = User::where('email', '=', $email)->first(); //->where('is_active', '=', 1)
        } elseif ($request->has('user_id')) {
            $user_id = $request->user_id;
            $registerUsers = User::find($user_id);
        }

        $response = [
            'code' => 404,
            'status' => 'Error',
            'error' => __('This User is not connected with our system. Please fill out the below details to invite.'),
        ];

        if (!empty($registerUsers)) {
            // Check if the user is already assigned to the current workspace
            $is_assigned = UserWorkspace::where('user_id', $registerUsers->id)
                ->where('workspace_id', $currentWorkspace->id)
                ->exists();

            if (!$is_assigned) {
                UserWorkspace::create([
                    'user_id' => $registerUsers->id,
                    'workspace_id' => $currentWorkspace->id,
                    'permission' => 'Member',
                ]);

                $workspace_name = Workspace::where('id', $authuser->currant_workspace)->first();

                try {
                    $uArr = [
                        'user_name' => $registerUsers->name,
                        // 'app_name' => env('APP_NAME'),
                        'app_name' => $setting['app_name'],
                        'workspace_name' => $workspace_name->name,
                        'owner_name' => $authusername->name,
                        'app_url' => env('APP_URL'),
                    ];

                    // Send Email
                    $resp = Utility::sendEmailTemplate('User Invited', $registerUsers->id, $uArr);
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has not been sent due to SMTP configuration');
                }

                $response = [
                    'code' => 200,
                    'status' => 'Success',
                    'url' => route('users.index', $currentWorkspace->slug),
                    'message' => __('User Invited Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''),
                ];
            } else {
                $response = [
                    'code' => 202,
                    'status' => 'error',
                    'message' => __('This user is already a member of this workspace.'),
                ];
            }
        }

        return json_encode($response);
    }



    public function manuallyActivatePlan(Request $request, $user_id, $plan_id, $duration)
    {
        $user = User::find($user_id);
        $plan = Plan::find($plan_id);

        $assignPlan = $user->assignPlan($plan->id, $duration);

        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $price = $plan->{$duration . '_price'};
            if (!empty($user->payment_subscription_id) && $user->payment_subscription_id != '') {
                try {
                    $user->cancel_subscription($user_id);
                } catch (\Exception $exception) {
                    \Log::debug($exception->getMessage());
                }
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'email' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $price,
                    'payment_frequency' => $duration,
                    'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                    'txn_id' => '',
                    'payment_type' => __('Manually Upgrade By Super Admin'),
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );

            return redirect()->back()->with('success', __('Plan successfully upgraded.'));
        } else {
            return redirect()->back()->with('error', __('Plan fail to upgrade.'));
        }
    }


    public function delete_all_notification($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $user = Auth::user();
        $get_notification = Notification::where('user_id', $user->id)->where('workspace_id', $currentWorkspace->id)->delete();

        // $get_notification->delete();

        return response()->json(
            [
                'is_success' => true,
                'success' => __('Notifications successfully deleted!'),
            ],
            200
        );
    }

    public function managelogin(Request $request, $userId)
    {
        $userId = decrypt($userId);
        $user = User::where('id', $userId)->first();
        if ($user) {
            if ($user->is_enable_login == 1) {
                $user->is_enable_login = 0;
                $user->save();

                return redirect()->back()->with('success', 'User login disable successfully.');
            } else {
                $user->is_enable_login = 1;
                $user->save();
                return redirect()->back()->with('success', 'User login enable successfully.');
            }
        } else {
            return redirect()->back()->with('error', 'User Not Found !!!');
        }
    }
}
