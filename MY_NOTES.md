# BACK-END Y AUTH EN LARAVEL

## despues de la creación del proyecto, la base de datos y modificar el archivo .env para ligar la base de datos
- levantamos el servidor
    > php artisan serve
- creamos el auth con laravel
    > php artisan make:auth
- version laravel 7
    > composer require laravel/ui:^2.4
    > php artisan ui vue --auth
- y ahora veremos unos botones de login/register en la pantalla princioal y en los cuales podremos dar clic.
- ahora debemos migrar las BD para que estas vistas tengan de donde obtener datos.
    > php artisan migrate
- ahora en login podemos entrar cualquier dato y nos deberia arrojar que no existe.

- posiblemente nuestro auth no tengo style, agregamos la linea de boostrap
    > en el head
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    > al final del body
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
            <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
            <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

 - verificamos que se puedan crear los usuarios y aparezcan en la base de datos

 # Roles
 - en la vista podemos agrega opcion a vista de admin y de usuario

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin') }}">Administrador</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('user') }}">Usuario</a>
    </li>

- su respectiva vista

    @extends('layouts.app')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Admin</div>

                    <div class="card-body">
                        Estas logeado como administrador.
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

- su ruta:
> Route::get('/home/admin', 'HomeController@admin')->name('admin');
> Route::get('/home/user', 'HomeController@user')->name('user');

- creamos la tabla de roles:
    > php artisan make:migration create_roles_table --create=roles
- agregamos sus atributos:
    > $table->id();
    > $table->string('name');
    > $table->timestamps();

- creamos su modelo
    > php artisan make:model Rol

- lo ligamos a su respectiva tabla
    > protected $table = "roles";

# llave foránea
- creamos la llave foranea que liga el campo rol_id a la tabla roles
    > php artisan make:migration add_user_roles_table --table=users

- tenemos que crear las relaciones entre las tablas:
- vamos al modelo user.php
    > public function rol(){
    >    // el user tiene un rol | indicamos donde esta el rol | el nombre de ese campo | el nombre del campo en user 
    >    return $this->hasOne('App\Rol', 'id', 'rol_id'); }
- de una forma visual podemos agregarla directamente en el panel de phpmyadmin
- y deberiamos poder dar clic en esa llave y nos deberia llevar a la tabla (es una forma de probar)

- hacemos la migracion
    > php artisan migrate

# SEEDERS
- creamos un seeder para tener valores por defecto en la tabla roles
    > php artisan make:seed NOMBRETableSeeder

- agregamos el seed a la invocacion principal en 'DatabaseSeeder.php'
    > public function run()
    {
        $this->call(StartSeed::class);
    }

- agregamos los datos iniciales en el seeder creado:
    >public function run()
    {
        $userRol = new Rol();
        $userRol->name = "user";
        $userRol->save();

        $userRol = new Rol();
        $userRol->name = "Admin";
        $userRol->save();
    }
- limpiamos el cache y preparamos los seeds:
    > composer dump-autoload

- si queremos solo insertar una seeder, podemos usar el comando
    > php artisan db:seed --class=NOMBRETableSeeder

- si queremos que todos los seeders se ejecuten seria
    > php artisan db:seed

# MODIFICAR EL FORM DEL AUTH DE LARAVEL
- primero ir al archivo raiz, app>http>controller>auth>reister
    > use RegistersUsers; y dar ctrl+clic en 'RegisterUsers' nos mandara a la base de laravel y copiamos la función:

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

- ahora la podemos modificar, en este caso pasaremos todos los roles a la vista con 'with'

    > public function showRegistrationForm(){
    >    $roles = Rol::all();
    >    return view('auth.register')->with('roles', $roles);    }

- ahora en la vista register.blade.php, agregamos en cualquier parte
    > {{ dd($roles) }} 
    para verificar si estamos recibiendo los datos.

- agregamos los roles a la vista
    <div class="form-group row">
        <label for="rol" class="col-md-4 col-form-label text-md-right">Rol</label>

        <div class="col-md-6">
            <select name="rol_id" id="rol_id" class="form-control">
                <option value="0">Selecciona un rol</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->id }}"> {{ $rol->name }}</option>    
                @endforeach
            </select>
        </div>
    </div>

- y ahora la funcionalidad para guardarlo en la base de datos, vamos a registerController.php
    > protected function create(array $data){
    >   return User::create([
    >        'name' => $data['name'],
    >        'email' => $data['email'],
    >        'password' => Hash::make($data['password']),
    >        'rol_id' => $data['rol_id'],
    >    ]);}

- y tambien tenemos que agregar el fillable, si no, no existiria, vamos al Model, User.php
    > protected $fillable = [
    >    'name', 'email', 'password', 'rol_id'    ];