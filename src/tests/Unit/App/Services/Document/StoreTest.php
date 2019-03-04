<?php

namespace Tests\Unit\Services\Elasticsearch;

use App\Models\Elasticsearch\Attachment;
use App\Services\Document\Store;
use App\Services\Elasticsearch\Document;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;
use Illuminate\Routing\UrlGenerator;

class StoreTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @test
     *
     * @return void
     */
    public function uploadShouldStoreDocuments()
    {
        $tags = ['baz'];
        $files = [
            UploadedFile::fake()->create('foo.pdf', 10)
        ];

        $id = 1;
        $attachment = new Attachment();
        $attachment->fileName = 'foo.pdf';
        $attachment->contentType = 'application/pdf';
        $attachment->content = '';
        $result = [
            '_shards' => ['successful' => true],
            '_id' => $id
        ];

        $elastic = Mockery::mock(Document::class)
            ->shouldReceive('storeDocument')
            ->once()
            #->withArgs([$attachment, array_values($tags)])
            ->andReturn($result)
            ->getMock();
        $url = Mockery::mock(UrlGenerator::class)
            ->shouldReceive('route')
            ->once()
            ->withArgs(['download-document', ['id' => $id]])
            ->andReturn('/document/' . $id)
            ->getMock();

        $store = new Store($elastic, $url);

        $actual = $store->upload($tags, $files);

        $this->assertEquals(
            ['fileName' => 'foo.pdf', 'url' => '/document/' . $id, '_id' => $id],
            $actual[0]
        );
    }
}
