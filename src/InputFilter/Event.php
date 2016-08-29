<?php

namespace PhpSchool\Website\InputFilter;

use Zend\Filter\File\RenameUpload;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Date;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\File\UploadFile;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\Validator\StringLength;
use Zend\Validator\Uri;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Event extends InputFilter
{
    public function __construct()
    {
        $titleLengthValidator = new StringLength(['min' => 5, 'max' => 255]);
        $titleLengthValidator
            ->setMessage('Title should be between %min% and %max% characters long.', StringLength::TOO_SHORT)
            ->setMessage('Title should be between %min% and %max% characters long.', StringLength::TOO_LONG);

        $title = new Input('name');
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

        $linkValidator = new StringLength(['min' => 1, 'max' => 255]);
        $linkValidator
            ->setMessage('Link should be between %min% and %max% characters long.', StringLength::TOO_SHORT)
            ->setMessage('Link should be between %min% and %max% characters long.', StringLength::TOO_LONG);

        $link = new Input('link');
        $link->setRequired(false);
        $link->getValidatorChain()
            ->attach(new Uri)
            ->attach($linkValidator);

        $this->add($link);

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

        $poster
            ->getFilterChain()
            ->attach(new RenameUpload([
                                          'target'    => realpath(
                                                  __DIR__ . '/../../public/uploads'
                                              ) . '/event-poster.jpg',
                                          'use_upload_name' => false,
                                          'use_upload_extension' => false,
                                          'randomize' => true,
            ]));
        
        $this->add($poster);
    }
}
