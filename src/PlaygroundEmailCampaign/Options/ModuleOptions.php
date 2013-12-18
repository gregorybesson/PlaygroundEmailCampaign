<?php

namespace PlaygroundEmailCampaign\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * drive path to weather media files
     */
    protected $media_path = 'public/media/email-campaign';

    /**
     * url path to story media files
     */
    protected $media_url = 'media/email-campaign';

    /**
     * web mail service name
     * available values : MailChimp
     */
    protected $service = 'MailChimp';

    /**
     * web mail user API Key
     */
    protected $userKey = '999f451057b5f5fbfb3bda744d9ffaf6-us3';

    public function setMediaPath($media_path)
    {
        $this->media_path = trim($media_path);

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaPath()
    {
        return $this->media_path;
    }

    public function setMediaUrl($media_url)
    {
        $this->media_url = trim($media_url);

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->media_url;
    }

    public function getService()
    {
        return $this->service;
    }

    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    public function getUserKey()
    {
        return $this->userKey;
    }

    public function setUserKey($userKey)
    {
        $this->userKey = $userKey;
        return $this;
    }

}
