<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
class DBSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dbhost = env('DB_HOST');
        $dbport = env('DB_PORT');
        $dbname = env('DB_DATABASE');
        $dbuser = env('DB_USERNAME');
        $dbpass = env('DB_PASSWORD');
        try {
            $db = new \PDO("pgsql:host=$dbhost;port=$dbport;sslmode=prefer", $dbuser, $dbpass);
            $test = $db->exec("CREATE DATABASE \"$dbname\";");
            if($test === false)
                throw new \Exception($db->errorInfo()[2]);
            $this->info(sprintf('Successfully created %s database', $dbname));
        }
        catch (\Exception $exception) {
            $this->error(sprintf('Failed to create %s database: %s', $dbname, $exception->getMessage()));
        }
    }
}