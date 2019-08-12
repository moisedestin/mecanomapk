<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\RequestEmergencyRequest as StoreRequest;
use App\Http\Requests\RequestEmergencyRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class RequestEmergencyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class RequestEmergencyCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\RequestEmergency');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/request_emergency');
        $this->crud->setEntityNameStrings('requestemergency', 'request_emergencies');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

         $this->crud->removeAllButtons();


        $this->crud->addColumn(
            [
                'label' => 'Date',
                'type' => 'closure',
                'function' => function($entry) {
                    return $entry->created_at;

                }
            ]
        )->beforeColumn('trouble');


        $this->crud->setColumnDetails('is_rate',
            [
                'label' => 'Is Rate',
                'type' => 'closure',
                'function' => function($entry) {
                    if($entry->is_rate == 0)
                        return  'No';
                    else
                        return  'Yes';

                }
            ]
        );

        $this->crud->setColumnDetails('mechanic_user_id',
            [
                'label' => 'Mechanician',
                'type' => 'model_function',
                'function_name' =>  'getMechanician'
            ]
        );

        $this->crud->setColumnDetails('driver_user_id',
            [
                'label' => 'Driver',
                'type' => 'model_function',
                'function_name' =>  'getDriver'
            ]
        );

        $this->crud->setColumnDetails('driver_user_id',
            [
                'label' => 'Driver',
                'type' => 'closure',
                'function' => function($entry) {

                    return User::find($entry->driver_user_id)->email;
                }
            ]
        );

        $this->crud->setColumnDetails('is_mechanic_arrived',
            [
                'label' => 'Mechanic arrived',
                'type' => 'closure',
                'function' => function($entry) {
                    if($entry->is_mechanic_arrived == 0)
                        return  '<span class="label label-default">No</span>';
                    else
                        return  '<span class="label label-success">Yes</span>';

                }
            ]
        );

        $this->crud->setColumnDetails('driver_check_arrived',
            [
                'label' => 'Driver Check Arrived',
                'type' => 'closure',
                'function' => function($entry) {
                    if($entry->driver_check_arrived == 0)
                        return  '<span class="label label-default">No</span>';
                    else
                        return  '<span class="label label-danger">Yes</span>';

                }
            ]
        );

        $this->crud->setColumnDetails('is_mechanic_agree',
            [
                'label' => 'Is Mechanician Agree',
                'type' => 'closure',
                'function' => function($entry) {
                    if(!$entry->is_mechanic_agree)
                        return  '<span class="label label-default">No</span>';
                    else
                        return  '<span class="label label-success">Yes</span>';

                }
            ]
        );

        $this->crud->orderBy('created_at', 'desc');


        // add asterisk for fields that are required in RequestEmergencyRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
