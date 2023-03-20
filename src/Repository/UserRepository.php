<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        protected ManagerRegistry $registry,
        private UserPasswordHasherInterface $hasher,
        private ValidatorInterface $validator,
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[]
     */
    public function fetchUnnotified(): array
    {
        return $this->createQueryBuilder('users')
            ->innerJoin('users.subscribedBands', 'bands')
            ->addSelect('bands')
            ->andWhere(['users.notified = :notified'])
            ->setParameter('notified', false)
            ->getQuery()
            ->getResult();
    }

    public function createRegistered(User $user, bool $flush): void
    {
        if (null !== $this->fetchOneByEmail($user->getEmail())) {
            throw new InvalidArgumentException("The email is already occupied", Response::HTTP_BAD_REQUEST);
        }

        /** by default user doesn't need to be notified */
        $user->setIsNotified(true);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw(new InvalidArgumentException((string) $errors, Response::HTTP_BAD_REQUEST));
        }

        $hashedPassword = $this->hasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashedPassword);

        $user->eraseCredentials();
        $this->save($user, $flush);
    }

    public function fetchOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('users')
            ->innerJoin('users.subscribedBands', 'bands')
            ->addSelect('bands')
            ->andWhere('users.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function fetchAll(): array
    {
        return $this->createQueryBuilder('users')
            ->innerJoin('users.subscribedBands', 'bands')
            ->addSelect('bands')
            ->getQuery()
            ->getResult();
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
