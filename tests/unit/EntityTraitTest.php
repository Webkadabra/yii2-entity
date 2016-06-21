<?php

namespace DevGroup\Entity\tests;

use tests\models\Page;

class EntityTraitTest extends \yii\codeception\TestCase
{
    public $appConfig = '@tests/config/unit.php';

    public function setUp()
    {
        parent::setUp();
        \Yii::$app->runAction('migrate/up', ['migrationPath' => '@tests/migrations', 'interactive' => 0]);
    }

    public function tearDown()
    {
        \Yii::$app->runAction('migrate/down', ['999999', 'migrationPath' => '@tests/migrations', 'interactive' => 0]);
        parent::tearDown();
    }

    public function testRules()
    {
        $page = new Page;
        // a model rule check
        $this->assertFalse($page->validate());
        $page->slug = 'test-page';
        $this->assertTrue($page->save());
        // a seo trait title check
        $page->title = str_repeat('A', 256);
        $this->assertFalse($page->validate());
        $page->title = str_repeat('A', 255);
        $this->assertTrue($page->validate());
    }

    public function testTimestamp()
    {
        $page = new Page;
        $page->slug = 'timestamp';
        $page->save();
        $this->assertNotNull($page->created_at);
        $this->assertNotNull($page->updated_at);
        $this->assertSame($page->created_at, $page->updated_at);
        sleep(1);
        $page->slug = 'testpage';
        $page->save();
        $this->assertTrue($page->created_at < $page->updated_at);
    }

    public function testBlameable()
    {
        \Yii::$app->user->setId(5);
        $page = new Page;
        $page->slug = 'blameable-';
        $page->save();
        $this->assertSame(5, $page->created_by);
        $this->assertSame(5, $page->updated_by);
        \Yii::$app->user->setId(null);
        $page->slug = 'blameable';
        $page->save();
        $this->assertSame(5, $page->created_by);
        $this->assertNull($page->updated_by);
    }

    public function testSlugRule()
    {
        $page = new Page;
        // test regexp
        $page->slug = '';
        $this->assertFalse($page->validate());
        $page->slug = '/test-page';
        $this->assertFalse($page->validate());
        $page->slug = 'test page';
        $this->assertFalse($page->validate());
        $page->slug = 'тест';
        $this->assertFalse($page->validate());
        // test max length
        $page->slug = str_repeat('Z', 81);
        $this->assertFalse($page->validate());
        // test some valid slugs
        $page->slug = 'valid-slug';
        $this->assertTrue($page->validate());
        $page->slug = 'valid_slug';
        $this->assertTrue($page->validate());
        $page->slug = 'validSlug123';
        $this->assertTrue($page->validate());
    }

    public function testTranslations()
    {
        $page = new Page;
        // test a default
        $this->assertSame('Page url', $page->getAttributeLabel('url'));
        // test merged labels
        $this->assertSame(6, count($page->attributeLabels()));
        // test a ru translation
        $this->assertSame('Заголовок', $page->getAttributeLabel('title'));
    }
}