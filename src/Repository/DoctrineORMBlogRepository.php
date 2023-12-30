<?php

namespace PhpSchool\Website\Repository;

use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\Entity\BlogPost;
use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Entity\Workshop;
use RuntimeException;

/**
 * @template-extends EntityRepository<BlogPost>
 */
class DoctrineORMBlogRepository extends EntityRepository
{
    /**
     * @return array<BlogPost>
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(BlogPost $post): void
    {
        $this->_em->persist($post);
        $this->_em->flush();
    }

    public function remove(BlogPost $post): void
    {
        $this->_em->remove($post);
        $this->_em->flush();
    }

    public function deleteAll(): void
    {
        $this->_em->createQuery('DELETE FROM ' . BlogPost::class)->execute();
    }

    public function findBySlug(string $slug): BlogPost
    {
        $post = $this->findOneBy(['slug' => $slug]);

        if ($post === null) {
            throw new RuntimeException(sprintf('Cannot find post with slug: "%s"', $slug));
        }

        return $post;
    }
}
