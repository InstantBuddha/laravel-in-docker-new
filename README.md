# laravel-in-docker-new

- [laravel-in-docker-new](#laravel-in-docker-new)
  - [Setup](#setup)
  - [Migration](#migration)
  - [Create a Model:](#create-a-model)
  - [Create a Factory](#create-a-factory)
  - [Create a Seeder:](#create-a-seeder)
  - [Verification of created data](#verification-of-created-data)
  - [Creating API endpoint to view members](#creating-api-endpoint-to-view-members)
  - [Adding new members](#adding-new-members)
  - [Volume problem](#volume-problem)
  - [Adding some rules to StoreMemberRequest](#adding-some-rules-to-storememberrequest)
  - [Create tests](#create-tests)
  - [Emails: Make:mail](#emails-makemail)
      - [Undo the Mistaken Action:](#undo-the-mistaken-action)
    - [Creating the envelope](#creating-the-envelope)
    - [Changing the official Laravel email template](#changing-the-official-laravel-email-template)
  - [Solving the bind mount permission issue](#solving-the-bind-mount-permission-issue)
  - [Adding mailcathcher](#adding-mailcathcher)
  - [Events](#events)
  - [Testing Emails](#testing-emails)
  - [Adding a closure](#adding-a-closure)
    - [Cleaning up the event listener](#cleaning-up-the-event-listener)
  - [Rate Limiting](#rate-limiting)
  - [Cross-Origin Resource Sharing (CORS)](#cross-origin-resource-sharing-cors)
  - [Test rate limiting](#test-rate-limiting)
  - [Add authentication](#add-authentication)
    - [Uncomment the following line in app/Http/Kernel.php](#uncomment-the-following-line-in-apphttpkernelphp)
    - [Modify routes/api.php](#modify-routesapiphp)
    - [Create controller](#create-controller)
    - [Create a User manually](#create-a-user-manually)
    - [Set token expiration](#set-token-expiration)
    - [Add a protected API route and change members routes](#add-a-protected-api-route-and-change-members-routes)
    - [IMPORTANT Postman settings](#important-postman-settings)
  - [Adding React frontend](#adding-react-frontend)

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

    //when the mailable is ready, add the closure:
    protected static function booted(): void {
        static::created(function (Member $member) {
            Mail::send(new WelcomeEmail($member));
        });
    }
}

```
## Create a Factory

It creates a factory in database\factories\
```sh
php artisan make:factory MemberFactory --model=Member
```

The modified factory (done by hand)

```php
<?php

declare(strict_types=1);

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
        $memberProperties = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber,
            'mailing_list' => fake()->boolean,
            'email_verified_at' => now(),
        ];

        if (fake()->boolean(60)) {
            return array_merge($memberProperties,
                ['zipcode' => fake()->postcode,
                    'city' => fake()->city,
                    'address' => fake()->address,
                    'comment' => fake()->text,]
            );
        }

        return $memberProperties;
    }
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
docker exec -it laravel-in-docker-new-app-1 sh
php artisan test
```

With use DatabaseTransactions; it does not delete the existing data.

## Emails: Make:mail

In the laravel app, create the

```sh
docker exec -it laravel-in-docker-new-app-1 sh
php artisan make:mail WelcomeEmail --markdown=emails.welcome
```

This created WelcomeEmail.php in the app\Mail folder

If you've accidentally created a mail class without the `--markdown` option and want to correct it, you can follow these steps to undo the first action and create a markdown email:

#### Undo the Mistaken Action:

1. **Delete the Incorrect Mail Class:**
   - Delete the mail class that was created without the `--markdown` option. The mail class is typically located in the `App\Mail` directory. For example:
     ```bash
     rm app/Mail/WelcomeEmail.php
     ```
     or
     ```bash
     del app/Mail/WelcomeEmail.php
     ```

2. **Create the Markdown Email:**
   - Run the correct `make:mail` command with the `--markdown` option:
     ```bash
     php artisan make:mail WelcomeEmail --markdown=emails.welcome
     ```
     This will create a new mail class named `WelcomeEmail` with a corresponding Markdown template in the `resources/views/emails` directory.

3. **Update the Markdown Template (Optional):**
   - Open the generated Markdown template (`resources/views/emails/welcome.blade.php`) and customize it according to your needs.

### Creating the envelope

Using a global from address:
you may specify a global "from" address in your config/mail.php configuration file. This address will be used if no other "from" address is specified within the mailable class. I modified it:

Here, it uses the .env values IF PRESENT. As the reply_to value is not set there, Laravel uses the value that is set here in config/mail.php

```php
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'fallback_set_in_mail_php@example.hu'),
        'name' => env('MAIL_FROM_NAME', 'FallbackNameSet InMailPhp'),
    ],
    
    'reply_to' => [
        'address' => env('MAIL_REPLY_TO_ADDRESS', 'fallback_replyto_set_in_mail_php@example.hu'),
        'name' => env('MAIL_REPLY_TO_NAME', 'FallbackNameSet InMailPhp'),
    ],
    'bcc' => [
        env('MAIL_BCC_ADDRESS_1', 'fallback_bcc1_set_in_mail_php@example.hu'),
        env('MAIL_BCC_ADDRESS_2', 'fallback_bcc2_set_in_mail_php@example.hu'),
    ],
```

In WelcomeEmail.php the envelope looked like this in the end:

```php
public function envelope(): Envelope
    {
        return new Envelope(
            from: env('MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME'),
            to: $this->member->email,
            subject: 'Welcome Email',
            bcc: [
                env('MAIL_BCC_ADDRESS_1'),
                env('MAIL_BCC_ADDRESS_2'),
            ],
        );
    }
```

### Changing the official Laravel email template

First I need to publish the Laravel email views:

```bash
php artisan vendor:publish --tag=laravel-mail
```

The above command will copy the email views to your project's resources/views/vendor/mail directory. In that directory, you'll find several Blade templates, including html/message.blade.php. They can be edited.

**The templates are different for HTML and for plain text!**

## Solving the bind mount permission issue

If files are created by a container, they require sudo to be changed, unless we set the permissions:

for the whole project:

```bash
chmod -R u+rwx ~/NEW_PROGRAMMING/laravel-in-docker-new
chmod -R g+rx ~/NEW_PROGRAMMING/laravel-in-docker-new
chmod -R o+rx ~/NEW_PROGRAMMING/laravel-in-docker-new

sudo chown -R dan:dan ~/NEW_PROGRAMMING/laravel-in-docker-new
```

for an individual file inside the folder on the host machine

```bash
sudo chmod u+rwx MailableTest.php
sudo chmod g+rx MailableTest.php
sudo chmod o+rx MailableTest.php

sudo chown dan:dan MailableTest.php
```

## Adding mailcathcher

The docker-compose.yml needed to be modified the following way:

```yml
version: "3"
services:
  app:
    build:
      context: ./
      dockerfile: Dockerfile
    container_name: laravel-in-docker-new-app-1
    working_dir: /app
    volumes:
      - .:/app
    ports:
      - "8000:8000"
    depends_on:
      - web
      - db
    networks:
      - laravel-in-docker-new-network    
  web:
    image: nginx:alpine
    container_name: laravel-in-docker-new-web-1
    ports:
      - "80:80"
    volumes:
      - .:/app
      - ./nginx-config:/etc/nginx/conf.d 
    networks:
      - laravel-in-docker-new-network
  db:
    image: mysql:8.1
    container_name: laravel-in-docker-new-db-1
    environment:
      MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
      MYSQL_ROOT_HOST: "%"
      MYSQL_DATABASE: '${DB_DATABASE}'
      MYSQL_PASSWORD: '${DB_PASSWORD}'
      MYSQL_ALLOW_EMPTY_PASSWORD: 0
    volumes:
      - db-data:/var/lib/mysql
    ports:
      - 3306:3306
    networks:
      - laravel-in-docker-new-network  
  mailcatcher:
    image: schickling/mailcatcher
    container_name: laravel-in-docker-new-mailcatcher-1
    ports:
      - "1080:1080"
      - "1025:1025"
    networks:
      - laravel-in-docker-new-network    
volumes:
  db-data:
networks:
  laravel-in-docker-new-network:  
```

And in .env the following information needed to be set with 1 new line:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailcatcher
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="set_in_env@example.hu"
MAIL_FROM_NAME="OurName SetInDotEnv"
MAILCATCHER_PORT_1025_TCP_ADDR=0.0.0.0
```
For testing web.php was modified with the following:

```php
<?php

use App\Mail\WelcomeEmail;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

//other routes

Route::get('/testemail', function () {
    $exampleMember = Member::factory()->make();

    Mail::to($exampleMember->email)->send(new WelcomeEmail($exampleMember));

    return 'Test email sent';
});
```

With these settings, a test email can be sent at:
http://localhost/testemail
The emails can be checked at:
http://localhost:1080/


## Events

Working according to the following:
https://laravel.com/docs/10.x/events#generating-events-and-listeners

1. **Create an Event:**
   - Run the following command to generate an event class:

     ```bash
     php artisan make:event MemberRegistered
     ```

   - This will create a file in the `app/Events` directory, like `MemberRegistered.php`.

2. **Modify the Event Class:**
   - Modify `MemberRegistered.php` slightly

     ```php
     public function __construct(public Member $member)
    {

    }
     ```

3. **Create a Listener:**
   - Run the following command to generate a listener class:

     ```bash
     php artisan make:listener SendWelcomeEmail --event=MemberRegistered
     ```

   - This will create a file in the `app/Listeners` directory, like `SendWelcomeEmail.php`.

4. **Modify the Listener (`SendWelcomeEmail.php`):**

```php
<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\MemberRegistered;
use App\Mail\WelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MemberRegistered $event): void
    {
        $member = $event->member;

        Mail::to($member->email)->send(new WelcomeEmail($member));
    }
}
```
5. Manually registering the events
   Modify EventServiceProvider.php like this:

   ```php
   public function boot(): void
    {
        Event::listen(
            MemberRegistered::class,
            SendWelcomeEmail::class,
        );
    }
   ```
6. **Dispatch the Event:**

https://laravel.com/docs/10.x/events#dispatching-events

   In MemberController.php change the part:

     ```php
     public function store(StoreMemberRequest $request): JsonResource
    {
        $member = Member::create($request->all());
        MemberRegistered::dispatch($member);
        return new JsonResource($member);
    }
     ```

## Testing Emails

Working with this:

https://laravel.com/docs/10.x/mail#testing-mailable-content

In the end the test for the mailable looks like this:

```php
public function test_mailable_content(): void
    {
        Mail::fake();

        $member = Member::factory()->make();

        $mailable = new WelcomeEmail($member);

        Mail::send($mailable);

        Mail::assertSent(WelcomeEmail::class, function (WelcomeEmail $mail) use ($member) {
            $mail->hasTo($member->email);
            $mail->hasFrom('set_in_env@example.hu', 'OurName SetInDotEnv');
            $mail->hasReplyTo('replyToAddress@inmailphp.com', 'OurReplyToAddress InMailPhp');
            $mail->hasSubject('Welcome Email');
            return true;
        });

        $mailable->assertSeeInHtml($member->name);
        $mailable->assertSeeInHtml($member->phone_number);
        $mailable->assertSeeInHtml('Successful registration');
        $mailable->assertSeeInHtml('árvíztűrő tükörfúrógép');
        $mailable->assertSeeInHtml('A hardcoded company name');
        if($member->address){
            $mailable->assertSeeInHtml($member->address);
        }
        if($member->comment){
            $mailable->assertSeeInHtml($member->comment);
        }
        if($member->mailing_list){
            $mailable->assertSeeInHtml('You have chosen to receive our newsletter.');
        }
        $mailable->assertSeeInText($member->name);
        $mailable->assertSeeInText($member->phone_number);
        $mailable->assertSeeInText('Successful registration');
        $mailable->assertSeeInText('árvíztűrő tükörfúrógép');
        $mailable->assertSeeInText('A hardcoded company name');
        if($member->address){
            $mailable->assertSeeInText($member->address);
        }
        if($member->comment){
            $mailable->assertSeeInText($member->comment);
        }
        if($member->mailing_list){
            $mailable->assertSeeInText('You have chosen to receive our newsletter.');
        }
    }
```

For testing by hand the following was added to web.php
```php
Route::get('/testemail', function () {
    $exampleMember = Member::factory()->make();
    
    Mail::send(new WelcomeEmail($exampleMember));
    return 'Test email sent';
});
```
## Adding a closure

Working based on this:
https://laravel.com/docs/10.x/eloquent#events-using-closures

Just added the following to the end of Member class in Member.php:
```php
protected static function booted(): void
    {
        static::created(function (Member $member) {
            Mail::send(new WelcomeEmail($member));
        });
    }
```

Or, with bcc:

```php
protected static function booted(): void {
        static::created(function (Member $member) {
            Mail::bcc(['bcc1@example.com', 'bcc2@example.com'])
                ->send(new WelcomeEmail($member));
        });
    }
```

### Cleaning up the event listener

remove the Events, Listeners directories:
```sh
sudo rm -r Events
sudo rm -r Listeners
```

In EventServiceProvider.php emptied the boot() method and the imports of MemberRegistered and SendWelcomeEmail classes.

In MemberController, removed the MemberRegistered::dispatch line with its import.

## Rate Limiting

Following this:
https://laravel.com/docs/10.x/routing#defining-rate-limiters

for global:
https://laracasts.com/discuss/channels/laravel/global-rate-limiting-for-routes

The built in app/providers/RouteServiceProvider needed to be modified like this:

```php
public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('web', function (Request $request){
            return Limit::perMinute(120)->by($request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->middleware('throttle:web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
   
        });
    }
```

## Cross-Origin Resource Sharing (CORS)

On the same page they mention Cross-Origin Resource Sharing (CORS) which could be used instead of validating frontend.

https://laravel.com/docs/10.x/routing#rate-limiting

## Test rate limiting

Official documentation:
https://laravel.com/docs/10.x/http-tests

In the end, the test looks like this:
```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_ENDPOINT = '/api/members/';
    private const RATE_LIMIT = 60;

    public function test_rate_limit()
    {
        for ($i = 1; $i <= self::RATE_LIMIT; $i++) {
            $member = Member::factory()->make();
            $this->withMiddleware(['api']);
            $response = $this->post(self::BASE_ENDPOINT, $member->toArray())
                ->assertCreated()
                ->assertHeader('X-Ratelimit-Limit', self::RATE_LIMIT)
                ->assertHeader('X-Ratelimit-Remaining', self::RATE_LIMIT - $i);
        }

        $member = Member::factory()->make();
        $this->withMiddleware(['api']);
        $response = $this->post(self::BASE_ENDPOINT, $member->toArray())
            ->assertStatus(429);
    }

}

```

## Add authentication

Following this (which turns out to be partly bad information):
https://www.laravelia.com/post/how-to-create-api-with-sanctum-authentication-in-laravel-10

Later followed this:
https://dev.to/shanisingh03/laravel-api-authentication-using-laravel-sanctum-edg

### Uncomment the following line in app/Http/Kernel.php

```php
\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
```

### Modify routes/api.php

I suppose it would have been better to create the controller first, but:

```php
Route::controller(AuthController::class)->group(function(){
    Route::post('login', 'login');
});
```

### Create controller

sh in and
```sh
docker exec -it laravel-in-docker-new-app-1 sh
php artisan make:controller API/AuthController
```

(to change permissions on host machine:)

```bash
sudo chmod u+rwx AuthController.php
sudo chmod g+rx AuthController.php
sudo chmod o+rx AuthController.php

sudo chown dan:dan AuthController.php
```

Modify it like this:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login',]]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $user->createToken('ApiToken')->plainTextToken,
                    'type' => 'bearer',
                ]
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }
}
```

At this point, with an empty post request like this:

http://localhost/api/login

There is a 200 response with "hello world"

### Create a User manually

To create a hash of the password "testPassword"
sh in to the Laravel container and

```sh
docker exec -it laravel-in-docker-new-app-1 sh
php artisan tinker
bcrypt('testPassword')
```
It will create the hash seen in the next bash commands

To create the user manually bash into the db container and

```bash
docker exec -it laravel-in-docker-new-db-1 /bin/bash
mysql -u root -p
SHOW DATABASES;
USE database;
SHOW TABLES;
INSERT INTO users (name, email, password) VALUES ('Tester One', 'testerone@example.com', "$2y$10$2D1AeM4o9YPBdgxQOUnyIO.Mb6pS8F7c6dl4Em.arnPRVSsNpSqnq");
```

Now the following POST request in postman will work:
http://localhost/api/login?email=testerone@example.com&password=testPassword


### Set token expiration

In app/config, there is sanctum.php and there change:
```php
'expiration' => 525600,
```

To get rid of the expired tokens, it is a good idea to prune them regularly:
Change App\Console\Kernel.php

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('sanctum:prune-expired')->daily();
}
```

### Add a protected API route and change members routes

In AuthController add
```php
    public function something(Request $request)
    {
        return response()->json([
            'message' => 'Something happened successfully',
        ]);
    }
```

At this point if authorization is not set correctly it just gives back a general 500 error message

In api.php add the line:

```php
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->get('/auth/something', [AuthController::class, 'something']);

Route::middleware(['auth:sanctum'])->post('/auth/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->apiResource('members', MemberController::class)->except(['store']);

Route::post('/members', [MemberController::class, 'store']);
```

### IMPORTANT Postman settings

In Postman, the following POST request needs to be created **WITH A SET API BEARER TOKEN IN AUTH** (set by hand)

http://localhost/api/something

**FURTHERMORE**, A key/value pair needs to be added in the headers, so that Postman will expect and accept **only** a JSON in response

In Headers uncheck Accept */* and add
Accept application/json
instead!!!

Saving the bearer token can be automatized using variables and tests, see the postman collection file.

## Adding React frontend

First build the basic files with the command in the already created directory (react-frontend):
```
docker run -it --rm -v $(pwd):/react-frontend -w /react-frontend node:14-alpine sh -c "npx create-react-app ."
```

checking the container separately:
```
docker run -it --rm --name react-frontend -p 3000:3000 -v $(pwd):/react-frontend -w /react-frontend node:14-alpine sh -c "npm install && npm start"
```