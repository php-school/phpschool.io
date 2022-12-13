<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ComposerPackageSearch
{
    use JsonUtils;

    private const COMPOSER_PACKAGES_FILE_LOCATION = __DIR__ . '/../../../var/packages.json';

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer): Response
    {
        $search = $request->getQueryParams()['package'] ?? null;

        if (empty($search)) {
            return $this->withJson(['status' => 'error', 'message' => 'No package set'], $response, 404);
        }

        $packages = json_decode(file_get_contents(self::COMPOSER_PACKAGES_FILE_LOCATION), true);

        $results = [];
        foreach ($packages['packageNames'] ?? [] as $packageName) {
            if (count($results) === 10) {
                break;
            }

            if (str_starts_with($packageName, $search)) {
                $results[] = $packageName;
            }
        }

        $results = array_unique($results);

        return $this->withJson(['results' => $results], $response);
    }
}