<?php

namespace PhpSchool\Website\Workshop;

use PhpSchool\Website\Entity\Workshop;
use RuntimeException;
use SendGrid;
use SendGrid\Mail;
use SendGrid\Personalization;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class EmailNotifier
{
    /**
     * @var SendGrid
     */
    private $sendGrid;

    /**
     * @var string
     */
    private $phpSchoolEmail;

    /**
     * @var array
     */
    private $templates = [
        'Workshop Submitted' => 'c3c5b0c3-5059-4025-a986-7a2c763e80e8',
        'Workshop Approved' => '3f8709bd-d30d-4214-a6cd-8b92b74a6f21',
    ];


    public function __construct(SendGrid $sendGrid, string $phpSchoolEmail)
    {
        $this->sendGrid = $sendGrid;
        $this->phpSchoolEmail = $phpSchoolEmail;
    }

    public function approved(Workshop $workshop)
    {
        $mail = $this->getMail($workshop, $workshop->getSubmitterEmail(), $this->templates['Workshop Approved']);

        $this->sendMail($mail);
    }

    public function new(Workshop $workshop)
    {
        $mail = $this->getMail($workshop, $this->phpSchoolEmail, $this->templates['Workshop Submitted']);

        $this->sendMail($mail);
    }

    private function sendMail(Mail $mail)
    {
        $response = $this->sendGrid->client->mail()->send()->post($mail);

        if ($response->statusCode() < 200 || $response->statusCode() > 300) {
            throw new RuntimeException(json_encode($response->headers()));
        }
    }

    private function getMail(Workshop $workshop, string $to, string $template) : Mail
    {
        $from = new SendGrid\Email('PHP School', $this->phpSchoolEmail);

        $personalisation = new Personalization;
        $personalisation->addSubstitution('%workshop%', $workshop->getDisplayName());
        $personalisation->addTo(new SendGrid\Email(null, $to));

        $mail = new Mail;
        $mail->setTemplateId($template);
        $mail->addPersonalization($personalisation);
        $mail->setFrom($from);

        return $mail;
    }
}
