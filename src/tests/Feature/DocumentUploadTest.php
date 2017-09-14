<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentUploadTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function postWithNoArgumentsShouldSucceed()
    {
        /** @var TestResponse $response */
        $response = $this->post("/document");
        $response->assertStatus(200);
        $response->assertJson([]);
    }
}
