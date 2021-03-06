<?php

class TestDdXls extends TestDd {

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    (new ProjectQueueWorkerInstaller)->install();
  }

  static function tearDownAfterClass() {
    parent::tearDownAfterClass();
    (new ProjectQueueWorkerInstaller)->uninstall();
  }

  function test() {
    $fm = new DdFieldsManager('a');
    $fm->create([
      'title' => 'Example Title',
      'name'  => 'sampleSttrrr',
      'type' => 'text'
    ]);
    $fm->create([
      'title' => 'Флажок',
      'name'  => 'flagg',
      'type' => 'bool'
    ]);
    $im = DdCore::imDefault('a');
    for ($i=1; $i<=130; $i++) {
      $im->create([
        'sampleSttrrr' => 'stringNumber'.$i,
        'flagg' => Misc::randNum(1) > 5,
      ]);
    }
    $ddo = new Ddo('a', 'xls', ['fieldOptions' => ['getAll' => true]]);
    $ddo->text = true;
    File::delete(UPLOAD_PATH.'/temp/1.xls');
    $im->items->cond->setOrder();
    $lj = new DdXlsFake($im->items->strName, $im->items);
    LongJobCore::state($lj->id())->delete(true);
    LongJobCore::run($lj);
    sleep(3);
    $url = LongJobCore::state($lj->id())->data();
    $this->assertFalse(empty($url));
    output("'$url' formed");
    $file = WEBROOT_PATH.$url;
    $c = file_get_contents($file.'.ids');
    $this->assertTrue($c == ' [130, 129, 128, 127, 126, 125, 124, 123, 122, 121, 120, 119, 118, 117, 116, 115, 114, 113, 112, 111, 110, 109, 108, 107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 87, 86, 85, 84, 83, 82, 81] [80, 79, 78, 77, 76, 75, 74, 73, 72, 71, 70, 69, 68, 67, 66, 65, 64, 63, 62, 61, 60, 59, 58, 57, 56, 55, 54, 53, 52, 51, 50, 49, 48, 47, 46, 45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 32, 31] [30, 29, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1]');
    output('done');
  }

}