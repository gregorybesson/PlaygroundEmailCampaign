<?php

namespace PlaygroundEmailCampaign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use DateTime;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(
 *              name="emailcampaign_subscription",
 *              uniqueConstraints={@UniqueConstraint(name="single_subscription", columns={"contact_id", "mailing_list_id"})}
 *           )
 */
class Subscription implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="subscriptions", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $contact;

    /**
     * @ORM\ManyToOne(targetEntity="MailingList", inversedBy="subscriptions", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="mailing_list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $mailingList;


    // Only : pending, subscribed, unsubscribed, cleared
    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updatedAt;

    /**
     * @PrePersist
     */
    public function createChrono()
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @PreUpdate
     */
    public function updateChrono()
    {
        $this->updatedAt = new \DateTime("now");
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function populate($data = array()) {
        if (isset($data['contact']) && $data['contact'] != null) {
            $this->contact = $data['contact'];
        }
        if (isset($data['mailingList']) && $data['mailingList'] != null) {
            $this->mailingList = $data['mailingList'];
        }
        if (isset($data['status']) && $data['status'] != null) {
            $this->status = $data['status'];
        }
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new Factory();

            $inputFilter->add($factory->createInput(array('name' => 'id', 'required' => true, 'filters' => array(array('name' => 'Int'),),)));

            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'StringLength', 'options' => array('min'=>1, 'max' => 255)),
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact($contact)
    {
        $this->contact = $contact;
        return $this;
    }

    public function getMailingList()
    {
        return $this->mailingList;
    }

    public function setMailingList($mailingList)
    {
        $this->mailingList = $mailingList;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return the unknown_type
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param unknown_type $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return the unknown_type
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param unknown_type $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}