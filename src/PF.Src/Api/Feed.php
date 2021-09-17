<?php

namespace Api;

use Phpfox;
use Phpfox_Component;

class Feed extends \Core\Api
{

    public function delete($id, $is_page = false, $app_id = null)
    {
        $this->auth();

        if ($is_page) {
            $this->db->tableExists(Phpfox::getT('pages_feed')) && $this->db->delete(':pages_feed', ['feed_id' => $id]);
            if ($app_id !== null) {
                $this->db->delete(':feed', ['type_id' => $app_id, 'parent_feed_id' => $id]);
            }

        } else {
            $this->db->delete(':feed', ['feed_id' => $id]);
        }
    }

    public function put($id, $put = null, $is_callback = false)
    {
        if ($put !== null) {
            $this->assign($put);
        }

        $this->requires([
            'content',
        ]);

        $old = $this->get($id, null, $is_callback);
        $oldContent = (array)$old->content;

        $privacy = 0;
        $content = $this->request->get('content');
        if (!is_string($content)) {
            $content = json_encode(array_merge($oldContent, (array)$content), JSON_UNESCAPED_UNICODE);
        }

        if ($this->request->get('privacy')) {
            $privacy = (int)$this->request->get('privacy');
        }

        $table = ':feed';
        if ($is_callback) {
            $table = ':pages_feed';
        }
        $this->db->update($table, ['content' => $content, 'privacy' => $privacy], ['feed_id' => $id]);

        return $this->get($id, null, $is_callback);
    }

    /**
     * @param array $post
     *
     * @return Feed\Object
     */
    public function post($post = [])
    {
        if ($post) {
            $this->assign($post);
        }
        $this->auth();
        $this->requires([
            'type_id',
            'content',
        ]);

        if (!$this->request->get('content')) {
            throw error(_p('Add some content.'));
        }

        $tags = [];
        $content = $this->request->get('content');
        if (!is_string($content)) {
            if (is_array($content)) {
                foreach ($content as $key => $value) {
                    if (is_object($value) && $value instanceof \Core\Text\Parse) {
                        $tags = $value->tags();
                        $content[$key] = $value->text();
                    }
                }
            }

            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }

        $fields = [
            'type_id' => $this->request->get('type_id'),
            'content' => $content,
            'privacy' => $this->request->get('privacy'),
        ];

        $is_callback = false;

        if ($this->request->get('module_id')) {
            $is_callback = true;
            Phpfox::getService('feed.process')->callback([
                'module'           => $this->request->get('module_id'),
                'table_prefix'     => 'pages_',
                'item_id'          => $this->request->get('module_item_id'),
                'has_content'      => true,
                'add_to_main_feed' => true,
            ]);


            $fields['parent_user_id'] = Phpfox::getService('user.auth')->getUserId();
            $fields['item_id'] = $this->request->get('module_item_id');

            if (!defined('PHPFOX_PAGES_IS_PARENT_FEED')) {
                define('PHPFOX_PAGES_IS_PARENT_FEED', true);
            }
        }
        $feedId = Phpfox::getService('feed.process')->add($fields);

        $feed = $this->get($feedId, null, $is_callback);
        if (count($tags)) {
            Phpfox::getService('tag.process')->add($feed->app, $feed->id, user()->id, $tags);
            db()->update(':feed', ['item_id' => $feedId], ['feed_id' => $feedId]);
        }

        return $feed;
    }

    /**
     * @param null $params
     * @param null $app_id
     * @param bool $is_callback
     *
     * @return Feed\Object|Feed\Object[]
     * @throws \Exception
     */
    public function get($params = null, $app_id = null, $is_callback = false)
    {
        $feeds = [];
        $isSingle = false;
        if (is_numeric($params)) {
            $id = (int)$params;

            $params = [];
            $params['id'] = $id;
            $isSingle = true;
        } else {
            if (is_string($params)) {
                $type = $params;
                $params = [];
                $params['type_id'] = $type;
            }
        }

        if (!empty($params['isSingle'])) {
            $isSingle = $params['isSingle'];
        }

        if (isset($_GET['page'])) {
            $params['page'] = $_GET['page'];
        }

        if (isset($_GET['last-item'])) {
            $params['last-item'] = $_GET['last-item'];
            unset($params['page']);
        }

        if (isset($_GET['limit'])) {
            $params['limit'] = $_GET['limit'];
        }

        if (isset($_GET['when'])) {
            $params['when'] = $_GET['when'];
        }

        if (isset($_GET['friends'])) {
            $params['friends'] = $_GET['friends'];
        }

        if (is_array($params)) {
            $params['is_api'] = true;
        }

        if ($is_callback && !empty($params['id'])) {
            $callback = storage()->get('feed_callback_' . $params['id']);

            if (isset($callback->value)) {
                Phpfox::getService('feed')->callback((array)$callback->value);
            }
        }
        $rows = Phpfox::getService('feed')->get($params);

        foreach ($rows as $row) {
            $object = [
                'id'             => (int)$row['feed_id'],
                'app'            => $row['type_id'],
                'content'        => $row['content'],
                'privacy'        => (int)$row['privacy'],
                'total_likes'    => (int)$row['feed_total_like'],
                'total_comments' => (int)(isset($row['total_comment']) ? $row['total_comment'] : 0),
                'total_view'     => (int)$row['total_view'],
                'user'           => $row,
            ];

            if ($row['type_id'] != 'app') {
                $object['is_app'] = true;
                $object['custom'] = [
                    'item_id'      => $row['item_id'],
                    'url'          => $row['feed_link'],
                    'external_url' => (isset($row['feed_link_actual']) ? $row['feed_link_actual'] : ''),
                    'title'        => $row['feed_title'],
                    'description'  => (isset($row['feed_content']) ? $row['feed_content'] : null),
                    'time_stamp'   => $row['feed_time_stamp'],
                    'image'        => (isset($row['feed_image']) ? $row['feed_image'] : null),
                    'type'         => $row['type_id'],
                    'privacy'      => $row['privacy'],
                    'likes'        => $row['feed_total_like'],
                    'is_liked'     => $row['feed_is_liked'],
                    'comments'     => (isset($row['total_comment']) ? $row['total_comment'] : 0),
                ];
            }

            $feeds[] = new Feed\Object($object);
        }

        if ($isSingle) {
            if (!isset($feeds[0])) {
                throw new \Exception('Unable to find this feed.');
            }

            $feed = $feeds[0];
            if (isset($callback) && isset($callback->value)) {
                $feed->module_id = $callback->value->module;
                $feed->module_item_id = $callback->value->item_id;
            }

            if (!$this->isApi() && $feed instanceof Feed\Object) {
                $app_object = null;
                if ($app_id !== null) {
                    $app_object = app($app_id);
                }

                $aFeed = [
                    'app_object'      => (isset($app_object->id) ? $app_object->id : null),
                    'is_app'          => $feed->is_app,
                    'comment_type_id' => 'app',
                    'privacy'         => $feed->custom->privacy,
                    'comment_privacy' => $feed->custom->privacy,
                    'like_type_id'    => 'app',
                    'feed_is_liked'   => $feed->custom->is_liked,
                    'item_id'         => $feed->custom->item_id,
                    'user_id'         => $feed->user->id,
                    'total_comment'   => $feed->custom->comments,
                    'total_like'      => $feed->custom->likes,
                    'feed_link'       => $feed->custom->url,
                    'feed_title'      => $feed->custom->title,
                    'feed_display'    => 'view',
                    'feed_total_like' => $feed->custom->likes,
                    'report_module'   => $feed->custom->type,
                    'report_phrase'   => _p('Report'),
                    'time_stamp'      => $feed->custom->time_stamp,
                ];
                if (isset($params['feed_table_prefix'])) {
                    $aFeed['feed_table_prefix'] = $params['feed_table_prefix'];
                }
                if (isset($params['share_type_id'])) {
                    $aFeed['share_type_id'] = $params['share_type_id'];
                }

                Phpfox_Component::setPublicParam('aFeed', $aFeed);
            }

            return $feed;
        }

        return $feeds;
    }
}