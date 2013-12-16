<?php

namespace PlaygroundEmailCampaign\Mapper;

use Doctrine\ORM\AbstractQuery as Query;

class Subscription
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $er;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em      = $em;
    }

    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('\PlaygroundEmailCampaign\Entity\Subscription');
        }

        return $this->er;
    }

    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    public function findBy($array = array(), $sortArray = array())
    {
        return $this->getEntityRepository()->findBy($array, $sortArray);
    }

    public function insert($entity)
    {
        return $this->persist($entity);
    }

    public function update($entity)
    {
        return $this->persist($entity);
    }

    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function queryByList($list, $sortArray = array())
    {
        $query = $this->em->createQuery(
            'SELECT s FROM PlaygroundEmailCampaign\Entity\Subscription s
                WHERE mailingList = :list'
            .( ! empty($sortArray) ? 'ORDER BY l.'.key($sortArray).' '.current($sortArray) : '' )
        );
        $query->setParameter('list', $list);
        return $query;
    }

    public function queryByContact($contact, $sortArray = array())
    {
        $query = $this->em->createQuery(
            'SELECT s FROM PlaygroundEmailCampaign\Entity\Subscription s
                WHERE contact = :contact'
            .( ! empty($sortArray) ? 'ORDER BY l.'.key($sortArray).' '.current($sortArray) : '' )
        );
        $query->setParameter('contact', $contact);
        return $query->getResult(Query::HYDRATE_OBJECT);
    }
}