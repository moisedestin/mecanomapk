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

        $entry = $this->crud->getCurrentEntry();

        // TODO: remove setFromDb() and manually define Fields and Columns
//        $this->crud->setFromDb();
        $this->crud->setColumns(['phone1','phone2','availability','image','rating']);

        $this->crud->setColumnDetails('image', [
            'label' => "Image", // Table column heading
            'type' => 'image',
            'prefix' => 'storage/'
        ]);

        $this->crud->modifyField('image',[ // image
            'label' => "Mechanic Image",
            'name' => "image",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // ommit or set to 0 to allow any aspect ratio
            // 'disk' => 's3_bucket', // in case you need to show images from a different disk
            // 'prefix' => 'uploads/images/profile_pictures/' // in case you only store the filename in the database, this text will be prepended to the database value
            'prefix' => 'storage/',

        ]);

        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->user->email;
            }
        ])->makeFirstColumn();

        $this->crud->addField([
            'name'=>'full_name',
            'label'=>'Ful Name',
            'type'=>'text',
            'value' => $entry? $entry->user->name : ''
        ]);

        $this->crud->addField([
            'name'=>'small_description',
            'label'=>'Description',
            'type'=>'textarea'
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

        ],'create');

        $this->crud->addField([
            'name'=>'email',
            'label'=>'Email',
            'type'=>'email',
            'attributes' => [
                'disabled' => 'disabled'
            ],
            'value' => $entry? $entry->user->email : ''

        ],'update');

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
            'type'=>'text',
            'value' => $entry? $entry->garage->name : ''
        ]);

        $this->crud->addField([
            'name'=>'garage_address',
            'label'=>'Addresse Garage',
            'type'=>'text',
            'value' => $entry? $entry->garage->addresse : ''
        ]);

        $this->crud->addField([
            'name'=>'rating',
            'label'=>'Rating',

            'attributes' => [
                "step" => "any",
                "max" => 5,
                "min" => 0,
                'disabled' => 'disabled'
            ],
            'type'=>'number'

        ],'update');

        $this->crud->addField([ // select_from_array
            'name' => 'services',
            'label' => "Services",
            'type' => 'select2_from_array',
            'options' => [
                "Suspension" => "Suspension",
                "Electricite" => "Électricité",
                "Remorquage" => "Remorquage",
                "Climatisation" => "Climatisation"
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

        $this->crud->orderBy('created_at', 'desc');



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
        $redirect_location = parent::updateCrud($request);

        $mechanic = $this->crud->entry;
        $user = $mechanic->user;
        $garage = $mechanic->garage;

        $user->name = $request->full_name;
        $user->save();

        $garage->name = $request->garage_name;
        $garage->addresse = $request->garage_address;
        $garage->save();


        return $redirect_location;
    }
}
