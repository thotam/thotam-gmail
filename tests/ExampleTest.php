<?php

namespace Thotam\ThotamGmail\Tests;

use Orchestra\Testbench\TestCase;
use Thotam\ThotamGmail\ThotamGmailServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [ThotamGmailServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
