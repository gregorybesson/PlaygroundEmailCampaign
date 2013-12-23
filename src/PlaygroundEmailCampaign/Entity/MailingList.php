<?php

namespace PlaygroundEmailCampaign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(
 *      name="emailcampaign_mailinglist",
 *      uniqueConstraints={@UniqueConstraint(name="unique_name", columns={"name"})}
 *      )
 */
class MailingList implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="mailingList")
     */
    private $subscriptions;

    /**
     * @ORM\Column(name="distant_id", type="string", nullable=true)
     */
    protected $distantId;

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

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function populate($data = array())
    {
        if (isset($data['name']) && $data['name'] != null) {
            $this->name = $data['name'];
        }
        if (isset($data['description']) && $data['description'] != null) {
            $this->description = $data['description'];
        }
        if (isset($data['distantId']) && $data['distantId'] != null) {
            $this->distantId = $data['distantId'];
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function getDistantId() {
        return $this->distantId;
    }

    public function setDistantId($distantId) {
        $this->distantId = $distantId;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}