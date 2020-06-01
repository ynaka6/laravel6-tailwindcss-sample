## 📝 Mutli Auth機能を利用して管理画面を作成

LaravelのMulti Authの機能を利用して管理画面を作成します。

### [前提として]
- DBのセットアップができていること
- 管理画面では登録処理など利用しません。認証はログイン画面のみとします。

### 1. Adminモデルを作成

```
$ php artisan make:model -m Models/Admin
```

`-m`オプションでマイグレーションファイルを同時に作成します。  
`database/migrations/yyyy_mm_dd_xxxx_create_admins_table` ファイルが作成されていることを確認してください。  

※ 「yyyy_mm_dd_xxxx」はタイムスタンプです


### 2. migrate実行し、adminsテーブルを作成

1で作成したマイグレーションファイル「`database/migrations/yyyy_mm_dd_xxxx_create_admins_table`」を以下のように修正します。

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
```

その後、migrateを実行します。

```
$ php artisan migrate
```

### 3. Adminモデルの修正

`app/Models/Admin.php`を以下のように修正します。

```
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
```

### 4. Adminモデルの初期データをtinkerで作成する

以下のコマンドでtinkerを起動します。

```
$ php artisan tinker
```

tinker起動後に以下のコマンドでデータを作成します。

```
use App\Models\Admin;

Admin::create([
  'name' => '管理者',
  'email' => 'admin@sample.com',
  'password' => bcrypt('password'),
]);
```

※ Emailやパスワードはご自身で指定してください。
※ phpMyAdminやTablePlusなどDB管理ツールでデータが登録されたことを確認しましょう。


### 5. auth.phpにadmin設定を追加

`config/auth.php`にadmin用のGuardを追加します。  
以下のように修正してください。

```
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
        // ##### 追加 ##### //
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
        // ##### ここまで ##### //
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        // ##### 追加 ##### //
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
        // ##### ここまで ##### //

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],

        // ##### 追加 ##### //
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_resets',
            'expire' => 60,
        ],
        // ##### ここまで ##### //
    ],

];
```

### 6. Exceptions/Handler.phpの修正

`app/Exceptions/Handler.php`を修正し、adminの認証エラーの処理を変更します。  
以下のように修正してください。

```
<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

// ##### 追加 ##### //
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
// ##### ここまで ##### //

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    // ##### 追加 ##### //
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if(in_array('admin', $exception->guards())){
            return redirect()->guest('admin/login');
        }
        return redirect()->guest(route('login'));
    }
    // ##### ここまで ##### //
}

```

### 7. ルーティングを定義

管理画面用のルーティングを定義します。

`app/Providor/RouteServiceProvider.php` に `admin` 用のルーティングを追加します。  
以下のように修正してください。


```
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        // ##### 追加 ##### //
        $this->mapAdminRoutes();
        // ##### ここまで ##### //
        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    // ##### 追加 ##### //
    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
             ->middleware('web')
             ->as('admin.')
             ->namespace($this->namespace)
             ->group(base_path('routes/admin.php'));
    }
    // ##### ここまで ##### //

}
```


`routes/admin.php` に 最低限必要となるルーティングを用意します。  
以下のようにファイルを追加してください。

```
<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('login', 'Admin\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Admin\Auth\LoginController@login')->name('login');

Route::group(['middleware' => 'auth:admin'], function() {
    Route::post('logout', 'Admin\Auth\LoginController@logout')->name('logout');

    Route::get('/', 'Admin\HomeController@index')->name('home');
});
```

### 8. 

