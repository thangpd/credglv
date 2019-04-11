<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\models;


use credglv\core\BaseObject;
use credglv\core\RuntimeException;

class Student extends UserModel
{
    /**
     * @var \WP_User
     */
    public $user;
    /**
     * Instructor constructor.
     * @param \WP_User|int $user
     */
    public function __construct($user)
    {
        parent::__construct([]);
        if (!is_object($user)) {
            if (is_numeric($user)) {
                $user = get_user_by('ID', $user);
            } else {
                $user = get_user_by('login', $user);
            }
        }
        if (empty($user)) {
            throw new RuntimeException(__('Invalid user', 'credglv'));
        }
        $this->user  = $user;
    }

    /**
     * @return mixed
     */
    public function getProfileUrl()
    {
        $defaultUrl = credglv()->page->getPageUrl(credglv()->config->getUrlConfigs('credglv_user_profile'));
        return credglv()->hook->registerFilter('credglv_student_profile_url', $defaultUrl);
    }



    /**
     * Get data of current user
     * @return Student|null
     */
    public static function getCurrentUser()
    {
        if ( is_user_logged_in() ) {
            $userId = get_current_user_id();
            return new Student($userId);
        }
        return null;
    }



}