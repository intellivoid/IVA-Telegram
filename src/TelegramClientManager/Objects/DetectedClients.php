<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace TelegramClientManager\Objects;

    use Exception;
    use Longman\TelegramBot\Entities\CallbackQuery;
    use Longman\TelegramBot\Entities\ChatMember\ChatMember;
    use Longman\TelegramBot\Entities\Message;
    use TelegramClientManager\Exceptions\DatabaseException;
    use TelegramClientManager\Exceptions\InvalidSearchMethod;
    use TelegramClientManager\Exceptions\TelegramClientNotFoundException;
    use TelegramClientManager\TelegramClientManager;

    class DetectedClients
    {
        /**
         * The chat/channel object of the current chat/channel
         *
         * @var TelegramClient\Chat|null
         */
        public $ChatObject = null;

        /**
         * The client of the chat/channel of the current chat/channel
         *
         * @var TelegramClient|null
         */
        public $ChatClient = null;

        /**
         * The user/bot object of the initializer (Entity that sent the message/action)
         *
         * @var TelegramClient\User|null
         */
        public $UserObject = null;

        /**
         * The user/bot client of the initializer (Entity that sent the message/action)
         *
         * @var TelegramClient|null
         */
        public $UserClient = null;

        /**
         * The direct client combination of the user initializer and the current chat/channel
         *
         * @var TelegramClient|null
         */
        public $DirectClient = null;

        /**
         * The original sender object of the forwarded content
         *
         * @var TelegramClient\User|null
         */
        public $ForwardUserObject = null;

        /**
         * The original sender client of the forwarded content
         *
         * @var TelegramClient|null
         */
        public $ForwardUserClient = null;

        /**
         * The channel origin object of the forwarded content
         *
         * @var TelegramClient\Chat|null
         */
        public $ForwardChannelObject = null;

        /**
         * The channel origin client of the forwarded content
         *
         * @var TelegramClient|null
         */
        public $ForwardChannelClient = null;

        /**
         * The target user object of the message that the reply is to
         *
         * @var TelegramClient\User|null
         */
        public $ReplyToUserObject = null;

        /**
         * The target user client of the message that the reply is to
         *
         * @var TelegramClient|null
         */
        public $ReplyToUserClient = null;

        /**
         * The original sender object of the forwarded content that this message is replying to
         *
         * @var TelegramClient\User|null
         */
        public $ReplyToUserForwardUserObject = null;

        /**
         * The original sender client of the forwarded content that this message is replying to
         *
         * @var TelegramClient|null
         */
        public $ReplyToUserForwardUserClient = null;

        /**
         * The original channel object origin of the forwarded content that this message is replying to
         *
         * @var TelegramClient\Chat|null
         */
        public $ReplyToUserForwardChannelObject = null;

        /**
         * The original channel cient origin of the forwarded content that this message is replying to
         *
         * @var TelegramClient|null
         */
        public $ReplyToUserForwardChannelClient = null;

        /**
         * Array of user mentions by UserID:ObjectType
         *
         * @var TelegramClient\User[]|null
         */
        public $MentionUserObjects = null;

        /**
         * Array of user mentions by UserID:ObjectClient
         *
         * @var TelegramClient[]|null
         */
        public $MentionUserClients = null;

        /**
         * Array of new chat members (objects) that has been added to the chat
         *
         * @var TelegramClient\User[]|null
         */
        public $NewChatMembersObjects = null;

        /**
         * Array of new chat members (clients) that has been added to the chat
         *
         * @var TelegramClient[]|null
         */
        public $NewChatMembersClients = null;

        /**
         * When enabled, the results will be sent privately and
         * the message will be deleted
         *
         * @var bool
         */
        public $PrivateMode = false;

        /**
         * The destination chat relative to the private mode
         *
         * @var TelegramClient\Chat|null
         */
        public $DestinationChat = null;

        /**
         * The message ID to reply to
         *
         * @var int|null
         */
        public $ReplyToID = null;

        /**
         * The chat of the callback query
         *
         * @var TelegramClient\Chat|null
         */
        public $CallbackQueryChatObject = null;

        /**
         * The chat client of the callback query
         *
         * @var TelegramClient|null
         */
        public $CallbackQueryChatClient = null;

        /**
         * The user of the callback query
         *
         * @var TelegramClient\User|null
         */
        public $CallbackQueryUserObject = null;

        /**
         * The user client of the callback query
         *
         * @var TelegramClient|null
         */
        public $CallbackQueryUserClient = null;

        /**
         * The client of the callback query
         *
         * @var TelegramClient|null
         */
        public $CallbackQueryClient = null;

        /**
         * The new sender chat object for users sending as a channel
         *
         * @var TelegramClient\Chat|null
         */
        public $SenderChatObject = null;

        /**
         * The new sender chat object for users sending as a channel
         *
         * @var TelegramClient|null
         */
        public $SenderChatClient = null;

        /**
         * The new sender chat object for users sending as a channel
         *
         * @var TelegramClient\Chat|null
         */
        public $ReplyToSenderChatObject = null;

        /**
         * The new sender chat object for users sending as a channel
         *
         * @var TelegramClient|null
         */
        public $ReplyToSenderChatClient = null;

        /**
         * The message used to detect all the clients available
         *
         * @var Message|null
         */
        public $Message = null;

        /**
         * @var CallbackQuery|null
         */
        public $CallbackQuery = null;

        /**
         * Attempts to find the target user that the reply/message is referring to
         *
         * @param bool $reply_only If enabled, the target user can refer to the user of that sent the message
         * @return TelegramClient|null
         */
        public function findTarget(bool $reply_only=true)
        {
            if($this->ReplyToUserClient !== null)
            {
                if($this->ReplyToSenderChatClient !== null)
                    return $this->ReplyToSenderChatClient;

                return $this->ReplyToUserClient;
            }

            if($this->MentionUserClients !== null)
            {
                if(count($this->MentionUserClients) > 0)
                    return $this->MentionUserClients[array_keys($this->MentionUserClients)[0]];
            }

            if($reply_only == false)
            {
                if($this->SenderChatClient !== null)
                    return $this->SenderChatClient;

                if($this->UserClient !== null)
                    return $this->UserClient;
            }

            return null;
        }

        /**
         * Finds the original target of a forwarded message
         *
         * @param bool $reply_only If enabled, the target user can refer to the user of that sent the message
         * @return TelegramClient|null
         */
        public function findForwardedTarget(bool $reply_only=true)
        {
            if($this->ReplyToSenderChatClient !== null)
            {
                return $this->SenderChatClient;
            }

            if($this->ReplyToUserForwardUserClient !== null)
            {
                return $this->ReplyToUserForwardUserClient;
            }

            if($this->ReplyToUserForwardChannelClient !== null)
            {
                return $this->ReplyToUserForwardChannelClient;
            }

            if($reply_only == false)
            {
                if($this->ForwardUserClient !== null)
                {
                    return $this->ForwardUserClient;
                }

                if($this->ForwardChannelClient !== null)
                {
                    return $this->ForwardChannelClient;
                }
            }

            return null;
        }


        /**
         * Attempts to find all possible clients
         *
         * @param TelegramClientManager $manager
         * @param Message $message
         * @param CallbackQuery|null $callbackQuery
         * @return DetectedClients
         * @throws DatabaseException
         * @throws InvalidSearchMethod
         * @throws TelegramClientNotFoundException
         */
        public static function findClients(TelegramClientManager $manager, Message $message, ?CallbackQuery $callbackQuery=null): DetectedClients
        {
            $DetectedClientsObject = new DetectedClients();
            $DetectedClientsObject->Message = $message;
            $DetectedClientsObject->CallbackQuery = $callbackQuery;

            /**
             * Detect and register the sender and chat
             */
            if($message->getChat() !== null)
            {
                $DetectedClientsObject->ChatObject = TelegramClient\Chat::fromArray($message->getChat()->getRawData());
                $DetectedClientsObject->ChatClient = $manager->getTelegramClientManager()->registerChat($DetectedClientsObject->ChatObject);
            }

            if($message->getFrom() !== null)
            {
                $DetectedClientsObject->UserObject = TelegramClient\User::fromArray($message->getFrom()->getRawData());
                $DetectedClientsObject->UserClient = $manager->getTelegramClientManager()->registerUser($DetectedClientsObject->UserObject);
            }

            if($DetectedClientsObject->ChatObject !== null && $DetectedClientsObject->UserObject !== null)
            {
                $DetectedClientsObject->DirectClient = $manager->getTelegramClientManager()->registerClient(
                    $DetectedClientsObject->ChatObject, $DetectedClientsObject->UserObject
                );
            }

            /**
             * CallbackQuery parser and detection
             */
            if($callbackQuery !== null)
            {
                if($callbackQuery->getFrom() !== null)
                {
                    $DetectedClientsObject->CallbackQueryUserObject = TelegramClient\User::fromArray($callbackQuery->getFrom()->getRawData());
                    $DetectedClientsObject->CallbackQueryUserClient = $manager->getTelegramClientManager()->registerUser($DetectedClientsObject->CallbackQueryUserObject);
                }

                if($callbackQuery->getMessage() !== null)
                {
                    if($callbackQuery->getMessage()->getChat() !== null)
                    {
                        $DetectedClientsObject->CallbackQueryChatObject = TelegramClient\Chat::fromArray($callbackQuery->getMessage()->getChat()->getRawData());
                        $DetectedClientsObject->CallbackQueryChatClient = $manager->getTelegramClientManager()->registerChat($DetectedClientsObject->CallbackQueryChatObject);
                    }
                }

                if($DetectedClientsObject->CallbackQueryUserObject !== null && $DetectedClientsObject->CallbackQueryChatObject !== null)
                {
                    $DetectedClientsObject->CallbackQueryClient = $manager->getTelegramClientManager()->registerClient(
                        $DetectedClientsObject->CallbackQueryChatObject, $DetectedClientsObject->CallbackQueryUserObject
                    );
                }
            }

            /**
             * Detect and define the forwarder if available
             */
            if($message->getForwardFrom() !== null)
            {
                $DetectedClientsObject->ForwardUserObject = TelegramClient\User::fromArray($message->getForwardFrom()->getRawData());
                $DetectedClientsObject->ForwardUserClient = $manager->getTelegramClientManager()->registerUser($DetectedClientsObject->ForwardUserObject);
            }

            /**
             * Detect and define the forwarded from chat/channel if available
             */
            if($message->getForwardFromChat() !== null)
            {
                $DetectedClientsObject->ForwardChannelObject = TelegramClient\Chat::fromArray($message->getForwardFromChat()->getRawData());
                $DetectedClientsObject->ForwardChannelClient = $manager->getTelegramClientManager()->registerChat($DetectedClientsObject->ForwardChannelObject);
            }

            /**
             * Detect and define the user sending a channel
             */
            if($message->getSenderChat() !== null)
            {
                $DetectedClientsObject->SenderChatObject = TelegramClient\Chat::fromArray($message->getSenderChat()->getRawData());
                $DetectedClientsObject->SenderChatClient = $manager->getTelegramClientManager()->registerChat($DetectedClientsObject->SenderChatObject);
            }

            /**
             * Detect and define the reply to message entities
             */
            if($message->getReplyToMessage() !== null)
            {
                if($message->getReplyToMessage()->getFrom() !== null)
                {
                    $DetectedClientsObject->ReplyToUserObject = TelegramClient\User::fromArray($message->getReplyToMessage()->getFrom()->getRawData());
                    $DetectedClientsObject->ReplyToUserClient = $manager->getTelegramClientManager()->registerUser($DetectedClientsObject->ReplyToUserObject);
                }

                if($message->getReplyToMessage()->getSenderChat() !== null)
                {
                    $DetectedClientsObject->ReplyToSenderChatObject = TelegramClient\Chat::fromArray($message->getReplyToMessage()->getSenderChat()->getRawData());
                    $DetectedClientsObject->ReplyToSenderChatClient = $manager->getTelegramClientManager()->registerChat($DetectedClientsObject->ReplyToSenderChatObject);
                }

                if($message->getReplyToMessage()->getForwardFrom() !== null)
                {
                    $DetectedClientsObject->ReplyToUserForwardChannelObject = TelegramClient\User::fromArray($message->getReplyToMessage()->getForwardFrom()->getRawData());
                    $DetectedClientsObject->ReplyToUserForwardChannelClient = $manager->getTelegramClientManager()->registerUser($DetectedClientsObject->ReplyToUserForwardChannelObject);
                }

                if($message->getReplyToMessage()->getForwardFromChat() !== null)
                {
                    $DetectedClientsObject->ReplyToUserForwardChannelObject = TelegramClient\Chat::fromArray($message->getReplyToMessage()->getForwardFromChat()->getRawData());
                    $DetectedClientsObject->ReplyToUserForwardChannelClient = $manager->getTelegramClientManager()->registerChat($DetectedClientsObject->ReplyToUserForwardChannelObject);
                }
            }

            /**
             * Detect and define the mentioned users
             */
            $DetectedClientsObject->MentionUserObjects = [];
            $DetectedClientsObject->MentionUserClients = [];

            // The message in general
            if($message->getEntities() !== null)
            {
                foreach($message->getEntities() as $messageEntity)
                {
                    /** @noinspection DuplicatedCode */
                    if($messageEntity->getUser() !== null)
                    {
                        $MentionUserObject = TelegramClient\User::fromArray($messageEntity->getUser()->getRawData());
                        $MentionUserClient = $manager->getTelegramClientManager()->registerUser($MentionUserObject);
                        $DetectedClientsObject->MentionUserObjects[$MentionUserObject->ID] = $MentionUserObject;
                        $DetectedClientsObject->MentionUserClients[$MentionUserObject->ID] = $MentionUserClient;
                    }
                }
            }

            // If the reply contains mentions
            if($message->getReplyToMessage() !== null && $message->getReplyToMessage()->getEntities() !== null)
            {
                foreach($message->getReplyToMessage()->getEntities() as $messageEntity)
                {
                    /** @noinspection DuplicatedCode */
                    if($messageEntity->getUser() !== null)
                    {
                        $MentionUserObject = TelegramClient\User::fromArray($messageEntity->getUser()->getRawData());
                        $MentionUserClient = $manager->getTelegramClientManager()->registerUser($MentionUserObject);
                        $DetectedClientsObject->MentionUserObjects[$MentionUserObject->ID] = $MentionUserObject;
                        $DetectedClientsObject->MentionUserClients[$MentionUserObject->ID] = $MentionUserClient;
                    }
                }
            }

            /**
             * detect new chat members
             */
            $DetectedClientsObject->NewChatMembersObjects = [];
            $DetectedClientsObject->NewChatMembersClients = [];

            // The message in general
            if($message->getNewChatMembers() !== null)
            {
                /** @var ChatMember $chatMember */
                foreach($message->getNewChatMembers() as $chatMember)
                {
                    /** @noinspection DuplicatedCode */
                    if($chatMember->getUser() !== null)
                    {
                        $NewUserObject = TelegramClient\User::fromArray($chatMember->getUser()->getRawData());
                        /** @noinspection DuplicatedCode */
                        $NewUserClient = $manager->getTelegramClientManager()->registerUser($NewUserObject);
                        $DetectedClientsObject->NewChatMembersObjects[$NewUserObject->ID] = $NewUserObject;
                        $DetectedClientsObject->NewChatMembersClients[$NewUserObject->ID] = $NewUserClient;
                    }
                }
            }

            /**
             * Return the final results
             */
            return $DetectedClientsObject;
        }
    }