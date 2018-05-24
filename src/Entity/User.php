<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ExclusionPolicy("none")
 */
class User extends BaseUser
{

    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Expense", mappedBy="user")
     */
    private $expense;

    public function __construct()
    {
        parent::__construct();
        $this->expense = new ArrayCollection();
    }

    /**
     * @param Expense $expense
     * @return $this
     */
    public function addExpense(Expense $expense){
        $this->expense[] = $expense;
    }

    /**
     * @return mixed
     */
    public function getExpense()
    {
        return $this->expense;
    }

    public function removeMinerResult(Expense $expense)
    {
        $this->expense->removeElement($expense);
    }

}