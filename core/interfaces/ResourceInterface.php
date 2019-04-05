<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 * @since 1.0
 *
 */


namespace credglv\core\interfaces;


interface ResourceInterface
{
    /**
     * @return boolean
     */
    public function isInline();

    /**
     * @param boolean $isInline
     * @return $this
     */
    public function setInline($isInline);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getInlineContent();

    /**
     * @param string $content
     * @return $this
     */
    public function setInlineContent($content);

    /**
     * Return array of this resource dependencies
     * @return mixed
     */
    public function getDependencies();
}