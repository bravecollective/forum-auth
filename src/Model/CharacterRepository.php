<?php
namespace Brave\ForumAuth\Model;

use Doctrine\ORM\EntityRepository;

class CharacterRepository extends EntityRepository
{

    /**
     *
     * {@inheritDoc}
     * @see \Doctrine\ORM\EntityRepository::find()
     * @return Character|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }
}
