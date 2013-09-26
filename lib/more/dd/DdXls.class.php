<?php

class DdXls extends LongJobAbstract {

  protected $items, $ddo, $fileName, $longJobId;

  function __construct($strName, DdItems $items) {
    $this->items = $items;
    Dir::make(UPLOAD_PATH.'/temp/admin/xls');
    $this->ddo = new Ddo($strName, 'xls', ['fieldOptions' => ['getAll' => true]]);
    $this->longJobId = 'i'.$strName.md5($this->items->cond->all());
    $this->ddo->text = true;
    $this->fileName = '/temp/admin/xls/'.$this->items->strName.'_'.date('d-m-Y_H-i-s').'.xls';
    $this->init();
  }

  protected function init() {
  }

  function id() {
    return $this->longJobId;
  }

  protected function getItems() {
    return $this->items->getItems();
  }

  protected function _total() {
    return $this->items->count();
  }

  protected function step() {
    return 5;
  }

  protected function iteration() {
    $this->items->cond->setLimit($this->n.','.$this->step());
    $this->ddo->setItems($this->getItems())->xls(UPLOAD_PATH.$this->fileName, !(bool)$this->n);
  }

  protected function result() {
    return '/'.UPLOAD_DIR.$this->fileName;
  }

}