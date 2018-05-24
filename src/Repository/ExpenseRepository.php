<?php
/**
 * Created by PhpStorm.
 * User: mazetuski
 * Date: 24/05/18
 * Time: 14:19
 */

namespace App\Repository;


use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class ExpenseRepository extends EntityRepository
{

    /**
     * Function for get expenses by time and user
     * @param User $user
     * @param \DateTime|null $dateStart
     * @param \DateTime|null $dateEnd
     * @return array
     */
    public function getExpenseByTimeAndUser(User $user, \DateTime $dateStart = null, \DateTime $dateEnd = null)
    {

        // Check if dates exists
        if (!$dateStart) {
            $dateStart = new \DateTime();
        }

        if (!$dateEnd) {
            $dateEnd = new \DateTime();
        }

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from('App:Expense' , 'e')
            ->innerJoin('e.user', 'u')
            ->where('u = :user')
            ->andWhere('e.createdAt >= :dateStart')
            ->andWhere('e.createdAt <= :dateEnd');

        // Set parameters
        $query->setParameter(':user', $user)
        ->setParameter(':dateStart', $dateStart)
        ->setParameter(':dateEnd', $dateEnd);

        return $query->getQuery()->getArrayResult();
    }

}