<?php


    namespace TelegramClientManager;

    use acm\acm;
    use Exception;
    use mysqli;

    $LocalDirectory = __DIR__ . DIRECTORY_SEPARATOR;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'TelegramChatType.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'DatabaseException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidSearchMethod.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'TelegramClientManager.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR. 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient' . DIRECTORY_SEPARATOR . 'Chat.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR. 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient' . DIRECTORY_SEPARATOR . 'SessionData.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR. 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient' . DIRECTORY_SEPARATOR . 'User.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR. 'Objects' . DIRECTORY_SEPARATOR . 'TelegramClient.php');

    if(class_exists('acm\acm') == false)
    {
        include_once($LocalDirectory . 'acm' . DIRECTORY_SEPARATOR . 'acm.php');
    }

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
         * TelegramClientManager constructor.
         * @throws Exception
         */
        public function __construct()
        {
            $this->acm = new acm(__DIR__, 'Telegram Client Manager');
            $this->DatabaseConfiguration = $this->acm->getConfiguration('Database');
            $this->TelegramConfiguration = $this->acm->getConfiguration('TelegramService');

            $this->database = new mysqli(
                $this->DatabaseConfiguration['Host'],
                $this->DatabaseConfiguration['Username'],
                $this->DatabaseConfiguration['Password'],
                $this->DatabaseConfiguration['Name'],
                $this->DatabaseConfiguration['Port']
            );

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
    }