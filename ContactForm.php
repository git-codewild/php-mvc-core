<?php

namespace codewild\phpmvc;

use codewild\phpmvc\Application;
use codewild\phpmvc\db\DbModel;


class ContactForm extends DbModel {
    public static function tableName(): string
    {
        return 'contacts';
    }

    public int $priority = 0;
    public string $subject = '';
    public string $email = '';
    public string $body = '';
    public ?string $sent_at = null;
    public ?string $sent_by = null;

    public static function attributes(): array{
        return ['priority', 'subject', 'email', 'body', 'sent_at', 'sent_by'];
    }

    public function rules(): array {
        return [
            'subject' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 64]],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_MAX, 'max' => 64]],
            'body' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 500]],
        ];
    }
    public function labels(): array {
        return [
            'priority' => 'Priority',
            'subject' => 'Subject',
            'email' => 'Email',
            'body' => 'Message',
            'sent_at' => 'Sent At',
            'sent_by' => 'Sent By'
        ];
    }

    public function save(){
        if (!Application::isGuest()){
            $this->sent_by = Application::$app->user->id;
        }

        return parent::save();
    }
}


?>
