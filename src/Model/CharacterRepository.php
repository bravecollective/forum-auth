<?php
namespace Brave\ForumAuth\Model;

use Doctrine\ORM\EntityRepository;

class CharacterRepository extends EntityRepository
{

    /**
     * {@inheritDoc}
     * @see \Doctrine\ORM\EntityRepository::find()
     * @return Character|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * {@inheritDoc}
     * @see \Doctrine\ORM\EntityRepository::findBy()
     * @return Character[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}
