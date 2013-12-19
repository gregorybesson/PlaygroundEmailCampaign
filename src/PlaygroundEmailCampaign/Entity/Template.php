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
     * @ORM\Column(name="preview_url", type="string", nullable=true)
     */
    protected $previewURL;

    /**
     * @ORM\Column(name="html_content", type="text")
     */
    protected $htmlContent = '';

    // Id dans la base du web mail
    /**
     * @ORM\Column(name="distant_id", type="integer", nullable=true)
     */
    protected $distantId;

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

    public function getPreviewURL()
    {
        return $this->previewURL;
    }

    public function setPreviewURL($previewURL)
    {
        $this->previewURL = $previewURL;
        return $this;
    }

    public function getDistantId()
    {
        return $this->distantId;
    }

    public function setDistantID($distantId)
    {
        $this->distantId = $distantId;
        return $this;
    }

    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    public function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;
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
        if (isset($data['previewURL']) && $data['previewURL'] != null) {
            $this->previewURL = $data['previewURL'];
        }
        if (isset($data['htmlContent']) && $data['htmlContent'] != null) {
            $this->htmlContent = $data['htmlContent'];
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
                'name' => 'previewURL',
                'required' => false,
                'allowEmpty' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'htmlContent',
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