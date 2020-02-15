<?php


    namespace TelegramClientManager\Managers;

    use msqg\QueryBuilder;
    use TelegramClientManager\Abstracts\SearchMethods\TelegramClientSearchMethod;
    use TelegramClientManager\Exceptions\DatabaseException;
    use TelegramClientManager\Exceptions\InvalidSearchMethod;
    use TelegramClientManager\Exceptions\TelegramClientNotFoundException;
    use TelegramClientManager\Objects\TelegramClient;
    use TelegramClientManager\Objects\TelegramClient\Chat;
    use TelegramClientManager\Objects\TelegramClient\User;
    use TelegramClientManager\Utilities\Hashing;
    use ZiProto\ZiProto;

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

        /**
         * Registers a new Telegram Client into the database
         *
         * @param Chat $chat
         * @param User $user
         * @return TelegramClient
         * @throws DatabaseException
         * @throws InvalidSearchMethod
         * @throws TelegramClientNotFoundException
         */
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

            $PublicID = $this->telegramClientManager->database->real_escape_string($PublicID);
            $Available = (int)true;
            $AccountID = 0;
            $User = ZiProto::encode($user->toArray());
            $User = $this->telegramClientManager->database->real_escape_string($User);
            $Chat = ZiProto::encode($chat->toArray());
            $Chat = $this->telegramClientManager->database->real_escape_string($Chat);
            $SessionData = new TelegramClient\SessionData();
            $SessionData = ZiProto::encode($SessionData->toArray());
            $SessionData = $this->telegramClientManager->database->real_escape_string($SessionData);
            $ChatID = $this->telegramClientManager->database->real_escape_string($chat->ID);
            $UserID = $this->telegramClientManager->database->real_escape_string($user->ID);
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

        /**
         * Gets an existing Telegram Client from the database
         *
         * @param string $search_method
         * @param string $value
         * @return TelegramClient
         * @throws DatabaseException
         * @throws InvalidSearchMethod
         * @throws TelegramClientNotFoundException
         */
        public function getClient(string $search_method, string $value): TelegramClient
        {
            switch($search_method)
            {
                case TelegramClientSearchMethod::byId:
                    $search_method = $this->telegramClientManager->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case TelegramClientSearchMethod::byPublicId:
                    $search_method = $this->telegramClientManager->database->real_escape_string($search_method);
                    $value = $this->telegramClientManager->database->real_escape_string($value);;
                    break;

                default:
                    throw new InvalidSearchMethod();
            }

            $Query = QueryBuilder::select('telegram_clients', [
                'id',
                'public_id',
                'available',
                'account_id',
                'user',
                'chat',
                'session_data',
                'chat_id',
                'user_id',
                'last_activity',
                'created'
            ], $search_method, $value);

            $QueryResults = $this->telegramClientManager->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->telegramClientManager->database->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new TelegramClientNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['user'] = ZiProto::decode($Row['user']);
                $Row['chat'] = ZiProto::decode($Row['chat']);
                $Row['session_data'] = ZiProto::decode($Row['session_data']);
                return TelegramClient::fromArray($Row);
            }
        }
    }