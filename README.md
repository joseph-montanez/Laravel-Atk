Laravel ATK
===============

Laravel ATK is a Laravel integration package for Agile UI & Agile Data.

## Installation

Begin by installing this package through Composer. Run this command from the Terminal:

```bash
composer require joseph-montanez/atk-laravel
```

##Usage

### Agile UI Integration

There is an app layer designed for Laravel that you need to use in order to leverage Agile UI within a controller.


#### 1. Initialize Agile UI

Normally to initialize Agile UI, you call the following code:

    $app = new \atk4\ui\App();
    
But since there is a custom app layer you have two options:

    //-- Directly call the integrated app class
    $app = new \Atk\Laravel\Ui\App(['title' => 'My Admin']);

    //-- Using Laravel's service provider to keep track of app instance
    $app = app()->make(\Atk\Laravel\Ui\App::class, ['title' => 'My Admin']);

#### 2. Using Agile UI Within A Controller

To get Agile UI to render you need to return a response, as echoing output has been disabled.

    class RoleController
    {
        public function index(Request $request, DB $db, Common $ui)
        {
            $app = app()->make(\Atk\Laravel\Ui\App::class, ['title' => 'My Admin']);
    
            /**
             * The List Header
             *
             * @var \atk4\ui\Header $header
             */
            $header = $ui->app->add('Header', ['Hello There!']);
    
            return response($app->run());
        }
    }

#### 3. Using Laravel's Names Routes inside Link

If you depend on Laravel's route named generator then you'll need to use this package's varient of Link. This allows a callback with the record / row data to be passed into your route.

    /**
     * The User Grid
     *
     * @var \atk4\ui\Grid $grid
     */
    $grid = $userListColumn->add('Grid');
    $grid->setModel(new User($atkDb), ['name', 'email', 'phone', 'created_at']);
    $grid->addDecorator(
        'name',
        new \Atk\Laravel\Ui\TableColumn\Link(null, [], function ($row) {
            return route('admin.user.edit', ['id' => $row['id']]);
        })
    );

### Agile Database Connection

If you are planning on using Agile Data, then you can use the service provide to take the existing database connection and pass it into Agile Data.


    $atkDb = app()->make(\Atk\Laravel\Data\Persistence_SQL::class);
    
    /**
     * @var \atk4\ui\CRUD $crud
     */
    $crud = $app->add('CRUD');
    $crud->setModel(new Permission($atkDb),  ['name', 'group_name', 'guard_name', 'created_at']);