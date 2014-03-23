<?php
/**
 * Created by IntelliJ IDEA.
 * User: jan.schumann
 * Date: 21.03.14
 * Time: 16:46
 */

namespace Drupal\Dic;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\ClassLoader\ClassLoader;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Singleton helper class
 * This encapsulates the container construction from drupal.
 * It is initialized with some variables from drupal but it does
 * not interact with drupal in any way
 */
class DicHelper {

  private static $instance;

  /**
   * @var boolean
   */
  private $initialized = false;
  /**
   * @var ClassLoader
   */
  private $classLoader;
  /**
   * @var string
   */
  private $rootDir;
  /**
   * @var string
   */
  private $environment;
  /**
   * @var boolean
   */
  private $debug;
  /**
   * @var string
   */
  private $configDir;
  /**
   * @var array
   */
  private $bundleInfo;
  /**
   * @var array
   */
  private $booted = false;
  /**
   * @var ProjectKernel
   */
  private $kernel;

  /**
   * this is a singleton class
   */
  private function __construct() { }
  private function __clone() {}

  /**
   * Creates the instance
   *
   * @return DicHelper
   */
  public static function getInstance() {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Populates the helper with information from drupal
   *
   * @param ClassLoader $classLoader  The class autoloader
   * @param string      $rootDir      Usualy the Drupal Root dir
   * @param string      $environment  The envidonment (prod/dev)
   * @param boolean     $debug        If we should boot in debug mode
   * @param string      $configDir    Where to find the settings.xml file
   * @param array       $bundleInfo   An array of bundles @see hook_dic_bundle_info()
   */
  public function initialize(ClassLoader $classLoader, $rootDir, $environment, $debug, $configDir) {
    $this->classLoader = $classLoader;
    $this->rootDir = $rootDir;
    $this->environment = $environment;
    $this->debug = $debug;
    $this->configDir = $configDir;

    $this->kernel = new ProjectKernel($this->environment, $this->debug, $this->rootDir, $this->configDir);

    $this->initialized = true;
  }

  /**
   * Reset this instance
   */
  public static function reset() {
    self::$instance = null;
  }

  /**
   * Checks if the helper is initialized
   *
   * @return bool
   */
  public function isInitialized() {
    return $this->initialized;
  }

  /**
   * Set a fresh set of bundles. This will cause the kernel to (re)-boot
   * om next getContainer()
   *
   * @param array $bundleInfo
   */
  public function setBundleInfo(array $bundleInfo) {
    if (!$this->isInitialized()) {
      throw new \RuntimeException('Please call initialize() before setBundleInfo()');
    }

    if ($this->bundleInfo !== $bundleInfo) {
      $this->bundleInfo = $bundleInfo;
      $this->kernel->setDrupalBundles($this->getAutoloadedBundles());
      // if the kernel already been booted, make sure a fresh container will be build
      if ($this->booted) {
        $this->flushCaches();
      }

      $this->booted = false;
    }
  }

  /**
   * Retrieves the container
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   */
  public function getContainer() {
    if (!$this->isInitialized()) {
      throw new \RuntimeException('Please call initialize() before getContainer()');
    }

    $this->boot();

    return $this->kernel->getContainer();
  }

  /**
   * Cleanup cache files
   */
  public function flushCaches() {
    if (!$this->isInitialized()) {
      throw new \RuntimeException('Please call initialize() before flushCaches()');
    }

    $fs = new Filesystem();
    $fs->remove($this->kernel->getCacheDir());
  }

  /**
   * Boot kernel and bundles if necessary
   */
  private function boot() {
    if ($this->isInitialized()) {
      // kernel will only boot if necessary
      $this->kernel->boot();

      // boot bundles if necessary
      if (!$this->booted) {
        foreach ($this->kernel->getBundles() as $bundle) {
          $bundle->boot();
        }
      }
      $this->booted = true;
    }
  }

  private function registerNamespaces($map = array()) {
    // changes in api of Classloader between 2.0.x and 2.2.x
    if (method_exists($this->classLoader, 'addPrefixes')) {
      $this->classLoader->addPrefixes($map);
    } else {
      $this->classLoader->registerNamespaces($map);
    }
  }

  private function getAutoloadedBundles() {
    $bundles = array();
    if (isset($this->bundleInfo['bundles']) && is_array($this->bundleInfo['bundles'])) {
      $bundles = $this->bundleInfo['bundles'];

      // initialize autoloading if necessary
      if (isset($this->bundleInfo['autoload']) && is_array($this->bundleInfo['autoload'])) {
        $this->registerNamespaces($this->bundleInfo['autoload']);
      }
    }

    return $bundles;
  }
}
