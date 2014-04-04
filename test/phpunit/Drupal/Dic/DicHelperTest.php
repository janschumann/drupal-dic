<?php

namespace Drupal\Dic;

use Composer\Autoload\ClassLoader;

class DicHelperTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var DicHelper
   */
  private $helper;
  /**
   * @var ClassLoader
   */
  private $classLoader;
  private $rootDir;
  private $fixturesDir;

  public function setUp() {
    $this->classLoader = new ClassLoader();
    $this->fixturesDir = dirname(__FILE__) . '/../../../fixtures';
    $this->rootDir = dirname(__FILE__) . '/../../../data';
  }

  public function tearDown() {
    $this->helper->flushCaches();
  }

  public function testHelperBuildsAnEmptyContainerWithoutBundlesProvided() {
    require_once dirname(__FILE__) . '/../../../fixtures/EmptyContainer.php';
    $expected = new \EmptyContainer();

    $this->helper = new DicHelper(new ProjectKernel('empty', true, $this->rootDir, $this->fixturesDir));
    $container = $this->helper->getContainer();

    $this->assertEquals($expected->getParameter('kernel.bundles'), $container->getParameter('kernel.bundles'));
  }

  public function testBundlesAutoloadInfoIsAddedToClassLoader() {
    $this->helper = new DicHelper(new ProjectKernel('dev', true, $this->rootDir, $this->fixturesDir));
    $this->helper->setClassLoader($this->classLoader);
    $this->helper->setBundleInfo(array(
      'bundles' => array(
        "\\Drupal\\Dic\\Bundle\\DicBundle\\DicBundle"
      ),
      'autoload' => array(
        '\\Drupal\\Dic\\' => array(dirname(__FILE__) . '/../../../../src')
      )
    ));

    $prefixes = $this->classLoader->getPrefixes();

    $this->assertArrayHasKey("\\Drupal\\Dic\\", $prefixes);
  }

  public function testGetContainerCanLoadExampleBundleWithoutAutoloadInfo() {
    $this->helper = new DicHelper(new ProjectKernel('dev', true, $this->rootDir, $this->fixturesDir . '/configs/DicBundle'));
    $this->helper->setBundleInfo(array(
        "\\Drupal\\Dic\\Bundle\\DicBundle\\DicBundle"
    ));

    $container = $this->helper->getContainer();

    $this->assertTrue($container->hasParameter('dic.extension.foo'));
    $this->assertEquals('bar', $container->getParameter('dic.extension.foo'));
    $this->assertTrue($container->hasParameter('example'));
    $this->assertEquals('value', $container->getParameter('example'));
  }
}
