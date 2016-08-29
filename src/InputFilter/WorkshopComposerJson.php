<?php

namespace PhpSchool\Website\InputFilter;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Callback;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class WorkshopComposerJson extends InputFilter
{
    public function __construct()
    {
//        $this->add([
//            'name' => 'type',
//            'required' => true,
//            'validators' => [
//                [
//                    'name' => NotEmpty::class,
//                    'options' => [
//                        'messages' => [
//                            NotEmpty::IS_EMPTY => 'The "type" key in "composer.json" should be "php-school-workshop"',
//                        ],
//                    ],
//                ],
//                [
//                    'name' => Identical::class,
//                    'options' => [
//                        'messages' => [
//                            Identical::NOT_SAME => 'The "type" key in "composer.json" should be "php-school-workshop"',
//                        ],
//                    ],
//                ]
//            ]
//        ]);
//
//        $this->add([
//           'name' => 'bin',
//           'required' => true,
//           'validators' => [
//               [
//                   'name' => NotEmpty::class,
//                   'options' => [
//                       'messages' => [
//                           NotEmpty::IS_EMPTY => 'The "bin" key in "composer.json" should be an array with one entry which points to the entry point of the workshop. For example: {"bin" : ["bin/learnyouphp"]}',
//                       ]
//                   ]
//               ],
//               [
//                   'name' => Callback::class,
//                   'options' => [
//                       'callback' => function ($bin) {
//                           return is_array($bin) && count($bin) === 1;
//                       },
//                       'messages' => [
//                           Callback::INVALID_VALUE => 'The "bin" key in "composer.json" should be an array with one entry which points to the entry point of the workshop. For example: {"bin" : ["bin/learnyouphp"]}',
//                       ]
//                   ]
//               ]
//           ]
//        ]);
//
//        $this->add([
//            'name' => 'description',
//            'required' => true,
//            'validators' => [
//                [
//                    'name' => NotEmpty::class,
//                    'options' => [
//                        'messages' => [
//                            NotEmpty::IS_EMPTY => 'The "description" key in "composer.json" should be set with a small description of the workshop',
//                        ],
//                    ],
//                ],
//                [
//                    'name' => StringLength::class,
//                    'options' => [
//                        'min' => 10,
//                        'max' => 512,
//                        'messages' => [
//                            StringLength::TOO_SHORT => 'The "description" key should be between %min% and %max% characters long.',
//                            StringLength::TOO_LONG => 'The "description" key should be between %min% and %max% characters long.',
//                        ],
//                    ],
//                ]
//            ]
//        ]);

        //type
        $notEmpty = new NotEmpty;
        $notEmpty
            ->setMessage('The "type" key in "composer.json" should be "php-school-workshop"', NotEmpty::IS_EMPTY);

        $typeValidator = new Identical('php-school-workshop');
        $typeValidator
            ->setMessage('The "type" key in "composer.json" should be "php-school-workshop"', Identical::NOT_SAME);

        $type = new Input('type');
        $type->getValidatorChain()
            ->attach($notEmpty)
            ->attach($typeValidator);

        //bin
        $notEmpty = new NotEmpty;
        $notEmpty
            ->setMessage('The "bin" key in "composer.json" should be an array with one entry which points to the entry point of the workshop. For example: {"bin" : ["bin/learnyouphp"]}', NotEmpty::IS_EMPTY);

        $binValidator = new Callback(function ($bin) {
            return is_array($bin) && count($bin) === 1;
        });

        $binValidator->setMessage(
            'The "bin" key in "composer.json" should be an array with one entry which points to the entry point of the workshop. For example: {"bin" : ["bin/learnyouphp"]}',
            Callback::INVALID_VALUE
        );

        $bin = new Input('bin');
        $bin->getValidatorChain()
            ->attach($notEmpty)
            ->attach($binValidator);

        //description
        $notEmpty = new NotEmpty;
        $notEmpty
            ->setMessage('The "description" key in "composer.json" should be set with a small description of the workshop', NotEmpty::IS_EMPTY);

        $descriptionValidator = new StringLength(['min' => 10, 'max' => 512]);
        $descriptionValidator
            ->setMessage('The "description" key should be between %min% and %max% characters long.', StringLength::TOO_SHORT)
            ->setMessage('The "description" key should be between %min% and %max% characters long.', StringLength::TOO_LONG);

        $description = new Input('description');
        $description->getValidatorChain()
            ->attach($notEmpty)
            ->attach($descriptionValidator);

        $this->add($type);
        $this->add($bin);
        $this->add($description);
    }
}
