<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\Visibility;
use ContainerYnlLcrf\getKnpPaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
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

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function getAllForProfile(Profile $profile, $currentPage = 1, $limit = 10): array
    {
        if ($profile == null)
            return [];

        $query =  $this->createQueryBuilder('post')
            ->andWhere('post.profile = :val')
            ->setParameter('val', $profile)
            ->orderBy('post.created_at', 'DESC')
            ->getQuery();

        return $this->paginate($query, $currentPage, $limit)
            ->getQuery()
            ->getResult();
    }

    public function getPublicOnly(Profile $profile, $currentPage = 1, $limit = 10): array
    {
        $query =  $this->createQueryBuilder('post')
            ->andWhere('post.profile = :val2')
            ->setParameter('val2', $profile)
            ->andWhere('post.visibility = :val')
            ->setParameter('val', Visibility::VISIBILITY_PUBLIC)
            ->orderBy('post.created_at', 'DESC')
            ->getQuery();

        return $this->paginate($query, $currentPage, $limit)
            ->getQuery()
            ->getResult();
    }

    public function getForFollowers(Profile $profile, $currentPage = 1, $limit = 10): array
    {
        if ($profile == null)
            return [];

        $query =  $this->createQueryBuilder('post')
            ->andWhere('post.profile = :val')
            ->setParameter('val', $profile)
            ->andWhere('post.visibility = :val2 OR post.visibility = :val3')
            ->setParameter('val2', Visibility::VISIBILITY_SUBSCRIBERS_ONLY)
            ->setParameter('val3', Visibility::VISIBILITY_PUBLIC)
            ->orderBy('post.created_at', 'DESC')
            ->getQuery();

        return $this->paginate($query, $currentPage, $limit)
            ->getQuery()
            ->getResult();
    }

    public function paginate($dql, $page = 1, $limit = 5)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }


//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
