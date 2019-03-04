<?php

namespace Tests\Unit\Services\Elasticsearch;

use App\Models\Elasticsearch\Attachment;
use App\Services\Elasticsearch\Document;
use Elasticsearch\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentTest extends TestCase
{
    /**
     * @var Document
     */
    private $service;

    public function setUp()
    {
        parent::setUp();

        /** @var Client $client */
        $client = $this->app->make(Client::class);

        $this->service = new Document($client);

        $this->service->dropIndex();
        $this->service->ensureIndex();
    }

    /**
     * @test
     *
     * @return void
     */
    public function storeDocumentShouldAddDocumentToElasticsearch()
    {
        $document = $this->createDocument();

        $result = $this->service->storeDocument($document);

        $this->assertEquals('brisklypapers_testing',  $result['_index']);
        $this->assertEquals('documents',  $result['_type']);
        $this->assertEquals(true,  $result['created']);
        $this->assertNotNull($result['_id']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function clientShouldStoreAndRetrieveDocument()
    {
        $document = $this->createDocument();

        $result = $this->service->storeDocument($document, $document->tags);
        $document->id = $result['_id'];

        $actual = $this->service->loadById($result['_id']);

        $this->assertEquals($document, $actual);
    }

    /**
     * @test
     *
     * @return void
     */
    public function storeDocumentShouldUpdateDocument()
    {
        $document = $this->createDocument();

        $result = $this->service->storeDocument($document, $document->tags);
        $document->id = $result['_id'];
        $document->text = 'update me';
        $result = $this->service->storeDocument($document, $document->tags);

        $actual = $this->service->loadById($result['_id']);

        $this->assertEquals($document, $actual);
    }

    /**
     * @return Attachment
     */
    private function createDocument()
    {
        $document = new Attachment();
        $document->content = 'content';
        $document->text = 'text content';
        $document->contentType = 'plain/txt';
        $document->fileName = 'document_test.txt';
        $document->tags = ['tag1', 'tag2'];
        return $document;
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function loadByIdThrowsAnExceptionIfDocumentIsNotAvailable()
    {
        $this->service->loadById('dummy');
    }

    /**
     * @test
     */
    public function searchShouldReturnSomeDocuments()
    {
        $document = $this->createDocument();

        $document->text = 'This document should be found by term Elasticsearch';
        $this->service->storeDocument($document, $document->tags);

        $document->text = 'This document should also be found by term Elasticsearch';
        $this->service->storeDocument($document, $document->tags);

        $document->text = 'This document should not be found';
        $this->service->storeDocument($document, $document->tags);

        $result = $this->wait(function() {
            $result = $this->service->search('Elasticsearch');
            if (2 === count($result)) {
                return $result;
            }
            return false;
        });

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Attachment::class, $result[0]);
        //$this->assertEquals('This document should be found by term Elasticsearch', $result[0]->text);
        $this->assertInstanceOf(Attachment::class, $result[1]);
        //$this->assertEquals('This document should also be found by term Elasticsearch', $result[1]->text);
    }

    /**
     * @test
     */
    public function searchShouldReturnDocumentByTag()
    {

        $document = $this->createDocument();

        $document->text = 'This document should be found by term ELasticsearch';
        $this->service->storeDocument($document, ['tag2', 'tag3']);

        $document->text = 'This document should also be found by term ELasticsearch';
        $this->service->storeDocument($document, ['tag1', 'tag3']);

        $document->text = 'This document should not be found';
        $this->service->storeDocument($document, ['tag1', 'tag2']);

        $result = $this->wait(function() {
            $result = $this->service->search('tag1');
            if (2 === count($result)) {
                return $result;
            }
            return false;
        });

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Attachment::class, $result[0]);
        $this->assertInstanceOf(Attachment::class, $result[1]);
    }

    /**
     * @param \Closure $callback
     * @param int $max in microsenconds
     * @param int $sleep in microseconds
     * @return bool
     */
    protected function wait(\Closure $callback, int $max = 2000000, int $sleep = 500000)
    {
        $start = microtime(true);

        do {
            if ($result = $callback()) {
                return $result;
            }
            usleep($sleep);
        } while(microtime(true) - $start < $max / 1000000);

        $this->fail('expected results were not delivered in time');
    }

    /**
     * @test
     */
    public function searchTagsShouldReturnTagsAndDensity()
    {

        $document = $this->createDocument();
        $this->service->storeDocument($document, ['react', 'php', 'react-native']);

        $tags = $this->wait(function() {
            $tags = $this->service->searchTags('react');
            if (!empty($tags)) {
                return $tags;
            }

            return false;
        });

        $expected = [
            ['value' => 'react', 'text' => 'react'],
            ['value' => 'react-native', 'text' => 'react-native']
        ];
        $this->assertEquals($expected, $tags);
    }

    /**
     * @test
     */
    public function dropUnkownIndexDoesNotThrowAnException()
    {
        $this->service->dropIndex();
        $this->service->dropIndex();
        $this->assertTrue(true);
    }
}
