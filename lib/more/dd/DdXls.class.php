<?php

class DdXls extends LongJobAbstract {

  protected $items, $ddo, $fileName, $longJobId;

  /**
   * @param string Используется для переопределения класса в O::replaceInjection
   * @param DdItems $items
   */
  function __construct($strName, DdItems $items) {
    $this->items = $items;
    $this->items->isPagination = false; // переопределяем, т.к. объект может быть получен уже сформированным
    Dir::make(UPLOAD_PATH.'/temp/admin/xls');
    $this->ddo = new Ddo($this->items->strName, 'xls', ['fieldOptions' => ['getAll' => true]]);
    $this->longJobId = 'i'.(session_id() ? : 'ddxls');
    $this->ddo->text = true;
    $this->fileName = '/temp/admin/xls/'.$this->items->strName.'_'.date('d-m-Y_H-i-s').'.xls';
    $this->init();
    parent::__construct();
  }

  protected function init() {
  }

  function id() {
    Misc::checkEmpty($this->longJobId, '$this->longJobId');
    return $this->longJobId;
  }

  protected function getItems() {
    //LogWriter::str('ddXlsCond', $this->items->cond->all());
    //LogWriter::str('ddXlsCond', 'count: '.count($this->items->getItemIds()));
    return $this->items->getItems();
  }

  protected function _total() {
    return $this->items->count();
  }

  protected function step() {
    return 30;
  }

  function iteration() {
    $this->output("set limit {$this->n},".$this->step());
    $this->items->cond->setLimit($this->n.','.$this->step());
    $this->ddo->setItems($this->getItems())->xls(UPLOAD_PATH.$this->fileName, !(bool)$this->n);
  }

  protected function result() {
    return '/'.UPLOAD_DIR.$this->fileName;
  }

}