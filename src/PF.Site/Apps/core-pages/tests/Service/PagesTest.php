<?php
/**
 * Author: phpFox
 * Date: 31/07/2017
 * Time: 10:41
 */

namespace Apps\Core_Pages\Service;


class PagesTest extends \PHPUnit_Framework_TestCase
{
    public function test_getFacade()
    {
        $obj = new Pages();

        $this->assertSame(true, $obj->getFacade() instanceof Facade);
    }

    public function test_isPage()
    {
        $obj = new Pages();

        $pageId = \Phpfox_Database::instance()->insert(':pages', [
            'type_id' => 0,
            'category_id' => 1,
            'user_id' => 1,
            'title' => 'test',
            'time_stamp' => PHPFOX_TIME,
            'item_type' => 'pages'
        ]);
        \Phpfox_Database::instance()->insert(':pages_url', [
            'page_id' => $pageId,
            'vanity_url' => 'test'
        ]);

        $this->assertSame(true, $obj->isPage('test'));
        $this->assertSame(false, $obj->isPage('test2'));

        \Phpfox_Database::instance()->delete(':pages', ['page_id' => $pageId]);
    }

    public function test_isTimelinePage()
    {
        $obj = new Pages();

        $noUseTimelineId = \Phpfox_Database::instance()->insert(':pages', [
            'type_id' => 0,
            'category_id' => 1,
            'user_id' => 1,
            'title' => 'test',
            'time_stamp' => PHPFOX_TIME,
            'item_type' => 'pages'
        ]);

        $useTimelineId = \Phpfox_Database::instance()->insert(':pages', [
            'type_id' => 0,
            'category_id' => 1,
            'user_id' => 1,
            'title' => 'test',
            'time_stamp' => PHPFOX_TIME,
            'item_type' => 'pages',
            'use_timeline' => 1
        ]);

        $this->assertSame(false, $obj->isTimelinePage(0));
        $this->assertSame(false, $obj->isTimelinePage($noUseTimelineId));
        $this->assertSame(true, $obj->isTimelinePage($useTimelineId));

        \Phpfox_Database::instance()->delete(':pages', ['page_id' => $noUseTimelineId]);
        \Phpfox_Database::instance()->delete(':pages', ['page_id' => $useTimelineId]);
    }

    public function test_getForEditWidget()
    {
        $obj = new Pages();

        $widgetId1 = \Phpfox_Database::instance()->insert(':pages_widget', [
            'page_id' => 0,
            'title' => 'title',
            'is_block' => 1,
            'time_stamp' => time(),
            'user_id' => 1
        ]);
        \Phpfox_Database::instance()->insert(':pages_widget_text', [
            'widget_id' => $widgetId1,
            'text' => 'test',
            'text_parsed' => 'test'
        ]);

        $widgetId2 = \Phpfox_Database::instance()->insert(':pages_widget', [
            'page_id' => 1,
            'title' => 'title',
            'is_block' => 1,
            'time_stamp' => time(),
            'user_id' => 2
        ]);
        \Phpfox_Database::instance()->insert(':pages_widget_text', [
            'widget_id' => $widgetId2,
            'text' => 'test',
            'text_parsed' => 'test'
        ]);

        $widgetId3 = \Phpfox_Database::instance()->insert(':pages_widget', [
            'page_id' => 1,
            'title' => 'title',
            'is_block' => 1,
            'time_stamp' => time(),
            'user_id' => 1
        ]);
        \Phpfox_Database::instance()->insert(':pages_widget_text', [
            'widget_id' => $widgetId3,
            'text' => 'test',
            'text_parsed' => 'test'
        ]);

        $this->assertSame(false, $obj->getForEditWidget(0));
        $this->assertSame(false, $obj->getForEditWidget($widgetId1));
        $this->assertSame(false, $obj->getForEditWidget($widgetId2));
        $this->assertSame(true, $obj->getForEditWidget($widgetId3));

        \Phpfox_Database::instance()->delete(':pages_widget', ['widget_id' => $widgetId1]);
        \Phpfox_Database::instance()->delete(':pages_widget_text', ['widget_id' => $widgetId1]);
        \Phpfox_Database::instance()->delete(':pages_widget', ['widget_id' => $widgetId2]);
        \Phpfox_Database::instance()->delete(':pages_widget_text', ['widget_id' => $widgetId2]);
        \Phpfox_Database::instance()->delete(':pages_widget', ['widget_id' => $widgetId3]);
        \Phpfox_Database::instance()->delete(':pages_widget_text', ['widget_id' => $widgetId3]);
    }

    public function test()
    {
    }


}
