<?php

/*
 * This file is part of askvortsov/flarum-moderator-warnings
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Mattoid\CheckinHistory\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\User\Exception\PermissionDeniedException;
use Mattoid\CheckinHistory\Api\Serializer\CheckinHistorySerializer;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListCheckinHistoryController extends AbstractListController
{
    public $serializer = CheckinHistorySerializer::class;

    public $include = ['warnedUser', 'addedByUser', 'hiddenByUser', 'post', 'post.discussion', 'post.user'];

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @throws PermissionDeniedException
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {

        $actor = RequestUtil::getActor($request);

        if (!$actor->can('event.view')) {
            return array();
        }

        $userId = $request->getAttributes()['actor']['id'];

        return UserCheckinHistory::where('user_id', $userId)->where('last_checkin_date', ">=", $request->getQueryParams()['start'])
            ->where('last_checkin_date', "<=", $request->getQueryParams()['end'])->get();

    }
}
