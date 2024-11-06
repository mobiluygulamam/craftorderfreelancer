<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class clientsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Client::join('workspaces', 'clients.currant_workspace', '=', 'workspaces.id')
            ->where('clients.currant_workspace', \Auth::user()->currant_workspace)
            ->select('clients.*', 'workspaces.name as workspace_name')
            ->get();

        foreach ($data as $k => $user) {
            unset(
                $user->address, $user->city, $user->state, $user->email_verified_at, $user->remember_token,
                $user->zipcode, $user->country, $user->lang,
                $user->password, $user->telephone, $user->avatar
                );

            $data[$k]["name"] = $user->name;
            $data[$k]["email"] = $user->email;
            $data[$k]["currant_workspace"] = $user->workspace_name;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "email",
            "currant_workspace",

            "Created At",
            "Updated At",
        ];
    }


}
