<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // $data = User::select(
        //     'users.id',
        //     'users.name',
        //     'users.email',
        //     'workspaces.name as workspace_name', // Include workspace name
        //     'users.type',
        //     'users.created_at',
        //     'users.updated_at'
        // )->leftJoin('workspaces', 'users.currant_workspace', '=', 'workspaces.id')->where('type','!=','admin')->get();
        // return $data;
        $data = User::select(
            'users.id',
            'users.name',
            'users.email',
            'workspaces.name as workspace_name', // Include workspace name
            'users.type',
            'users.created_at',
            'users.updated_at'
        )->leftJoin('workspaces', 'users.currant_workspace', '=', 'workspaces.id')->where([
            ['type', '=', 'user'],
            ['plan', '!=', 'NULL'],

        ])->get();
        return $data;
    }
    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "Email",
            "Current Workspace",
            "Type",
            "Created At",
            "Updated At",
        ];
    }
    
}

