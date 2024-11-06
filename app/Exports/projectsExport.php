<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class projectsExport implements FromCollection,WithHeadings
{

    public function collection()
    {
        $objUser = \Auth::user();
        // $data = Project::where('created_by',\Auth::user()->id)->where('workspace',\Auth::user()->currant_workspace)->get();
        
        $data = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', \Auth::user()->currant_workspace)->get();

        foreach($data as $k => $Projects)
        {
            unset($Projects->created_by, $Projects->is_active,$Projects->id,$Projects->copylinksetting ,$Projects->password );


            $data[$k]["name"]           = $Projects->name;
            $data[$k]["status"]          = $Projects->status;
            $data[$k]["description"]     = $Projects->description;
            $data[$k]["start_date"]       = $Projects->start_date;
            $data[$k]["end_date"]         = $Projects->end_date;
            $data[$k]["budget"]          =  $Projects->budget;
            $data[$k]["workspace"]       = $Projects->workspace;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Name",
            "status",
            'description',
            "start_date",
            "end_date",
            "budget",
            "workspace",
            "Created At",
            "Updated At",
        ];
    }

}
