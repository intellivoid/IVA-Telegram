<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace TelegramClientManager;

    use acm2\acm2;
    use acm2\Objects\Schema;
    use Exception;
    use mysqli;

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
        private $DatabaseConfiguration;

        /**
         * @var mysqli
         */
        private $database;

        /**
         * @var acm2
         */
        private $acm;

        /**
         * TelegramClientManager constructor.
         * @throws Exception
         */
        public function __construct()
        {
            $this->acm = new acm2('Telegram Client Manager');

            // Database Schema Configuration
            $DatabaseSchema = new Schema();
            $DatabaseSchema->setName('Database');
            $DatabaseSchema->setDefinition('Host', 'localhost');
            $DatabaseSchema->setDefinition('Port', '3306');
            $DatabaseSchema->setDefinition('Username', 'root');
            $DatabaseSchema->setDefinition('Password', '');
            $DatabaseSchema->setDefinition('Name', 'intellivoid');
            $this->acm->defineSchema($DatabaseSchema);

            // Update the configuration
            $this->acm->updateConfiguration();

            $this->DatabaseConfiguration = $this->acm->getConfiguration('Database');
            $this->database = null;

            $this->TelegramClientManager = new Managers\TelegramClientManager($this);
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
    }