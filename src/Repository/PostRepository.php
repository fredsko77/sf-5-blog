<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param string|null $search
     * @param int $page
     * @param int $itemsPerPage
     * @param array|null $sort
     *
     * @return null|Post[]
     */
    public function adminPost(?string $search = null, ?array $sort = null, ?string $state = null)
    {
        $query = $this->createQueryBuilder('p');

        if ($search !== null) {
            $query->where('MATCH_AGAINST(p.title, p.summary, p.content)AGAINST(:search boolean)>0')
                ->setParameter(':search', $search)
            ;
        }

        if ($state !== null) {
            $query->andWhere('p.state = :state')
                ->setParameter(':state', $state);
        }

        if (is_array($sort)) {
            $key = array_key_first($sort);
            $query->orderBy('p.' . (string) $key, $sort[$key]);
        }

        return $query->getQuery();
    }

    /**
     * Latest posts for Homepage
     * @return null|Post[] Returns an array of Post objects or null
     */
    public function latest()
    {
        return $this->createQueryBuilder('p')
            ->where('p.state = :state')
            ->andWhere('p.visibility <> :visibility')
            ->setParameter(':state', Post::STATE_PUBLISHED)
            ->setParameter(':visibility', Post::VISIBILITY_PRIVATE)
            ->orderBy('p.updatedAt', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Undocumented function
     *
     * @return null|Post[] Returns an array of Posts or null
     */
    public function related(Post $post)
    {
        return $this->createQueryBuilder('p')
            ->where('p.state = ' . Post::STATE_PUBLISHED)
            ->andWhere('p.visibility <> ' . Post::VISIBILITY_PRIVATE)
            ->andWhere('p.category = ' . $post->getCategory())
            ->andWhere('p.id <> ' . $post->getId())
            ->orderBy('p.updatedAt', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('p')
    ->andWhere('p.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('p.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?Post
{
return $this->createQueryBuilder('p')
->andWhere('p.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
