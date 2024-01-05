<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Repository\DoctrineORMBlogRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogPost
{
    use JsonUtils;

    public function __construct(private DoctrineORMBlogRepository $repository)
    {
    }

    public function __invoke(Request $request, Response $response, string $slug): Response
    {
        return $this->withJson(
            [
                'post' => $this->repository->findBySlug($slug),
            ],
            $response
        );
    }
}
