<?php


    namespace TelegramClientManager;

    use acm\acm;
    use Exception;
    use mysqli;

    $LocalDirectory = __DIR__ . DIRECTORY_SEPARATOR;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'TelegramChatType.php');

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
        }
    }