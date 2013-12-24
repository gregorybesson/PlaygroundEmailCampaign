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

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="emailcampaign_campaign")
 */
class Campaign implements InputFilterAwareInterface
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
     * @ORM\Column(type="string")
     */
    protected $fromName;

    /**
     * @ORM\Column(type="string")
     */
    protected $fromEmail;

    /**
     * @ORM\Column(type="string")
     */
    protected $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="campaigns", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $template;

    /**
     * @ORM\ManyToOne(targetEntity="MailingList", inversedBy="campaigns", cascade={"persist"})
     * @ORM\JoinColumn(name="mailing_list_id", referencedColumnName="id")
     */
    protected $mailingList;

    /**
     * @ORM\Column(type="boolean", name="is_tracked")
     */
    protected $isTracked;

    /**
     * @ORM\Column(type="string")
     */
    protected $trackingURL;

    /**
     * @ORM\Column(type="string")
     */
    protected $unsubscribeURL;

    /**
     * @ORM\Column(name="distant_id", type="string", nullable=true)
     */
    protected $distantId;

    /**
     * @ORM\Column(type="boolean", name="is_sent")
     */
    protected $isSent=false;

    /**
     * @ORM\Column(type="datetime", name="sending_time", nullable=true)
     */
    protected $sendingTime;

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

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function populate($data= array())
    {
        if (isset($data['name']) && $data['name'] != null) {
            $this->name = $data['name'];
        }
        if (isset($data['description']) && $data['description'] != null) {
            $this->description = $data['description'];
        }
        if (isset($data['fromName']) && $data['fromName'] != null) {
            $this->fromName = $data['fromName'];
        }
        if (isset($data['fromEmail']) && $data['fromEmail'] != null) {
            $this->fromEmail = $data['fromEmail'];
        }
        if (isset($data['$subject']) && $data['$subject'] != null) {
            $this->$subject = $data['$subject'];
        }
        if (isset($data['template']) && $data['template'] != null) {
            $this->template = $data['template'];
        }
        if (isset($data['mailingList']) && $data['mailingList'] != null) {
            $this->mailingList = $data['mailingList'];
        }
        if (isset($data['isTracked']) && $data['isTracked'] != null) {
            $this->isTracked = $data['isTracked'];
        }
        if (isset($data['trackingURL']) && $data['trackingURL'] != null) {
            $this->trackingURL = $data['trackingURL'];
        }
        if (isset($data['unsubscribeURL']) && $data['unsubscribeURL'] != null) {
            $this->unsubscribeURL = $data['unsubscribeURL'];
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
                'allowEmpty' => false,
                'validators' => array(
                    array('name' => 'NotEmpty',),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'description',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'fromName',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'StringToLower', 'options' => array('encoding' => 'UTF-8')),
                ),
                'validators' => array(
                    array('name' => 'StringLength', 'options' => array('min'=>1, 'max' => 255)),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'fromEmail',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'StringToLower', 'options' => array('encoding' => 'UTF-8')),
                ),
                'validators' => array(
                    array('name' => 'StringLength', 'options' => array('min'=>1, 'max' => 255)),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'subject',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'StringToLower', 'options' => array('encoding' => 'UTF-8')),
                ),
                'validators' => array(
                    array('name' => 'StringLength', 'options' => array('min'=>1, 'max' => 255)),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'trackingURL',
                'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'unsuscribeLink',
                'required' => false,
            )));
            $this->inputFilter= $inputFilter;
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

    public function getFromName()
    {
        return $this->fromName;
    }

    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
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

    public function getIsTracked()
    {
        return $this->isTracked;
    }

    public function setIsTracked($isTracked)
    {
        $this->isTracked = $isTracked;
        return $this;
    }

    public function getTrackingURL()
    {
        return $this->trackingURL;
    }

    public function setTrackingURL($trackingURL)
    {
        $this->trackingURL = $trackingURL;
        return $this;
    }

    public function getUnsubscribeURL()
    {
        return $this->unsubscribeURL;
    }

    public function setUnsubscribeURL($unsubscribeURL)
    {
        $this->unsubscribeURL = $unsubscribeURL;
        return $this;
    }

    public function getDistantId()
    {
        return $this->distantId;
    }

    public function setDistantId($distantId)
    {
        $this->distantId = $distantId;
        return $this;
    }

    public function getIsSent()
    {
        return $this->isSent;
    }

    public function setIsSent($isSent)
    {
        $this->isSent = $isSent;
        return $this;
    }

    public function getSendingTime()
    {
        return $this->sendingTime;
    }

    public function setSendingTime($sendingTime)
    {
        $this->sendingTime = $sendingTime;
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