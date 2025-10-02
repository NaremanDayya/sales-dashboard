<?php
namespace App\View\Components;

use Illuminate\View\Component;

class SalesRepPermissions extends Component
{
public $permissions;
public $selectedPermissions;

/**
* Create a new component instance.
*/
public function __construct($permissions, $selectedPermissions = [])
{
$this->permissions = $permissions;
$this->selectedPermissions = $selectedPermissions;
}

/**
* Get the view / contents that represent the component.
*/
public function render()
{
return view('components.sales-rep-permissions');
}
}
