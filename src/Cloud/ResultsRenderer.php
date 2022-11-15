<?php

namespace PhpSchool\Website\Cloud;

use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\Result\Cgi\CgiResult;
use PhpSchool\PhpWorkshop\Result\Cli\CliResult;
use PhpSchool\PhpWorkshop\Result\Cli\FailureInterface;
use PhpSchool\PhpWorkshop\Result\ComparisonFailure;
use PhpSchool\PhpWorkshop\Result\Failure;
use PhpSchool\PhpWorkshop\Result\FileComparisonFailure;
use PhpSchool\PhpWorkshop\Result\FunctionRequirementsFailure;
use PhpSchool\PhpWorkshop\Result\ResultGroupInterface;
use PhpSchool\PhpWorkshop\Result\ResultInterface;
use PhpSchool\PhpWorkshop\Result\SuccessInterface;
use PhpSchool\PhpWorkshop\ResultAggregator;
use PhpSchool\PhpWorkshop\UserState;
use PhpSchool\PhpWorkshop\Result\Cgi\GenericFailure as CgiGenericFailure;
use PhpSchool\PhpWorkshop\Result\Cli\GenericFailure as CliGenericFailure;
use PhpSchool\PhpWorkshop\Result\Cgi\RequestFailure as CgiRequestFailure;
use PhpSchool\PhpWorkshop\Result\Cli\RequestFailure as CliRequestFailure;

class ResultsRenderer
{
    public function render(
        ResultAggregator $results,
        ExerciseInterface $exercise,
        UserState $userState,
    ): string {

        $successes  = [];
        $failures   = [];
        foreach ($results as $result) {
            if (
                $result instanceof SuccessInterface
                || ($result instanceof ResultGroupInterface && $result->isSuccessful())
            ) {
                $successes[] = $result;
            } else {
                $failures[] = $result;
            }
        }

        $parts = [];
        foreach ($successes as $success) {
            $parts[] = <<<EOT
                <li class="flex items-start space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-green-500">
                      <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" />
                    </svg>
                    <div class="flex flex-col">
                        <p>{$success->getCheckName()}</p>
                    </div>
                </li>
            EOT;
        }

        foreach ($failures as $failure) {
            $parts[] = $this->renderResult($failure);
        }

        return implode("\n", $parts);
    }

    /**
     * Render a result. Attempt to find the correct renderer via the result renderer factory.
     *
     * @param ResultInterface $result The result.
     * @return string The string representation of the result.
     */
    public function renderResult(ResultInterface $result): string
    {
        if ($result instanceof Failure) {
            return $this->renderFailure($result);
        }

        return match (get_class($result)) {
            FunctionRequirementsFailure::class => $this->renderFunctionRequirementFailure($result),

            CgiResult::class => $this->renderCgiResult($result),
            CgiRequestFailure::class => $this->renderCgiRequestFailure($result),

            CliResult::class => $this->renderCliResult($result),
            CliRequestFailure::class => $this->renderCliRequestFailure($result),

            ComparisonFailure::class => $this->renderComparisonFailure($result),
            FileComparisonFailure::class => $this->renderFileComparisonFailure($result)
        };
    }

    private function renderFailure(\PhpSchool\PhpWorkshop\Result\Failure $result): string
    {
        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    <p>{$result->getCheckName()}</p>
                    <p class="text-xs text-gray-300 mt-1">{$result->getReason()}</p>
                </div>
            </li>
        EOT;
    }

    private function renderFunctionRequirementFailure(FunctionRequirementsFailure $result): string
    {
        $banned = '';
        if (count($bannedFunctions = $result->getBannedFunctions())) {
            $functions = implode("\n", array_map(function (array $call) {
                return sprintf('<li>%s on line %s</li>', $call['function'], $call['line']);
            }, $bannedFunctions));

            $banned .= <<<EOT
                <p>Some functions were used which should not be used in this exercise</p>
                <ul>
                    {$functions}
                </ul>
            EOT;
        }

        $missing = '';
        if (count($missingFunctions = $result->getMissingFunctions())) {
            $functions = implode("\n", array_map(function (string $function) {
                return sprintf('<li>%s</li>', $function);
            }, $missingFunctions));

            $missing .= <<<EOT
                <p>Some function requirements were missing. You should use the functions</p>
                <ul>
                    {$functions}
                </ul>
            EOT;
        }

        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    {$banned}
                    {$missing}
                </div>
            </li>
        EOT;
    }

    private function renderComparisonFailure(ComparisonFailure $result): string
    {
        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    <p>{$result->getCheckName()}</p>
                    <p>Your output:</p>
                    <p class="text-xs text-gray-300 mt-1 border-l-1 border-red-500">{$result->getActualValue()}</p>
                    <p>Expected output:</p>
                    <p class="text-xs text-gray-300 mt-1 border-l-1 border-green-500">{$result->getExpectedValue()}</p>
                </div>
            </li>
        EOT;
    }

    private function renderFileComparisonFailure(FileComparisonFailure $result): string
    {
        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    <p>{$result->getCheckName()}</p>
                    <p>Your output for: {$result->getFileName()}</p>
                    <p class="text-xs text-gray-300 mt-1 border-l-1 border-red-500">{$result->getActualValue()}</p>
                    <p>Expected output for: {$result->getFileName()}</p>
                    <p class="text-xs text-gray-300 mt-1 border-l-1 border-green-500">{$result->getExpectedValue()}</p>
                </div>
            </li>
        EOT;
    }

    private function renderCliResult(CliResult $result): string
    {
        /** @var array<int, \PhpSchool\PhpWorkshop\Result\Cli\ResultInterface> $results */
        $results = array_filter($result->getResults(), function (\PhpSchool\PhpWorkshop\Result\Cli\ResultInterface $result) {
            return $result instanceof FailureInterface;
        });

        if (!count($results)) {
            return '';
        }

        $output = '';
        foreach ($results as $key => $request) {
            $execution = $key + 1;
            $output .= "<p>Execution number <span class='font-bold'>{$execution}</span> failed</p>";
            $output .= $request->getArgs()->isEmpty()
                ? "<p>Arguments: None</p>"
                : sprintf("<p>Arguments: \"%s\"</p>", $request->getArgs()->implode('", "'));

            $output .= $this->renderResult($request);
        }

        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    {$output}
                </div>
            </li>
        EOT;
    }

    private function renderCliRequestFailure(CliRequestFailure $result): string
    {
        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    <p>{$result->getCheckName()}</p>
                    <p>Your output:</p>
                    <p class="text-xs text-gray-300 mt-1 border-l-1 border-red-500">{$result->getActualOutput()}</p>
                    <p>Expected output:</p>
                    <p class="text-xs text-gray-300 mt-1 border-l-1 border-green-500">{$result->getExpectedOutput()}</p>
                </div>
            </li>
        EOT;
    }

    private function renderCgiResult(CgiResult $result): string
    {
        /** @var array<int, \PhpSchool\PhpWorkshop\Result\Cgi\FailureInterface> $results */
        $results = array_filter($result->getResults(), function (\PhpSchool\PhpWorkshop\Result\Cgi\ResultInterface $result) {
            return $result instanceof \PhpSchool\PhpWorkshop\Result\Cgi\FailureInterface;
        });

        if (!count($results)) {
            return '';
        }

        $output = '';
        foreach ($results as $key => $request) {
            $execution = $key + 1;
            $output .= "<p>Request {$execution} failed</p>";
            $output .=  "<p>Request Details:</p>";

            $httpRequest = $request->getRequest();

            $output .= "<p>URL: {$httpRequest->getUri()}</p>";
            $output .= "<p>METHOD: {$httpRequest->getMethod()}</p>";

            if ($httpRequest->getHeaders()) {
                $output .= '<p>HEADERS:</p>';

                $headers = [];
                foreach ($httpRequest->getHeaders() as $name => $values) {
                    $headers[] = sprintf("<li>%s: %s</li>", $name, implode(', ', $values));
                }

                $output .= sprintf("<ul>%s</ul>", implode('', $headers));
            }

            if ($body = (string) $httpRequest->getBody()) {
                $output .= '<p>BODY:</p>';

                switch ($httpRequest->getHeaderLine('Content-Type')) {
                    case 'application/json':
                        $output .= '<pre>' . json_encode(json_decode($body, true), JSON_PRETTY_PRINT) . '</pre>';
                        break;
                    default:
                        $output .= '<pre>' . $body . '</pre>';
                        break;
                }
            }
            $output .= $this->renderResult($request);
        }

        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    {$output}
                </div>
            </li>
        EOT;
    }

    private function renderCgiRequestFailure(CgiRequestFailure $result): string
    {
        $output = '';
        if ($result->headersDifferent()) {
            $output .= <<<EOT
                <p>Your Headers:</p>
                {$this->headers($result->getActualHeaders())}
                <p>Expected Headers:</p>
                {$this->headers($result->getExpectedHeaders(), false)}
            EOT;
        }

        if ($result->bodyDifferent()) {
            if ($result->headersAndBodyDifferent()) {
                $output .= '</br>';
            }

            $output .= <<<EOT
                <p>Your Output:</p>
                <p class="text-xs text-gray-300 mt-1 border-l-1 border-red-500">{$result->getActualOutput()}</p>
                <p>Expected Output:</p>
                <p class="text-xs text-gray-300 mt-1 border-l-1 border-green-500">{$result->getExpectedOutput()}</p>
            EOT;
        }



        return <<<EOT
            <li class="flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500">
                  <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                </svg>
                <div class="flex flex-col">
                    $output
                </div>
            </li>
        EOT;
    }

    /**
     * @param array<string, string> $headers
     */
    private function headers(array $headers, bool $actual = true): string
    {
        $output = '';
        foreach ($headers as $name => $value) {
            $output .= "<li>$name: $value</li>";
        }

        $colour = $actual ? 'red' : 'green';
        return <<<EOT
            <ul class="bg-{$colour}-500">
            </ul>
        EOT;
    }
}
