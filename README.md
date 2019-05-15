# laravel-golden-master-tests

Class for golden master tests in Laravel.

Installation
------------

```bash
$ composer require matthewbdaly/laravel-golden-master-tests
```

Usage
-----

```php
<?php

namespace Tests\GoldenMaster;

use Matthewbdaly\LaravelGoldenMasterTests\GoldenMasterTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class ExampleTest extends GoldenMasterTestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider authDataProvider
     */
    public function testAuthPages($data)
    {
        $user = factory(User::class)->create([
            'email' => 'eric@example.com',
            'name' => 'Eric Smith',
            'password' => 'password'
        ]);
        $this->actingAs($user)
            ->goto($data)
            ->saveHtml()
            ->assertSnapshotsMatch();
    }

    /**
     * @dataProvider nonAuthDataProvider
     */
    public function testNonAuthPages($data)
    {
        $this->goto($data)
            ->saveHtml()
            ->assertSnapshotsMatch();
    }

    public function authDataProvider()
    {
        return [
            ['/'],
        ];
    }

    public function nonAuthDataProvider()
    {
        return [
            ['/register'],
            ['/login'],
        ];
    }
}
```
