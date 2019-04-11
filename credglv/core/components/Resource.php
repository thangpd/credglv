<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */



namespace credglv\core\components;


use credglv\core\BaseObject;
use credglv\core\interfaces\ResourceInterface;
use prefix_credcoin\logMono;


class Resource extends BaseObject implements ResourceInterface
{
    /**
     * @var string
     */
    public $baseUrl = '';
    /**
     *
     * @var bool
     */
    public $isInline = false;
    /**
     * @var string
     */
    public $inlineContent = '';
    /**
     * @var string ID of this resource
     */
    public $id;
    /**
     * @var array
     */
    public $dependencies = [];
    /**
     * @var string
     */
    public $url;

    /**
     * @return boolean
     */
    public function isInline()
    {
        return $this->isInline;
    }

    /**
     * @param boolean $isInline
     * @return $this
     */
    public function setInline($isInline)
    {
        // TODO: Implement setInline() method.
        $this->isInline = $isInline;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (!preg_match('/^http/i', $this->url) && preg_match('/^\//', $this->url)) {
            return plugins_url(CREDGLV_NAME . '/' . $this->url);
        }
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        // TODO: Implement setUrl() method.
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        // TODO: Implement setId() method.
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getInlineContent()
    {
        // TODO: Implement getInlineContent() method.
        return $this->inlineContent;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setInlineContent($content)
    {
        // TODO: Implement setInlineContent() method.
        $this->inlineContent = $content;
        return $this;
    }

    /**
     * Return array of this resource dependencies
     * @return mixed
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
}