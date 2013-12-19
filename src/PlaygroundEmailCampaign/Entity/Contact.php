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

use PlaygroundUser\Entity\User;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(
 *              name="emailcampaign_contact",
 *              uniqueConstraints={@UniqueConstraint(name="contact_match_user", columns={"user_id"})}
 *           )
 */
class Contact implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="\PlaygroundUser\Entity\User", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $optin;

    /**
     * @ORM\Column(name="optin_datetime", type="datetime", nullable=true)
     */
    protected $optinDatetime;

    /**
     * @ORM\Column(name="optout_datetime", type="datetime", nullable=true)
     */
    protected $optoutDatetime;

    /**
     * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="unsubscribed_contacts")
     * @ORM\JoinColumn(name="unsubscribed_campaign_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $unsubscribedCampaign;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $client;

    // only TEXT or HTML
    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     */
    protected $emailType;

    /**
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="contact")
     */
    private $subscriptions;

    /**
     * @ORM\Column(name="distant_id", type="integer", nullable=true)
     */
    protected $distantId;

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('optin')) {
            if ($optin) {
                $optinDatetime = new \DateTime();
            } else {
                $optoutDatetime = new \DateTime();
            }
        }
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
        if (isset($data['user']) && $data['user'] != null) {
            $this->user = $data['user'];
        }
        if (isset($data['optin']) && $data['optin'] != null) {
            $this->optin = $data['optin'];
        }
        if (isset($data['optinDatetime']) && $data['optinDatetime'] != null) {
            $this->optinDatetime = $data['optinDatetime'];
        }
        if (isset($data['optoutDatetime']) && $data['optoutDatetime'] != null) {
            $this->optoutDatetime = $data['optoutDatetime'];
        }
        if (isset($data['unsubscribedCampaign']) && $data['unsubscribedCampaign'] != null) {
            $this->unsubscribedCampaign = $data['unsubscribedCampaign'];
        }
        if (isset($data['client']) && $data['client'] != null) {
            $this->client = $data['client'];
        }
        if (isset($data['emailType']) && $data['emailType'] != null) {
            $this->emailType = $data['emailType'];
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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getOptin()
    {
        return $this->optin;
    }

    public function setOptin($optin)
    {
        $this->optin = $optin;
        return $this;
    }

    public function getOptinDatetime()
    {
        return $this->optinDatetime;
    }

    public function setOptinDatetime($optinDatetime)
    {
        $this->optinDatetime = $optinDatetime;
        return $this;
    }

    public function getOptoutDatetime()
    {
        return $this->optoutDatetime;
    }

    public function setOptoutDatetime($optoutDatetime)
    {
        $this->optoutDatetime = $optoutDatetime;
        return $this;
    }

    public function getUnsubscribedCampaign()
    {
        return $this->unsubscribedCampaign;
    }

    public function setUnsubscribedCampaign($unsubscribedCampaign)
    {
        $this->unsubscribedCampaign = $unsubscribedCampaign;
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    public function getEmailType()
    {
        return $this->emailType;
    }

    public function setEmailType($emailType)
    {
        $this->emailType = $emailType;
        return $this;
    }

    public function getSubscrptions()
    {
        return $this->subscriptions;
    }

    public function getDistantID() {
        return $this->distantID;
    }

    public function setDistantID($distantID) {
        $this->distantID = $distantID;
        return $this;
    }
}