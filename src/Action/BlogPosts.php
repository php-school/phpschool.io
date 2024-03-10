<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Repository\DoctrineORMBlogRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogPosts
{
    use JsonUtils;

    public function __construct(private readonly DoctrineORMBlogRepository $repository) {}

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(
            [
                'posts' => $this->repository->findAll(),
            ],
            $response
        );
    }
}
