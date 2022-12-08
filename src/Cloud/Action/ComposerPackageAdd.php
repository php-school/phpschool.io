<?php

namespace PhpSchool\Website\Cloud\Action;

use ahinkle\PackagistLatestVersion\PackagistLatestVersion;
use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ComposerPackageAdd
{
    use JsonUtils;

    private PackagistLatestVersion $packagistLatestVersion;

    public function __construct(PackagistLatestVersion $packagistLatestVersion)
    {
        $this->packagistLatestVersion = $packagistLatestVersion;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer): Response
    {
        $package = $request->getQueryParams()['package'] ?? null;

        if (empty($package)) {
            return $this->withJson(['status' => 'error', 'message' => 'No package set'], $response, 404);
        }

        if (count(explode('/', $package)) !== 2) {
            return $this->withJson(['status' => 'error', 'message' => 'Not a valid package name'], $response, 404);
        }

        $version = $this->packagistLatestVersion->getLatestRelease($package);

        if ($version === null) {
            return $this->withJson(['status' => 'error', 'message' => 'Package metadata not found'], $response, 404);
        }

        $data = [
            'package_name' => $package,
            'latest_version' => $version['version'],
        ];

        return $this->withJson($data, $response);
    }
}
