# laravel-in-docker-new

- [laravel-in-docker-new](#laravel-in-docker-new)
  - [Setup](#setup)
  - [Migration](#migration)
  - [Create a Factory](#create-a-factory)
  - [Create a Model:](#create-a-model)
  - [Create a Seeder:](#create-a-seeder)
  - [Verification of created data](#verification-of-created-data)
  - [Creating API endpoint to view members](#creating-api-endpoint-to-view-members)
  - [Adding new members](#adding-new-members)
  - [Volume problem](#volume-problem)
  - [Adding some rules to StoreMemberRequest](#adding-some-rules-to-storememberrequest)
  - [Create tests](#create-tests)

## Setup

Enginx can be reached through http://localhost and not the original ip mentioned. However, this can be changed in \laravel\config\app.php

I added MySQL extension:
```sh
docker exec -it laravel-in-docker-new-app-1 sh
docker-php-ext-install pdo pdo_mysql
```


## Migration

AND after that I could run
```sh
docker exec -it laravel-in-docker-new-app-1 sh
php artisan migrate
```

After that in sh:
```sh
php artisan make:migration create_members_table
```

Modified the newest migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone_number'); 
        $table->string('zipcode')->nullable();
        $table->string('city')->nullable(); 
        $table->text('address')->nullable();
        $table->text('comment')->nullable(); 
        $table->boolean('mailing_list')->nullable(); 
        $table->timestamp('email_verified_at')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
```

AND then:

```sh
   php artisan migrate
```
## Create a Factory

**Probably the model should have been created first**

Create a Factory (If I had done it after creating a model, it would have been easier)

```sh
php artisan make:factory MemberFactory --model=Member
```

The modified factory (done by hand)

```php
<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Member;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber,
            'zipcode' => fake()->postcode,
            'city' => fake()->city,
            'address' => fake()->address,
            'comment' => fake()->text,
            'mailing_list' => fake()->boolean,
            'email_verified_at' => now(),
    ];
    }
}

```

## Create a Model:

```sh
php artisan make:model Member
```

And modify app\Models\Member.php like this:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'zipcode',
        'city',
        'address',
        'comment',
        'mailing_list',
        'email_verified_at',
    ];
}

```

## Create a Seeder:

```sh
php artisan make:seeder MemberSeeder
```

then modified \database\migrations\MemberSeeder.php like this:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Member::factory()->count(20)->create();
    }
}

```

defined the seeder and then

```sh
php artisan db:seed --class=MemberSeeder
```

## Verification of created data

You can verify that the data has been created in your database by querying the `members` table. There are several ways to do this:

1. **Using Tinker (Laravel's REPL):**
   You can use Laravel's Tinker to interact with your application from the command line. Open your terminal and run:

   ```sh
   php artisan tinker
   ```

   Then, you can query your `members` table to check if the data has been created. For example:

   ```php
   use App\Models\Member;

   // Get all members
   Member::all();

   // Get the count of members
   Member::count();
   ```

2. **Display Information of a Specific Member:**
   To display the information of a specific member, you can use the `find` method to retrieve a member by their ID and then display the attributes. In Tinker:

   ```php
   use App\Models\Member;

   // Find a member by ID
   $member = Member::find(1); // Replace 1 with the actual member ID

   // Display member information
   $member->name;
   $member->email;
   // Display other attributes as needed
   ```

   This will display the information of the member with the specified ID.

3. **Dump All Information in the Table on the Command Line:**
   To dump all the information in the `members` table on the command line, you can use the `get` method to retrieve all records and then use `dd()` (data dump) to display the data. In Tinker:

   ```php
   use App\Models\Member;

   // Get all members
   $members = Member::get();

   // Dump all member information
   dd($members);
   ```

   This will display all the information of the members in the `members` table.

## Creating API endpoint to view members

1. Create a Controller:

   ```sh
   php artisan make:controller MemberController
   ```

   This will create a `MemberController.php` file in the `app/Http/Controllers` directory.

2. Create a Route in the `routes/api.php` file:

   ```php
   //It seems that if I don't need authentication, I just simply add my route. MemberController can be imported automatically.

    Route::apiResource('members', MemberController::class);
   ```

3. Implement the `show` Method in the `\app\Http\MemberController`
   ```php
    <?php

    namespace App\Http\Controllers;

    use App\Models\Member;
    use Exception;
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
    use Illuminate\Http\Resources\Json\JsonResource;
    use Symfony\Component\HttpFoundation\Response;

    class MemberController extends Controller
    {
       public function index(): AnonymousResourceCollection
        {
            return JsonResource::collection(Member::all());
       }

       public function show(string $id): JsonResource|Response
       {
           try{
               return new JsonResource(Member::findOrFail($id));
           } catch (Exception) {
               return response(null, Response::HTTP_NOT_FOUND);
           }
       }
    }

   ```

5. Testing the API Endpoint:
   Type it in the browser:

   http://localhost/api/members/1


   Or for all the data:

   http://localhost/api/members

## Adding new members

1. Create StoreMemberRequest.php in \app\Http\Requests\
   ```sh
   php artisan make:request StoreMemberRequest
   ```
   And modify it so that it looks like this (later regexp needs to be added):

   ```php
    <?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class StoreMemberRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {   
            return true;    //supposedly this needs to be true instead
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'min:3'],
                'email' => ['required', 'email'],
               'phone_number' => ['required', 'string'],
                'zipcode' => ['string'],
                'city' => ['string'],
                'address' => ['string'],
                'comment' => ['string'],
                'mailing_list' => ['required', 'boolean'],
            ];
        }
    }

   ```
2. Implement the method in the MemberController

    Now MemberController.php looks like this, although it would be a good idea to add feedback if the action was successful or not.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return JsonResource::collection(Member::all());
    }

    public function show(string $id): JsonResource|Response
    {
        try{
            return new JsonResource(Member::findOrFail($id));
        } catch (Exception) {
            return response(null, Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreMemberRequest $request): JsonResource
    {
        $member = Member::create($request->all());
        return new JsonResource($member);
    }
}

```

3. Define the route

    I suppose it is not necessary as it was universally defined

4. Trying it out

    I am going to use the following POST request in Postman:

    ```
    http://localhost/api/members?name=John&email=john@example.com&phone_number=123456789&mailing_list=0&city=Lake Maritza

    ```
   
   The following header needed to be added:
   ```JSON
   {"header": [
					{
						"key": "Accept",
						"value": "application/json",
						"type": "text"
					},
					{
						"key": "Content-Type",
						"value": "application/json",
						"type": "text"
					}
   ]}
   ```

## Volume problem

After closing down everything, the mysql information disappeared and when trying to connect, I encountered a could not find driver error

HEre is what I did:
Added a volume to the mysql

and then:

In a terminal (let's call it laravelTerminal):
```sh
docker exec -it laravel-in-docker-new-app-1 sh
docker-php-ext-install pdo pdo_mysql
php artisan migrate
```

In an other terminal (call it dbTerminal):

```bash
docker exec -it laravel-in-docker-new-db-1 bash
mysql -u root -p
SHOW DATABASES;
USE database;
SHOW TABLES;
SELECT * FROM members;
```

Then in laravelTerminal:
```sh
php artisan db:seed --class=MemberSeeder
```

in dbTerminal the seeded members can be checked:
```bash
SELECT * FROM members;
```

The problem was solved by creating a Dockerfile and adding docker-php-ext-install pdo pdo_mysql during build.

## Adding some rules to StoreMemberRequest

I removed the regular expressions as they were not needed:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //supposedly this needs to be true instead
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:8'],
            'email' => ['required', 'email', 'min:8'],
            'phone_number' => ['required', 'string', 'min:7', 'max:20'],
            'zipcode' => ['string', 'max:15'],
            'city' => ['string', 'max:30'],
            'address' => ['string', 'max:50'],
            'comment' => ['string', 'max:250'],
            'mailing_list' => ['required', 'boolean'],
        ];
    }
}
```

## Create tests

First bash in and create MemberTest:

```sh
docker exec -it laravel-in-docker-new-app-1 sh
php artisan make:test MemberTest
```

Check what I need to test:
In app\Http\Controllers there is MemberController that has three functions:
- index()
- show(string $id)
- store(StoreMemberRequest $request)
We can write test cases for these.

Then write the test cases:
MemberTest.php looks like this after wtiting the index testcase:

```php
<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;    //This would reset the database, so if entries were present when testing, they would be lost.
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;
    private const BASE_ENDPOINT = '/api/members/';

    public function testMember_index(): void
    {
        $members = Member::factory()->count(3)->create();
        $memberIds = $members->map(fn(Member $member) => $member->id)->toArray();
        $response = $this->get(self::BASE_ENDPOINT)->json('data');
        $this->assertCount($members->count(), $response); //for empty database
        
        foreach ($response as $responseMember) {
            $this->assertContains($responseMember['id'], $memberIds);
        }
    }
    
    public function testMember_store(): void 
    {
        $member = Member::factory()->make();
        $response = $this->post(self::BASE_ENDPOINT, $member->toArray())->json('data');
        $this->assertNotNull($response['id']);
        $this->assertEquals($member->name, $response['name']);
        $this->assertEquals($member->email, $response['email']); 
        $this->assertEquals($member->phone_number, $response['phone_number']);
    }

    public function testMember_show(): void
    {
        $member = Member::factory()->count(3)->create()->random();
        $response = $this->get(self::BASE_ENDPOINT . $member->id)->json('data');
        $this->assertEquals($member->id, $response['id']); 
        $this->assertEquals($member->name, $response['name']);
        $this->assertEquals($member->email, $response['email']);
        $this->assertEquals($member->phone_number, $response['phone_number']);
    }
    
    
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

```

Then run the test:

```sh
php artisan test
```

With use DatabaseTransactions; it does not delete the existing data.