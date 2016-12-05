<?php

namespace AppBundle\Utils\Notification\Messages;
use Doctrine\ORM\EntityManager;

/**
 * Class AbstractMessage
 *
 * @package AppBundle\Utils\Notification\Messages
 */
abstract class AbstractMessage
{
    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;
    /**
     * Indicates notification title
     *
     * @var string
     */
    protected $title = null;

    /**
     * Indicates notification body text
     *
     * @var string
     */
    protected $body = null;

    /**
     * Indicates the action associated with a user click on the notification
     *
     * @var string
     */
    protected $clickAction = null;

    /**
     * Indicates a sound to play when the device receives a notification
     *
     * @var string
     */
    protected $sound = null;

    /**
     * Indicates notification icon(Android)
     *
     * @var string
     */
    protected $icon = null;

    /**
     * Indicates whether each notification results in a new entry in
     * the notification drawer on Android(Android)
     *
     * @var string
     */
    protected $tag = null;

    /**
     * Indicates color of the icon, expressed in #rrggbb format(Android)
     *
     * @var string
     */
    protected $color = null;

    /**
     * Indicates the badge on the client app home icon(IOS)
     *
     * @var string
     */
    protected $badge = null;

    /**
     * AbstractMessage constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get keys array
     *
     * @return array
     */
    abstract public function getParameters();

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getClickAction()
    {
        return $this->clickAction;
    }

    /**
     * @param string $clickAction
     */
    public function setClickAction($clickAction)
    {
        $this->clickAction = $clickAction;
    }

    /**
     * @return string
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * @param string $sound
     */
    public function setSound($sound)
    {
        $this->sound = $sound;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param string $badge
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }

    /**
     * @return array
     */
    protected function createNotificationArray()
    {
        $notification = [];
        if (!is_null($this->title) & !empty($this->title)) {
            $notification['title'] = $this->title;
        }
        if (!is_null($this->body) & !empty($this->body)) {
            $notification['body'] = $this->body;
        }
        if (!is_null($this->sound) & !empty($this->sound)) {
            $notification['sound'] = $this->sound;
        }
        if (!is_null($this->clickAction) & !empty($this->clickAction)) {
            $notification['clickAction'] = $this->clickAction;
        }
        if (!is_null($this->icon) & !empty($this->icon)) {
            $notification['icon'] = $this->icon;
        }
        if (!is_null($this->tag) & !empty($this->tag)) {
            $notification['tag'] = $this->tag;
        }
        if (!is_null($this->color) & !empty($this->color)) {
            $notification['color'] = $this->color;
        }
        if (!is_null($this->badge) & !empty($this->badge)) {
            $notification['badge'] = $this->badge;
        }
        
        return $notification;
    }
}