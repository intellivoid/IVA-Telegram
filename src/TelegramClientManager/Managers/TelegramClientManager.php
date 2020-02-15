<?php


    namespace TelegramClientManager\Managers;

    use TelegramClientManager\Abstracts\SearchMethods\TelegramClientSearchMethod;
    use TelegramClientManager\Exceptions\DatabaseException;

    /**
     * Class TelegramClientManager
     * @package TelegramClientManager\Managers
     */
    class TelegramClientManager
    {
        /**
         * @var TelegramClientManager
         */
        private $telegramClientManager;

        /**
         * TelegramClientManager constructor.
         * @param \TelegramClientManager\TelegramClientManager $telegramClientManager
         */
        public function __construct(\TelegramClientManager\TelegramClientManager $telegramClientManager)
        {
            $this->telegramClientManager = $telegramClientManager;
        }

        public function registerClient(Chat $chat, User $user): TelegramClient
        {
            $CurrentTime = (int)time();
            $PublicID = Hashing::telegramClientPublicID($chat->ID, $user->ID);

            try
            {
                $ExistingClient = $this->getClient(TelegramClientSearchMethod::byPublicId, $PublicID);

                $ExistingClient->LastActivityTimestamp = $CurrentTime;
                $ExistingClient->Available = true;
                $ExistingClient->User = $user;
                $ExistingClient->Chat = $chat;

                $this->updateClient($ExistingClient);

                return $ExistingClient;
            }
            catch (TelegramClientNotFoundException $e)
            {
                // Ignore this exception
                unset($e);
            }

            $PublicID = $this->intellivoidAccounts->database->real_escape_string($PublicID);
            $Available = (int)true;
            $AccountID = 0;
            $User = ZiProto::encode($user->toArray());
            $User = $this->intellivoidAccounts->database->real_escape_string($User);
            $Chat = ZiProto::encode($chat->toArray());
            $Chat = $this->intellivoidAccounts->database->real_escape_string($Chat);
            $SessionData = new TelegramClient\SessionData();
            $SessionData = ZiProto::encode($SessionData->toArray());
            $SessionData = $this->intellivoidAccounts->database->real_escape_string($SessionData);
            $ChatID = $this->intellivoidAccounts->database->real_escape_string($chat->ID);
            $UserID = $this->intellivoidAccounts->database->real_escape_string($user->ID);
            $LastActivity = $CurrentTime;
            $Created = $CurrentTime;

            $Query = QueryBuilder::insert_into('telegram_clients', array(
                    'public_id' => $PublicID,
                    'available' => $Available,
                    'account_id' => $AccountID,
                    'user' => $User,
                    'chat' => $Chat,
                    'session_data' => $SessionData,
                    'chat_id' => $ChatID,
                    'user_id' => $UserID,
                    'last_activity' => $LastActivity,
                    'created' => $Created
                )
            );

            $QueryResults = $this->telegramClientManager->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->telegramClientManager->database->error);
            }

            return $this->getClient(TelegramClientSearchMethod::byPublicId, $PublicID);
        }
    }