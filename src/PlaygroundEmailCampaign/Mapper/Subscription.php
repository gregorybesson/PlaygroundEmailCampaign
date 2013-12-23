<?php

namespace PlaygroundEmailCampaign\Mapper;

use Doctrine\ORM\AbstractQuery as Query;
use PlaygroundEmailCampaign\Entity\Subscription as SubscriptionEntity;

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

    public function findOneBy($array = array(), $sortArray = array())
    {
        return $this->getEntityRepository()->findOneBy($array, $sortArray);
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
                WHERE s.mailingList = :list
                AND s.status != :status'
            .( ! empty($sortArray) ? 'ORDER BY s.'.key($sortArray).' '.current($sortArray) : '' )
        );
        $query->setParameter('list', $list);
        $query->setParameter('status', SubscriptionEntity::STATUS_CLEARED);
        return $query;
    }

    public function queryByContact($contact, $sortArray = array())
    {
        $query = $this->em->createQuery(
            'SELECT s FROM PlaygroundEmailCampaign\Entity\Subscription s
                WHERE s.contact = :contact'
            .( ! empty($sortArray) ? 'ORDER BY s.'.key($sortArray).' '.current($sortArray) : '' )
        );
        $query->setParameter('contact', $contact);
        return $query->getResult(Query::HYDRATE_OBJECT);
    }

    public function isRegistered($list, $contact)
    {
        $results = $this->getEntityRepository()->findBy(
            array(
                'mailingList' => $list,
                'contact' => $contact,
            ));
        return (!empty($results)) ? current($results) : false;
    }
}