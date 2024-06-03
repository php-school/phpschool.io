<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Service;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\InputFilter\WorkshopComposerJson as WorkshopComposerJsonInputFilter;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\Service\WorkshopCreator;
use PHPUnit\Framework\TestCase;

class WorkshopCreatorTest extends TestCase
{
    public function testExceptionIsThrownIfComposerJsonFileIsInvalid(): void
    {
        $this->expectException(WorkshopCreationException::class);

        $repo = $this->createMock(WorkshopRepository::class);
        $inputFilter = $this->createMock(WorkshopComposerJsonInputFilter::class);

        $inputFilter->expects($this->once())
            ->method('setData')
            ->with($this->getComposerJson());

        $inputFilter->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $inputFilter->expects($this->once())
            ->method('getMessages')
            ->willReturn([]);

        $creator = new WorkshopCreator($inputFilter, $repo);

        $creator->create([
            'github-url' => 'https://github.com/php-school/test-workshop',
        ]);
    }

    public function testWorkshopIsSavedIfComposerJsonIsValid(): void
    {
        $repo = $this->createMock(WorkshopRepository::class);
        $inputFilter = $this->createMock(WorkshopComposerJsonInputFilter::class);

        $inputFilter->expects($this->once())
            ->method('setData')
            ->with($this->getComposerJson());

        $inputFilter->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $inputFilter->expects($this->once())
            ->method('getValues')
            ->willReturn([
                'bin' => ['bin/testworkshop'],
                'description' => 'Test Workshop'
            ]);

        $repo
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Workshop $workshop) {
                $this->assertEquals('php-school', $workshop->getGitHubOwner());
                $this->assertEquals('test-workshop', $workshop->getGitHubRepoName());
                $this->assertEquals('testworkshop', $workshop->getCode());
                $this->assertEquals('Test Workshop', $workshop->getDisplayName());
                $this->assertEquals('Test Workshop', $workshop->getDescription());
                $this->assertEquals('aydin@hotmail.co.uk', $workshop->getSubmitterEmail());
                $this->assertEquals('Aydin Hassan', $workshop->getSubmitterName());
                $this->assertEquals('https://twitter.com/aydin_h', $workshop->getSubmitterContact());

                return true;
            }));

        $creator = new WorkshopCreator($inputFilter, $repo);

        $creator->create([
            'github-url' => 'https://github.com/php-school/test-workshop',
            'workshop-name' => 'Test Workshop',
            'email' => 'aydin@hotmail.co.uk',
            'name' => 'Aydin Hassan',
            'contact' => 'https://twitter.com/aydin_h'
        ]);
    }

    private function getComposerJson(): array
    {
        return [
            'name' => 'php-school/test-workshop',
            'description' => 'Test Workshop',
            'type' => 'php-school-workshop',
            'keywords' => ["cli", "console", "terminal", "phpschool", "php-school", "workshop", "learning", "education"],
            'homepage' => 'https://www.phpschool.io',
            'license' => 'MIT',
            'authors' => [['name' => 'Aydin Hassan', 'email' => 'aydin@hotmail.co.uk']],
            'require' => ['php' => '>=7.3', 'php-school/php-workshop' => '^4.0.1'],
            'require-dev' => [
                'phpunit/phpunit' => '^9',
                'squizlabs/php_codesniffer' => '^3.7',
                'phpstan/phpstan' => '^1.8.11',
            ],
            'autoload' => ['psr-4' => ['PhpSchool\\TestWorkshop\\' => 'src/']],
            'autoload-dev' => ['psr-4' => ['PhpSchool\\TestWorkshopTest\\' => 'test']],
            'scripts' => [
                'test' => ['@unit-tests', '@cs', '@static'],
                'unit-tests' => 'phpunit',
                'static' => 'phpstan --ansi analyse --level max src',
                'cs' => ['phpcs src --standard=PSR12', "phpcs test --standard=PSR12"],
                'cs-fix' => ['phpcbf src --standard=PSR12 --encoding=UTF-8', "phpcbf test --standard=PSR12 --encoding=UTF-8"],
            ],
            'bin' => ['bin/testworkshop'],
        ];
    }
}
