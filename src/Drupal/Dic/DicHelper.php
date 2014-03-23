<?php

namespace Drupal\Dic;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\ClassLoader\ClassLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This encapsulates the container construction from drupal
 * and takes care of autoloading bundles to provide to the kernel
 */
class DicHelper {

  /**
   * @var ProjectKernel
   */
  private $kernel;
  /**
   * @var ClassLoader
   */
  private $classLoader = null;
  /**
   * @var boolean
   */
  private $bundlesBooted = true;

  /**
   * Constructor.
   *
   * @param ProjectKernel $kernel
   */
  public function __construct(ProjectKernel $kernel) {
    $this->setKernel($kernel);
  }

  /**
   * Set a new kernel
   *
   * @param ProjectKernel $kernel
   */
  public function setKernel(ProjectKernel $kernel) {
    $this->kernel = $kernel;
  }

  /**
   * Set an optional class loader for autoloading bundles
   *
   * @param ClassLoader $classLoader
   */
  public function setClassLoader(ClassLoader $classLoader) {
    $this->classLoader = $classLoader;
  }

  /**
   * Set bundle info
   *
   * @param array $bundleInfo
   */
  public function setBundleInfo(array $bundleInfo) {
    $this->kernel->setDrupalBundles($this->getAutoloadedBundles($bundleInfo));
    $this->bundlesBooted = false;
  }

  /**
   * Retrieves the container
   *
   * @return ContainerInterface
   */
  public function getContainer() {
    // kernel will only boot if necessary
    $this->kernel->boot();

    // boot bundles if necessary
    if (!$this->bundlesBooted) {
      foreach ($this->kernel->getBundles() as $bundle) {
        $bundle->boot();
      }
      $this->bundlesBooted = true;
    }

    return $this->kernel->getContainer();
  }

  /**
   * Cleanup cache files
   */
  public function flushCaches($all = false) {
    $dir = $this->kernel->getCacheDir();
    if ($all) {
      $dir .= '/..';
    }
    $fs = new Filesystem();
    $fs->remove(realpath($dir));
  }

  private function registerNamespaces($map) {
    // changes in api of Classloader between 2.0.x and 2.2.x
    if (method_exists($this->classLoader, 'addPrefixes')) {
      $this->classLoader->addPrefixes($map);
    } else {
      $this->classLoader->registerNamespaces($map);
    }
  }

  private function getAutoloadedBundles(array $bundleInfo) {
    if (isset($bundleInfo['bundles']) && is_array($bundleInfo['bundles'])) {
      $bundles = $bundleInfo['bundles'];

      // initialize autoloading if necessary
      if (isset($bundleInfo['autoload']) && is_array($bundleInfo['autoload'])) {
        if (is_null($this->classLoader)) {
          throw new \RuntimeException("Cannot autoload classes. Plese provide a ClassLoader. See setClassLoader().");
        }
        $this->registerNamespaces($bundleInfo['autoload']);
      }
    }
    else {
      $bundles = $bundleInfo;
    }

    return $bundles;
  }
}
