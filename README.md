# laravel-in-docker-new


## Setup

Enginx can be reached through http://localhost and not the original ip mentioned. However, this can be changed in \laravel\config\app.php

I added MySQL extension:
```bash
docker exec -it laravel-in-docker-new-app-1 bash
docker-php-ext-install pdo pdo_mysql
```


## Migration

AND after that I could run
```bash
docker exec -it laravel-in-docker-new-app-1 bash
php artisan migrate
```

After that in bash:
```bash
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
        $table->boolean('mailinglist')->nullable(); 
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

```bash
   php artisan migrate
```
## Create a Factory

**Probably the model should have been created first**

Create a Factory (If I had done it after creating a model, it would have been easier)

```bash
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
            'mailinglist' => fake()->boolean,
            'email_verified_at' => now(),
    ];
    }
}

```

## Create a Model:

```bash
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
        'mailinglist',
        'email_verified_at',
    ];
}

```

## Create a Seeder:

```bash
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

```bash
php artisan db:seed --class=MemberSeeder
```

## Verification of created data

You can verify that the data has been created in your database by querying the `members` table. There are several ways to do this:

1. **Using Tinker (Laravel's REPL):**
   You can use Laravel's Tinker to interact with your application from the command line. Open your terminal and run:

   ```bash
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

   ```bash
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
