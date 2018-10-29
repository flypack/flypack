<?php

/**
 * RenderTest
 */

namespace fly\tests;

use PHPUnit\Framework\TestCase;
use fly\Render;

class RenderTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testSimple()
    {

        Render::setViewsDir(__DIR__ . '/samples/render');

        ob_start();
        Render::View('view1');
        $this->assertEquals('VIEW1', ob_get_clean());

        ob_start();
        Render::View('view1');
        Render::View('views_folder/view2');
        $this->assertEquals('VIEW1VIEW2_(NO)', ob_get_clean());

        ob_start();
        Render::View('view1');
        Render::View('views_folder/view3');
        $this->assertEquals('VIEW1VIEW3_(NO)', ob_get_clean());
    }

    /**
     * @throws \Exception
     */
    public function testWithVars()
    {

        Render::setViewsDir(__DIR__ . '/samples/render');

        ob_start();
        Render::View('view1', ['VIEW_VAR' => 1]);
        $this->assertEquals('VIEW1', ob_get_clean());

        ob_start();
        Render::View('view1');
        Render::View('views_folder/view2', ['VIEW_VAR' => 'value']);
        $this->assertEquals('VIEW1VIEW2_(value)', ob_get_clean());

        ob_start();
        Render::View('view1');
        Render::View('views_folder/view3', ['VIEW_VAR' => 'val']);
        $this->assertEquals('VIEW1VIEW3_(NO)', ob_get_clean());

        ob_start();
        Render::View('view1');
        Render::View('views_folder/view2', ['VIEW_VAR' => 123456]);
        Render::View('views_folder/view3', ['VIEW_VAR_TEST' => 'val']);
        $this->assertEquals('VIEW1VIEW2_(123456)VIEW3_(val)', ob_get_clean());
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Render: No view file exists
     *
     * @throws \Exception
     */
    public function testExceptionOnInvalidConfig()
    {
        Render::setViewsDir(__DIR__ . '/samples/render');
        Render::View('view2');
    }

}
