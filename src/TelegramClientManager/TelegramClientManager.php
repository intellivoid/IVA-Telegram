<?php


    namespace TelegramClientManager;

    use acm\acm;
    use Exception;
    use mysqli;

    $LocalDirectory = __DIR__ . DIRECTORY_SEPARATOR;

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