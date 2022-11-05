<?php

namespace PhpSchool\Website\Action;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\FlashMessages;
use PhpSchool\Website\User\Session;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentLogin
{
    use RedirectUtils;

    private Session $session;
    private EntityManagerInterface $entityManager;
    private Github $githubProvider;
    private FlashMessages $messages;

    public function __construct(
        Github $githubProvider,
        Session $session,
        EntityManagerInterface $entityManager,
        FlashMessages $messages
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->githubProvider = $githubProvider;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
        if ($this->session->get('student') instanceof StudentDTO) {
            return $this->redirect('/cloud');
        }

        $params = $request->getQueryParams();

        if ($request->getMethod() === 'GET' && !isset($params['code'])) {
            $authUrl = $this->githubProvider->getAuthorizationUrl();
            $this->session->set('oauth2state', $this->githubProvider->getState());
            return $this->redirect($authUrl);
        }

        if ($request->getMethod() === 'GET') {
            if (($params['state'] ?? '') !== $this->session->get('oauth2state')) {
                return $this->redirect('/');
            }
        }

        /** @var AccessToken $token */
        $token = $this->githubProvider->getAccessToken('authorization_code', [
            'code' => $params['code']
        ]);

        try {
            /** @var GithubResourceOwner $user */
            $user = $this->githubProvider->getResourceOwner($token);

            $student = $this->findStudent($user);

            if ($student) {
                $this->updateStudent($student, $user);
            }

            if ($student === null) {
                try {
                    $student = $this->createStudent($user);
                } catch (\Exception $e) {
                    $this->messages->addMessage('error', 'Could not authenticate with GitHub. Please try again later.');
                    return $this->redirect('/cloud');
                }
            }

            $this->session->set('student', $student->toDTO());
            $this->session->regenerate();

            $this->messages->addMessage('success', 'Successfully authenticated with GitHub.');

            return $this->redirect('/cloud');
        } catch (IdentityProviderException $e) {
            $this->messages->addMessage('error', 'Could not authenticate with GitHub. Please try again later.');

            return $this->redirect('/cloud');
        }
    }

    private function findStudent(GithubResourceOwner $resourceOwner): ?Student
    {
        return $this->entityManager->getRepository(Student::class)
            ->findOneBy(['githubId' => $resourceOwner->getId()]);
    }

    private function createStudent(GithubResourceOwner $user): Student
    {
        $id = $this->parseId($user->getId());
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

    private function parseId(?int $id): ?string
    {
        return is_int($id) ? (string) $id : null;
    }

    private function assertHasValue(?string $value): void
    {
        if ($value === null || $value === '') {
            throw new \RuntimeException('Missing data from GitHub');
        }
    }

    private function updateStudent(Student $student, GithubResourceOwner $user): void
    {
        $username = $user->getNickname();
        $email = $user->getEmail();
        $name = $user->getName();

        $this->assertHasValue($username);
        $this->assertHasValue($email);
        $this->assertHasValue($name);

        $student->setUsername($username);
        $student->setEmail($email);
        $student->setName($name);
        $student->setProfilePicture($user->toArray()['avatar_url'] ?? null);
        $student->setLocation($user->toArray()['location'] ?? null);

        $this->entityManager->persist($student);
        $this->entityManager->flush();
    }
}
