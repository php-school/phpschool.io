<?php

namespace PhpSchool\Website\Action;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Session;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentLogin
{
    use RedirectUtils;

    private Session $session;
    private EntityManagerInterface $entityManager;

    public function __construct(Session $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
//        if ($this->session->get('student') instanceof Student) {
//            return $this->redirect('/cloud');
//        }

//        $provider = new \League\OAuth2\Client\Provider\Github([
//            'clientId'          => '73f5718e4f24ca2f955c',
//            'clientSecret'      => 'cb38ab5d8fa3e8da4913cf102432091fce085e89',
//        ]);

        //dev
        $provider = new \League\OAuth2\Client\Provider\Github([
            'clientId'          => 'a1473555b64653495b46',
            'clientSecret'      => '4124b0d6b15e51734b2a1f4ee2d32490e61c1c3c',
        ]);


        $params = $request->getQueryParams();

        if ($request->getMethod() === 'GET' && !isset($params['code'])) {
            $authUrl = $provider->getAuthorizationUrl();
            $this->session->set('oauth2state', $provider->getState());
            return $this->redirect($authUrl);
        }

        if ($request->getMethod() === 'GET') {
            if (($params['state'] ?? '') !== $this->session->get('oauth2state')) {
                return $this->redirect('/');
            }
        }

        /** @var AccessToken $token */
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $params['code']
        ]);

        try {
            /** @var GithubResourceOwner $user */
            $user = $provider->getResourceOwner($token);

            $student = $this->findStudent($user);

            if ($student === null) {
                try {
                    $student = $this->createStudent($user);
                } catch (\Exception $e) {
                    return $this->redirect('/cloud');
                }
            }

            $this->session->set('student', $student);

            return $this->redirect('/cloud');
        } catch (IdentityProviderException $e) {
            return $this->redirect('/');
        }
    }

    private function findStudent(GithubResourceOwner $resourceOwner): ?Student
    {
        return $this->entityManager->getRepository(Student::class)
            ->findOneBy(['githubId' => $resourceOwner->getId()]);
    }

    private function createStudent(GithubResourceOwner $user): Student
    {
        $id = is_int($user->getId()) ? (string) $user->getId() : null;
        $username = $user->getNickname();
        $email = $user->getEmail();
        $name = $user->getName();

        $this->assertHasValue($id);
        $this->assertHasValue($username);
        $this->assertHasValue($email);
        $this->assertHasValue($name);

        $student = new Student(
            $id,
            $username,
            $email,
            $name,
            $user->toArray()['avatar_url'] ?? null,
            $user->toArray()['location'] ?? null
        );

        $this->entityManager->persist($student);
        $this->entityManager->flush();

        return $student;
    }

    private function assertHasValue(?string $value): void
    {
        if ($value === null || $value === '') {
            throw new \RuntimeException('Missing data from GitHub');
        }
    }
}
