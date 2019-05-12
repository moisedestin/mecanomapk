<?php

namespace App\Http\Controllers\Admin;

 use App\Models\Garage;
use App\Models\Mechanic;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\MechanicRequest as StoreRequest;
use App\Http\Requests\MechanicRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class MechanicCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class MechanicCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Mechanic');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/mechanic');
        $this->crud->setEntityNameStrings('mechanic', 'mechanics');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
//        $this->crud->setFromDb();
        $this->crud->setColumns(['phone1','phone2','availability']);


        $this->crud->addField([
            'name'=>'full_name',
            'label'=>'Ful Name',
            'type'=>'text'

        ]);

        $this->crud->addField([
            'name'=>'gender',
            'label'=>'Sexe',
            'type'=>'select2_from_array',
            'options' => [
                '0' => 'FEMALE',
                '1' => 'MALE'
            ]

         ]);

        $this->crud->addField([   // date_picker
            'name' => 'birthday',
            'type' => 'date_picker',
            'label' => 'Birthday',
            // optional:
            'date_picker_options' => [
                'todayBtn' => true,
                'format' => 'dd-mm-yyyy',
                'language' => 'fr'
            ],
        ]);

        $this->crud->addField([
            'name'=>'email',
            'label'=>'Email',
            'type'=>'email'

        ]);

        $this->crud->addField([
            'name'=>'phone1',
            'label'=>'Phone 2',
            'type'=>'text'

        ]);

        $this->crud->addField([
            'name'=>'phone2',
            'label'=>'Phone 2',
            'type'=>'text'

        ]);

        $this->crud->addField([
            'name'=>'garage_name',
            'label'=>'Nom du garage',
            'type'=>'text'

        ]);

        $this->crud->addField([
            'name'=>'address',
            'label'=>'Addresse',
            'type'=>'text'

        ]);

        //todo:: services
        $this->crud->addField([ // select_from_array
            'name' => 'services',
            'label' => "Services",
            'type' => 'select2_from_array',
            'options' => [
                "One" => "One",
                "two" => "Two"
            ],
            'allows_null' => false,
            'default' => 'one',
             'allows_multiple' => true
        ]);

        $this->crud->addField([
            'name'=>'availability',
            'label'=>'Disponibilite horaires ',
            'type'=>'select2_from_array',
            'options' => [
                'Tranche1(8ham-4hpm)' => 'Tranche1(8ham-4hpm)',
                'Tranche1(4hpm-12hpm)' => 'Tranche1(4hpm-12hpm)',
                'Tranche1(12hpm-8hpm)' => 'Tranche1(12hpm-8hpm)',
            ]

        ]);

        $this->crud->addColumn([
            'name' => 'user_name',
            'label' => 'Full Name',
            'type' =>  'model_function',
            'function_name' => 'getUserName'
        ])->beforeColumn('phone1');


         $this->crud->removeColumn('user_id'); // remove a column from the stack
        $this->crud->removeButton('delete');


        // add asterisk for fields that are required in MechanicRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users,email'
        ]);


        $user = new User();
        $user->name = $request->full_name  ;
        $user->email = $request->email;
        $user->password = bcrypt("password");
        $user->save();

        $mechanic = new Mechanic();
        $mechanic->user_id = $user->id;
        $mechanic->services = \GuzzleHttp\json_encode($request->services);
        $mechanic->sexe = $request->gender;
//        $mechanic->birthday = Carbon::parse($request->birthday)->toDateTimeString();
        $mechanic->phone1 = $request->phone1;
        $mechanic->phone2 = $request->phone2;
//        $mechanic->haveScanner = $request->scanner? 1:0;
//        $mechanic->specialisation = $request->specialisation;
        $mechanic->availability = $request->availability;
        $mechanic->save();

        $garage = new Garage();
        $garage->mechanic_id = $mechanic->id;
        $garage->name = $request->garage_name;
        $garage->addresse = $request->address;
        $garage->save();

        return redirect('admin/mechanic');

         // your additional operations before save here
//        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
//        return $redirect_location;
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
