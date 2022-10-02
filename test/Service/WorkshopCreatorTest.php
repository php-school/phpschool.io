<?php

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
            'github-url' => 'https://github.com/php-school/php8-appreciate',
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
                'bin' => ['bin/php8appreciate'],
                'description' => '2020 PHP: A showcase and classroom for the cutting edge features of PHP 8'
            ]);

        $repo
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Workshop $workshop) {
                $this->assertEquals('php-school', $workshop->getGitHubOwner());
                $this->assertEquals('php8-appreciate', $workshop->getGitHubRepoName());
                $this->assertEquals('php8appreciate', $workshop->getCode());
                $this->assertEquals('PHP8 Appreciate', $workshop->getDisplayName());
                $this->assertEquals('2020 PHP: A showcase and classroom for the cutting edge features of PHP 8', $workshop->getDescription());
                $this->assertEquals('aydin@hotmail.co.uk', $workshop->getSubmitterEmail());
                $this->assertEquals('Aydin Hassan', $workshop->getSubmitterName());
                $this->assertEquals('https://twitter.com/aydin_h', $workshop->getSubmitterContact());

                return true;
            }));

        $creator = new WorkshopCreator($inputFilter, $repo);

        $creator->create([
            'github-url' => 'https://github.com/php-school/php8-appreciate',
            'workshop-name' => 'PHP8 Appreciate',
            'email' => 'aydin@hotmail.co.uk',
            'name' => 'Aydin Hassan',
            'contact' => 'https://twitter.com/aydin_h'
        ]);
    }

    private function getComposerJson(): array
    {
        return [
            'name' => 'php-school/php8-appreciate',
            'description' => '2020 PHP: A showcase and classroom for the cutting edge features of PHP 8',
            'type' => 'php-school-workshop',
            'keywords' => ["cli", "console", "terminal", "phpschool", "php-school", "workshop", "learning", "education"],
            'homepage' => 'https://www.phpschool.io',
            'license' => 'MIT',
            'authors' => [['name' => 'Aydin Hassan', 'email' => 'aydin@hotmail.co.uk']],
            'require' => ['php' => '^8.0', 'php-school/php-workshop' => '^4.0'],
            'require-dev' => [
                'phpunit/phpunit' => '^9',
                'squizlabs/php_codesniffer' => '^3.5',
                'phpstan/phpstan' => '^0.12.52',
                'timeweb/phpstan-enum' => '^2.2',
            ],
            'autoload' => ['psr-4' => ['PhpSchool\\PHP8Appreciate\\' => 'src/']],
            'autoload-dev' => ['psr-4' => ['PhpSchool\\PHP8AppreciateTest\\' => 'test']],
            'scripts' => [
                'test' => ['@unit-tests', '@cs', '@static'],
                'unit-tests' => 'phpunit',
                'static' => 'phpstan --ansi analyse --level max src',
                'cs' => ['phpcs src --standard=PSR12', "phpcs test --standard=PSR12 --ignore='test/solutions'"],
                'cs-fix' => ['phpcbf src --standard=PSR12 --encoding=UTF-8', "phpcbf test --standard=PSR12 --encoding=UTF-8 --ignore='test/solutions'"],
            ],
            'bin' => ['bin/php8appreciate'],
        ];
    }
}
