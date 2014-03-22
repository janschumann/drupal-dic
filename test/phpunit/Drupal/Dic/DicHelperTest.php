<?php
/**
 * Created by IntelliJ IDEA.
 * User: jan.schumann
 * Date: 21.03.14
 * Time: 21:56
 */

namespace Drupal\Dic;

use Symfony\Component\ClassLoader\ClassLoader;

class DicHelperTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var DicHelper
   */
  private $helper;
  /**
   * @var ClassLoader
   */
  private $classLoader;

  public function setUp() {
    $this->helper = DicHelper::getInstance();
  }

  public function tearDown() {
    $this->helper->flushCaches();
  }

  private function initHelper($configDir, $env = 'dev', $debug = true) {
    $this->classLoader = new ClassLoader();
    $rootDir = dirname(__FILE__) . '/../../../data';
    $environment = $env;

    $this->helper->initialize($this->classLoader, $rootDir, $environment, $debug, $configDir);
  }

  public function testGetContainerCanLoadExampleBundle() {
    $this->initHelper(dirname(__FILE__) . '/../../../fixtures/configs/DicBundle');
    $this->helper->setBundleInfo(array(
      'bundles' => array(
        "\\Drupal\\Dic\\Bundle\\DicBundle\\DicBundle"
      ),
      'autoload' => array(
        '\\Drupal\\Dic\\' => array(dirname(__FILE__) . '/../../../../src')
      )
    ));

    $container = $this->helper->getContainer();

    $this->assertTrue($container->hasParameter('dic.extension.foo'));
    $this->assertEquals('bar', $container->getParameter('dic.extension.foo'));
    $this->assertTrue($container->hasParameter('example'));
    $this->assertEquals('value', $container->getParameter('example'));
  }

  public function testGetContainerAfterInitializeWillBuildAnEmptyContainer() {
    $this->initHelper(dirname(__FILE__) . '/../../../fixtures', 'empty');

    $container = $this->helper->getContainer();

    require_once dirname(__FILE__) . '/../../../fixtures/EmptyContainer.php';
    $expected = new \EmptyContainer();

    $this->assertEquals($expected->getParameterBag()->all(), $container->getParameterBag()->all());
  }

  public function testBundlesAutoloadInfoIsAddedToClassLoader() {
    $this->initHelper(dirname(__FILE__) . '/../../../fixtures');
    $this->helper->setBundleInfo(array(
      'bundles' => array(
        "\\Drupal\\Example\\Bundle\\ExampleBundle\\ExampleBundle"
      ),
      'autoload' => array(
        '\\Drupal\\Example\\' => array(dirname(__FILE__) . '/../../../../src')
      )
    ));

    $prefixes = $this->classLoader->getPrefixes();

    $this->assertArrayHasKey("\\Drupal\\Example\\", $prefixes);
  }
}
