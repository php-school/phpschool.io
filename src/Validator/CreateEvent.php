<?php

namespace PhpSchool\Website\Validator;

use Github\Client;
use PhpSchool\Website\Repository\WorkshopRepository;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\Date;
use Zend\Validator\File\Extension;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\File\UploadFile;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\Validator\Callback;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class CreateEvent extends Validator
{
    public function __construct()
    {
        $titleLengthValidator = new StringLength(['min' => 5, 'max' => 255]);
        $titleLengthValidator
            ->setMessage('Title should be between %min% and %max% characters long.', StringLength::TOO_SHORT)
            ->setMessage('Title should be between %min% and %max% characters long.', StringLength::TOO_LONG);

        $title = new Input('title');
        $title->getValidatorChain()
            ->attach($titleLengthValidator);

        $this->add($title);

        $descriptionValidator = new StringLength(['min' => 10, 'max' => 512]);
        $descriptionValidator
            ->setMessage('Description should be between %min% and %max% characters long.', StringLength::TOO_SHORT)
            ->setMessage('Description should be between %min% and %max% characters long.', StringLength::TOO_LONG);

        $description = new Input('description');
        $description->getValidatorChain()
            ->attach($descriptionValidator);

        $this->add($description);

        $dateValidator = new Date();
        $dateValidator->setFormat('Y-m-d\TH:i');

        $date = new Input('date');
        $date->getValidatorChain()
            ->attach($dateValidator);

        $this->add($date);

        $venueValidator = new StringLength(['min' => 10, 'max' => 512]);
        $venueValidator
            ->setMessage('Venue should be between %min% and %max% characters long.', StringLength::TOO_SHORT)
            ->setMessage('Venue should be between %min% and %max% characters long.', StringLength::TOO_LONG);

        $venue = new Input('venue');
        $venue->getValidatorChain()
            ->attach($venueValidator);

        $this->add($venue);

        $poster = new FileInput('poster');
        $poster->setRequired(false);
        $poster
            ->getValidatorChain()
            ->attach(new UploadFile)
            ->attach(new IsImage)
            ->attach(new Size(['max' => '2MB']));

        if (!file_exists(__DIR__ . '/../../public/uploads')) {
            mkdir(__DIR__ . '/../../public/uploads');
        }
        $poster
            ->getFilterChain()
            ->attach(new RenameUpload([
                'target'    => realpath(__DIR__ . '/../../public/uploads') . '/event-poster.jpg',
                'use_upload_name' => false,
                'use_upload_extension' => false,
                'randomize' => true,
            ]));
        
        $this->add($poster);
    }
}
