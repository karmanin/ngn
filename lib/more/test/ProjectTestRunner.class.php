<?php

class ProjectTestRunner extends TestRunnerAbstract {

  protected $project;

  function __construct($filterNames = null) {
    $this->project = PROJECT_KEY;
    parent::__construct($filterNames);
  }

  protected function run() {
    PHPUnit_TextUI_TestRunner::run($this->suite, [
        'printer' => new ProjectTestPrinter($this->project)
      ]
    );
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function($class) {
      $r = ClassCore::hasAncestor($class, 'ProjectTestCase');
      return $r;
    });
  }

  function _local($checkErrors = true) {
    if ($checkErrors) (new Errors)->clear();
    $classes = array_filter($this->getClasses(), function($class) {
      return strstr(Lib::getClassPath($class), "projects/$this->project/") or !empty($class::$local);
    });
    if ($checkErrors) $classes[] = 'TestProjectAllErrors';
    $this->_run($classes);
  }

  function _global() {
    $this->_run(array_filter($this->getClasses(), function($class) {
      return !strstr(Lib::getClassPath($class), "projects/$this->project/");
    }));
  }

  function _all() {
    $this->_run($this->getClasses());
  }

}