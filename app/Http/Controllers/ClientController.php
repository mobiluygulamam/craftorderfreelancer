<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientProject;
use App\Models\ClientWorkspace;
use App\Models\Mail\SendClientLoginDetail;
use App\Exports\clientsExport;
use App\Imports\clientsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Plan;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function clientLogout(Request $request)
    {
        \Auth::guard('client')->logout();

        $request->session()->invalidate();

        return redirect()->route('client.login');
    }

    public function index($slug)
    {
        $this->middleware('auth');
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->creater->id == \Auth::user()->id) {
            $clients = Client::select(
                [
                    'clients.*',
                    'client_workspaces.is_active',
                ]
            )->join('client_workspaces', 'client_workspaces.client_id', '=', 'clients.id')->where('client_workspaces.workspace_id', '=', $currentWorkspace->id)->get();

            return view('clients.index', compact('currentWorkspace', 'clients'));
        } else {
            return redirect()->route('home');
        }
    }

    public function create($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        return view('clients.create', compact('currentWorkspace'));
    }

    public function export()
    {
        $name = 'clients_' . date('Y-m-d i:h:s');
        $data = Excel::download(new clientsExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function importFile($slug)
    {

        return view('clients.import', compact('slug'));
    }


    public function import(Request $request)
    {
        $slug = $request->slug;


        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $Users = (new clientsImport())->toArray(request()->file('file'))[0];

        $totalCustomer = count($Users) - 1;
        $errorArray = [];


        for ($i = 1; $i <= count($Users) - 1; $i++) {
            $user = $Users[$i];

            $userByEmail = Client::where('email', $user[2])->first();


            if (!empty($userByEmail)) {
                $userData = $userByEmail;
            } else {
                $userData = new Client();
                // $userData->id = $this->UserNumber();

            }
            $userData->name = $user[1];
            $userData->email = $user[2];
            $userData->password = Hash::make($user[3]);
            $userData->currant_workspace = $currentWorkspace->id;
            $userData->email_verified_at = date('Y-m-d H:i:s');


            if (empty($userData)) {
                $errorArray[] = $userData;
            } else {
                if (!$userByEmail) {
                    $userData->save();
                } else {
                    return redirect()->back()->with("error", __($user[1] . " Company already exits."));
                }
            }

            $clientByEmail = ClientWorkspace::where('client_id', $userData->id)->first();

            if (!$clientByEmail) {
                $objWorkspace = new ClientWorkspace();
                $objWorkspace->client_id = $userData->id;
                $objWorkspace->workspace_id = $currentWorkspace->id;
                if (empty($objWorkspace)) {
                    $errorArray[] = $objWorkspace;
                } else {
                    $objWorkspace->save();
                }
            } else {
                return redirect()->back()->with("error", __($user[1] . " client already exits."));
            }
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


    public function store($slug, Request $request)
    {

        $objUser = \Auth::user();
        $plan = Plan::find($objUser->plan);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $setting = Utility::fetchAdminPaymentSetting();
        if ($plan) {
            $totalWS = $objUser->countClients($currentWorkspace->id);
            if ($totalWS < $plan->max_clients || $plan->max_clients == -1) {

                $registerClient = Client::where('email', '=', $request->email)->first();

                $clientPassword = $request->password;
                $password = !is_null($clientPassword) ? Hash::make($clientPassword) : null;
                $passwordSwitch = 0;
                if (!empty($request->password_switch) && $request->password_switch == 'on') {
                    $passwordSwitch = 1;
                    $validator = Validator::make($request->all(), ['password' => 'required|min:6']);
                    if ($validator->fails()) {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
                }
                if (!$registerClient) {
                    $arrUser['name'] = $request->name;
                    $arrUser['email'] = $request->email;
                    $arrUser['password'] = $password;
                    $arrUser['currant_workspace'] = $currentWorkspace->id;
                    $arrUser['email_verified_at'] = date('Y-m-d i:h:s');
                    $arrUser['lang'] = isset($currentWorkspace->lang) ? $currentWorkspace->lang : 'en';
                    $arrUser['is_enable_login'] = $passwordSwitch;
                    $arrUser['city'] = $request->city;
                    $arrUser['state'] = $request->state;
                    $arrUser['zipcode'] = $request->zip_code;
                    $arrUser['country'] = $request->country;
                    $arrUser['telephone'] = $request->telephone;
                    $arrUser['address'] = $request->address;
                    $registerClient = Client::create($arrUser);

                    try {
                        $registerClient->password = $request->password;
                        $user = Client::where('email', '=', $request->email)->first();
                        $uArr = [
                            'user_name' => $request->name,
                            // 'app_name' => env('APP_NAME'),
                            'app_name' => $setting['app_name'],
                            'email' => $request->email,
                            'password' => $request->password,
                            'app_url' => env('APP_URL'),
                            'email_verified_at' => date('Y-m-d H:i:s'),
                        ];

                        // Send Email
                        $resp = Utility::sendclientEmailTemplate('New Client', $user->id, $uArr);
                        // Mail::to($request->email)->send(new SendClientLoginDetail($registerClient));
                    } catch (\Exception $e) {
                        $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                    }
                }

                $checkClient = ClientWorkspace::where('client_id', '=', $registerClient->id)->where('workspace_id', '=', $currentWorkspace->id)->first();
                if (!$checkClient) {
                    ClientWorkspace::create(
                        [
                            'client_id' => $registerClient->id,
                            'workspace_id' => $currentWorkspace->id,
                        ]
                    );
                } else {
                    return redirect()->back()->with('error', __('Client Is Already Exist.'));
                }

                return redirect()->route('clients.index', $currentWorkspace->slug)->with('success', __('Client Created Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
            } else {
                return redirect()->back()->with('error', __('Your client limit is over, Please upgrade plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Default plan is deleted.'));
        }
    }

    public function edit($slug, $id)
    {
        $client = Client::find($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        return view('clients.edit', compact('client', 'currentWorkspace'));
    }
    public function update($slug, $id, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($id) {
            $objUser = Client::find($id);
        } else {
            $objUser = \Auth::user();
        }
        $validation = [];
        $validation['name'] = 'required';
        $validation['email'] = 'required|email|max:100|unique:users,email,' . $objUser->id . ',id';
        $validation['city'] = 'required';
        $validation['state'] = 'required';
        $validation['zip_code'] = 'required';
        $validation['country'] = 'required';
        $validation['telephone'] = 'required';
        $validation['address'] = 'required';


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
        if ($objUser->getGuard() == 'client') {
            $objUser->password = Hash::make($request->password);
        }
        $objUser->city = $request->city;
        $objUser->state = $request->state;
        $objUser->zipcode = $request->zip_code;
        $objUser->country = $request->country;
        $objUser->telephone = $request->telephone;
        $objUser->address = $request->address;

        $dir = 'users-avatar/';

        $logo = Utility::get_file('users-avatar/');
        if ($request->has('avatar')) {
            if (\File::exists($logo . $objUser->avatar)) {
                \File::delete($logo . $objUser->avatar);
            }

            $logoName = uniqid() . '.png';
            $path = Utility::upload_file($request, 'avatar', $logoName, $dir, []);
            if ($path['flag'] == 1) {
                $avatar = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }

            // $request->avatar->storeAs('avatars', $logoName);
            $objUser->avatar = $logoName;
        }

        $objUser->save();

        return redirect()->back()->with('success', __('Client Updated Successfully!'));

        // else
        // {
        //     return redirect()->back()->with('error', __('Something is wrong.'));
        // }
    }

    // Old
    // public function destroy($slug, $id)
    // {
    //     $client = Client::find($id);

    //     if ($client) {
    //         $currentWorkspace = Utility::getWorkspaceBySlug($slug);
    //         ClientWorkspace::where('client_id', '=', $client->id)->delete();
    //         ClientProject::where('client_id', '=', $client->id)->delete();
    //         $client->delete();

    //         return redirect()->route('clients.index', $currentWorkspace->slug)->with('success', __('Client Deleted Successfully!'));
    //     } else {
    //         return redirect()->back()->with('error', __('Something is wrong.'));
    //     }
    // }

    public function destroy($slug, $id)
    {
        $client = Client::find($id);
        if ($client) {
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $clientAllWorkspace = ClientWorkspace::where('client_id', $id)->get();
            if (count($clientAllWorkspace) > 1) {
                $clientAllWorkspace = ClientWorkspace::where('client_id', $id)->first();
                $client->currant_workspace = $clientAllWorkspace->workspace_id;
                $client->save();
                // ClientWorkspace::where('workspace_id', '=', $currentWorkspace->id)->delete();
                // ClientProject::where('workspace_id', '=', $currentWorkspace->id)->delete();
                ClientWorkspace::where('workspace_id', '=', $currentWorkspace->id)->where('client_id', '=', $id)->delete();
                ClientProject::where('workspace_id', '=', $currentWorkspace->id)->where('client_id', '=', $id)->delete();
                return redirect()->back()->with('success', __('Client Deleted Successfully!'));
            } else {
                // ClientWorkspace::where('workspace_id', '=', $currentWorkspace->id)->delete();
                // ClientProject::where('workspace_id', '=', $currentWorkspace->id)->delete();
                ClientWorkspace::where('workspace_id', '=', $currentWorkspace->id)->where('client_id', '=', $id)->delete();
                ClientProject::where('workspace_id', '=', $currentWorkspace->id)->where('client_id', '=', $id)->delete();
                $client->delete();
                return redirect()->back()->with('success', __('Client Deleted Successfully!'));
            }
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

    public function updateBilling(Request $request)
    {
        $objUser = \Auth::user();
        $objUser->address = $request->address;
        $objUser->city = $request->city;
        $objUser->state = $request->state;
        $objUser->zipcode = $request->zipcode;
        $objUser->country = $request->country;
        $objUser->telephone = $request->telephone;
        $objUser->save();
        return redirect()->back()->with('success', __('Billing Details Updated Successfully!'));
    }

    public function resetPassword($slug, $id)
    {
        $client = Client::find($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        return view('clients.reset_password', compact('client', 'currentWorkspace'));
    }

    public function changePassword($slug, $id, Request $request)
    {

        $request->validate(
            [
                'password' => 'required|same:password',
                'password_confirmation' => 'required|same:password',
            ]
        );
        $client = Client::find($id);
        if ($client) {
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            if ($request->login_enable == true) {
                $client->password = Hash::make($request->password);
                $client->is_enable_login = 1;
                $client->save();
            } else {
                $client->password = Hash::make($request->password);
                $client->save();
            }
            return redirect()->route('clients.index', $currentWorkspace->slug)->with('success', __('Password Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }
    public function account()
    {
        $user = \Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        return view('users.account', compact('currentWorkspace', 'user'));
    }

    public function clientManageLogin(Request $request, $clientId)
    {
        $clientId = decrypt($clientId);
        $client = Client::where('id', $clientId)->first();
        if ($client) {
            if ($client->is_enable_login == 1) {
                $client->is_enable_login = 0;
                $client->save();

                return redirect()->back()->with('success', 'client login disable successfully.');
            } else {
                $client->is_enable_login = 1;
                $client->save();
                return redirect()->back()->with('success', 'client login enable successfully.');
            }
        } else {
            return redirect()->back()->with('error', 'client Not Found !!!');
        }
    }
}
