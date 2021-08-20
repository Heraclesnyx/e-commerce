<?php


//Gestion des emails

namespace App\Classe;


use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    //Dans notre compte mailJet
    private $api_key = '672f8085440bd6d954014c04a30a9236';
    private $api_key_secret = '07f0f2d5bf6b47aaaeba2a21b229803b';

    public function send($to_email, $to_name, $subject, $content)
    {
        //Dans la doc MailJet-> Use un template
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "heraclesnyx@gmail.com",
                        'Name' => "E-commerce"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3115635,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]); //Ressource importer class
        $response->success();
    }
}