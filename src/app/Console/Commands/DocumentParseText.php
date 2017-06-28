<?php

namespace App\Console\Commands;

use App\Http\Controllers\Document\Parse;
use Illuminate\Console\Command;

class DocumentParseText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:parsetext {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var Parse
     */
    private $parse;

    /**
     * Create a new command instance.
     *
     * @param Parse $parse
     */
    public function __construct(Parse $parse)
    {
        parent::__construct();

        $this->parse = $parse;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->parse->text($this->argument('id'));
    }
}
