<?php


    namespace TelegramClientManager;

    use acm\acm;
    use Exception;
    use msqg\QueryBuilder;
    use mysqli;
    use ppm\ppm;

    $LocalDirectory = __DIR__ . DIRECTORY_SEPARATOR;

    include_once($LocalDirectory . 'AutoConfig.php');

    /**
     * Class TelegramClientManager
     * @package TelegramClientManager
     */
    class TelegramClientManager
    {
        /**
         * @var Managers\TelegramClientManager
         */
        private $TelegramClientManager;

        /**
         * @var mixed
         */
        private $TelegramConfiguration;

        /**
         * @var mixed
         */
        private $DatabaseConfiguration;

        /**
         * @var mysqli
         */
        private $database;

        /**
         * @var acm
         */
        private $acm;

        /**
         * TelegramClientManager constructor.
         * @throws Exception
         */
        public function __construct()
        {
            $this->acm = new acm(__DIR__, 'Telegram Client Manager');
            $this->DatabaseConfiguration = $this->acm->getConfiguration('Database');
            $this->database = null;

            $this->TelegramClientManager = new Managers\TelegramClientManager($this);
        }

        /**
         * @return mixed
         */
        public function getTelegramConfiguration()
        {
            return $this->TelegramConfiguration;
        }

        /**
         * @return mixed
         */
        public function getDatabaseConfiguration()
        {
            return $this->DatabaseConfiguration;
        }

        /**
         * @return Managers\TelegramClientManager
         */
        public function getTelegramClientManager(): Managers\TelegramClientManager
        {
            return $this->TelegramClientManager;
        }

        /**
         * @return mysqli
         */
        public function getDatabase(): mysqli
        {
            if($this->database == null)
            {
                $this->connectDatabase();
            }

            return $this->database;
        }

        /**
         * Closes the current database connection
         */
        public function disconnectDatabase()
        {
            $this->database->close();
            $this->database = null;
        }

        /**
         * Creates a new database connection
         */
        public function connectDatabase()
        {
            if($this->database !== null)
            {
                $this->disconnectDatabase();
            }

            $this->database = new mysqli(
                $this->DatabaseConfiguration['Host'],
                $this->DatabaseConfiguration['Username'],
                $this->DatabaseConfiguration['Password'],
                $this->DatabaseConfiguration['Name'],
                $this->DatabaseConfiguration['Port']
            );
        }

        /**
         * Updates all old records
         */
        public function applyAccountIdPatch()
        {
            print("Loading query ..." . PHP_EOL);
            $Query = QueryBuilder::select("telegram_clients", ["id"], "account_id", 0);
            $QueryResults = $this->getDatabase()->query($Query);

            if($QueryResults == false)
            {
                print("Query Failed" . PHP_EOL);
                exit(0);
            }

            print("Query success, loading results ..." . PHP_EOL);
            $ResultsArray = [];

            while($Row = $QueryResults->fetch_assoc())
            {
                $ResultsArray[] = $Row;
            }

            print("Processing results" . PHP_EOL);
            foreach($ResultsArray as $item)
            {
                print("Updating row " . $item["id"] . " ... ");
                $Query = QueryBuilder::update("telegram_clients", ["account_id" => null], "id", $item["id"]);
                $QueryResults = $this->getDatabase()->query($Query);
                if($QueryResults == false)
                {
                    print("Failed" . PHP_EOL);
                }
                else
                {
                    print("Success" . PHP_EOL);
                }
            }
        }
    }