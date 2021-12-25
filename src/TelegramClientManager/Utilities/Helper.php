<?php

    namespace TelegramClientManager\Utilities;

    use TelegramClientManager\Abstracts\TelegramChatType;
    use TelegramClientManager\Objects\TelegramClient;

    class Helper
    {
        /**
         * Generates a HTML mention
         *
         * @param TelegramClient $client
         * @return string
         */
        public static function generateMention(TelegramClient $client)
        {
            switch($client->Chat->Type)
            {
                case TelegramChatType::Private:
                    /** @noinspection DuplicatedCode */
                    if($client->User->Username == null)
                    {
                        if($client->User->LastName == null)
                        {
                            return "<a href=\"tg://user?id=" . $client->User->ID . "\">" . self::escapeHTML($client->User->FirstName) . "</a>";
                        }
                        else
                        {
                            return "<a href=\"tg://user?id=" . $client->User->ID . "\">" . self::escapeHTML($client->User->FirstName . " " . $client->User->LastName) . "</a>";
                        }
                    }
                    else
                    {
                        return "@" . $client->User->Username;
                    }
                    break;

                case TelegramChatType::SuperGroup:
                case TelegramChatType::Group:
                case TelegramChatType::Channel:
                    /** @noinspection DuplicatedCode */
                    if($client->Chat->Username == null)
                    {
                        if($client->Chat->Title !== null)
                        {
                            return "<a href=\"tg://user?id=" . $client->User->ID . "\">" . self::escapeHTML($client->Chat->Title) . "</a>";
                        }
                    }
                    else
                    {
                        return "@" . $client->Chat->Username;
                    }

                    break;

                default:
                    return "<a href=\"tg://user?id=" . $client->Chat->ID . "\">Unknown</a>";
            }

            return "Unknown";
        }
    }