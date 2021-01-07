<?php


    namespace TelegramClientManager;

    use acm\acm;
    use Exception;
    use mysqli;
    use ppm\ppm;

    $LocalDirectory = __DIR__ . DIRECTORY_SEPARATOR;

    if(defined("PPM") == false)
    {
        include_once($LocalDirectory . 'Abstracts' . DIRECTORY_SEPARATOR . 'SearchMethods' . DIRECTORY_SEPARATOR . 'TelegramClientSearchMethod.php');
        include_once($LocalDirectory . 'Abstracts' . DIRECTORY_SEPARATOR . 'TelegramChatType.php');

        include_once($LocalDirectory . 'Exceptions' . DIRECTORY_SEPARATOR . 'DatabaseException.php');
        include_once($LocalDirectory . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidSearchMethod.php');
        include_once($LocalDirectory . 'Exceptions' . DIRECTORY_SEPARATOR . 'TelegramClientNotFoundException.php');

        include_once($LocalDirectory . 'Managers' . DIRECTORY_SEPARATOR . 'TelegramClientManager.php');

        include_once($LocalDirectory . 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient' . DIRECTORY_SEPARATOR . 'Chat.php');
        include_once($LocalDirectory . 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient' . DIRECTORY_SEPARATOR . 'SessionData.php');
        include_once($LocalDirectory . 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient' . DIRECTORY_SEPARATOR . 'User.php');
        include_once($LocalDirectory . 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient.php');

        include_once($LocalDirectory . 'Utilities' . DIRECTORY_SEPARATOR . 'Hashing.php');

        if(class_exists('ZiProto\ZiProto') == false)
        {
            /** @noinspection PhpIncludeInspection */
            include_once($LocalDirectory . 'ZiProto' . DIRECTORY_SEPARATOR . 'ZiProto.php');
        }

        if(class_exists('msqg\msqg') == false)
        {
            /** @noinspection PhpIncludeInspection */
            include_once($LocalDirectory . 'msqg' . DIRECTORY_SEPARATOR . 'msqg.php');
        }

        if(class_exists('acm\acm') == false)
        {
            /** @noinspection PhpIncludeInspection */
            include_once($LocalDirectory . 'acm' . DIRECTORY_SEPARATOR . 'acm.php');
        }
    }
    else
    {
        ppm::import("net.intellivoid.ziproto");
        ppm::import("net.intellivoid.msqg");
        ppm::import("net.intellivoid.acm");
    }

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
    }