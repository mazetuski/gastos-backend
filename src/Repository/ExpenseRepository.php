<?php
/**
 * Created by PhpStorm.
 * User: mazetuski
 * Date: 24/05/18
 * Time: 14:19
 */

namespace App\Repository;


use App\Entity\Expense;
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

    /**
     * Function for create new Expense
     * @param User $user
     * @param $name
     * @param $price
     * @return Expense
     */
    public function createExpense(User $user, $name, $price){
        // Create and initialize expense
        $expense = new Expense();
        $expense->setName($name);
        $expense->setPrice($price);
        $expense->setUser($user);
        // Get posible identifier
        $identifier = strtolower(str_replace(' ', '_', $name));

        // Get expenses that have a identifier like that
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from('App:Expense', 'e')
            ->where("e.identifier LIKE :identifier" );
        $query->setParameter(':identifier', '%'.$identifier.'%');
        /** @var Expense $expenseResult */
        $expenseResult = $query->getQuery()->getResult();

        // If expense with identifier like new exists, put the old one
        if($expenseResult && count($expenseResult)>0){
            $expense->setIdentifier($expenseResult[0]->getIdentifier());
        }else{
            $expense->setIdentifier($identifier);
        }

        return $expense;
    }

}