<?php

namespace App\EventListener;

use App\Entity\Avatar\Avatar;
use App\Entity\Table\Table;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class CacheImageListener
{
    /**
     * @param CacheManager $cacheManager
     */
    public function __construct(private readonly CacheManager $cacheManager)
    {
    }

    /**
     * @param PreUpdateEventArgs $args
     *
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $change = $args->getEntityChangeSet();

        if ($entity instanceof Table) {
            if (array_key_exists('picture', $change)) {
                // clear cache of thumbnail with the old file
                $this->cacheManager->remove('uploads/images/tables/' . $change['picture'][0]);
            }
        }

        if ($entity instanceof Avatar) {
            if (array_key_exists('path', $change)) {
                // clear cache of thumbnail with the old file
                $this->cacheManager->remove('uploads/images/avatars/' . $change['path'][0]);
            }
        }
    }

    /**
     * When delete entity so remove all thumbnails related
     *
     * @param LifecycleEventArgs $args
     *
     * @return void
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Table) {
            $this->cacheManager->remove('uploads/images/tables/' . $entity->getPicture());
        }
        if ($entity instanceof Avatar) {
            $this->cacheManager->remove('uploads/images/avatars/' . $entity->getPath());
        }
    }
}
