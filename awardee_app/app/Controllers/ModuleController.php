<?php

namespace App\Controllers;

class ModuleController extends BaseController
{
    public function dealers()
    {
        return view('modules/dealers/index', [
            'pageTitle' => 'Dealers',
        ]);
    }

    public function stores()
    {
        return view('modules/stores/index', [
            'pageTitle' => 'Stores',
        ]);
    }

    public function regions()
    {
        return view('modules/regions/index', [
            'pageTitle' => 'Regions',
        ]);
    }

    public function sales()
    {
        return view('modules/sales/index', [
            'pageTitle' => 'Sales',
        ]);
    }

    public function awardees()
    {
        return view('modules/awardees/index', [
            'pageTitle' => 'Awardees',
        ]);
    }

    public function reports()
    {
        return view('modules/reports/index', [
            'pageTitle' => 'Reports',
        ]);
    }

    public function settings()
    {
        return view('modules/settings/index', [
            'pageTitle' => 'Settings',
        ]);
    }
}
