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
        $this->add([
            'name' => 'name',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 5,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Title should be between %min% and %max% characters long.',
                            StringLength::TOO_LONG => 'Title should be between %min% and %max% characters long.',
                        ]
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'description',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 10,
                        'max' => 512,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Description should be between %min% and %max% characters long.',
                            StringLength::TOO_LONG => 'Description should be between %min% and %max% characters long.',
                        ]
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'link',
            'required' => false,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Link should be between %min% and %max% characters long.',
                            StringLength::TOO_LONG => 'Link should be between %min% and %max% characters long.',
                        ]
                    ]
                ],
                [
                    'name' => Uri::class
                ]
            ]
        ]);

        $this->add([
            'name' => 'date',
            'required' => true,
            'validators' => [
                [
                    'name' => Date::class,
                    'options' => [
                        'format' => 'Y-m-d\TH:i',
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'venue',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 10,
                        'max' => 512,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Venue should be between %min% and %max% characters long.',
                            StringLength::TOO_LONG => 'Venue should be between %min% and %max% characters long.',
                        ]
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'poster',
            'type' => FileInput::class,
            'required' => false,
            'validators' => [
                ['name' => UploadFile::class],
                ['name' => IsImage::class],
                [
                    'name' => Size::class,
                    'options' => [
                        'max' => '2MB'
                    ]
                ]
            ],
            'filters' => [
                [
                    'name' => RenameUpload::class,
                    'options' => [
                        'target' => realpath(__DIR__ . '/../../public/uploads') . '/event-poster.jpg',
                        'use_upload_name' => false,
                        'use_upload_extension' => false,
                        'randomize' => true,
                    ]
                ]
            ]
        ]);
    }
}
