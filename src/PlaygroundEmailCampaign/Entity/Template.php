<?php

namespace PlaygroundEmailCampaign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(
 *              name="emailcampaign_template",
 *              uniqueConstraints={@UniqueConstraint(name="title", columns={"title"})}
 *           )
 */
class Template implements InputFilterAwareInterface
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
    protected $title;

    /**
     * @ORM\Column(name="html_file_url", type="string", nullable=true)
     */
    protected $htmlFileURL;

    // Id dans la base du web mail
    /**
     * @ORM\Column(name="distant_id", type="integer", nullable=true)
     */
    protected $distantID;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getHtmlFileURL()
    {
        return $this->htmlFileURL;
    }

    public function setHtmlFileURL($htmlFileURL)
    {
        $this->htmlFileURL = $htmlFileURL;
        return $this;
    }

    public function getDistantID() {
        return $this->distantID;
    }

    public function setDistantID($distantID) {
        $this->distantID = $distantID;
        return $this;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function populate($data= array())
    {
        if (isset($data['title']) && $data['title'] != null) {
            $this->title = $data['title'];
        }
        if (isset($data['htmlFileURL']) && $data['htmlFileURL'] != null) {
            $this->htmlFileURL = $data['htmlFileURL'];
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
                'name' => 'title',
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('name' => 'NotEmpty',),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'htmlFileURL',
                'required' => false,
                'allowEmpty' => true,
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

}