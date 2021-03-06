<?php

class TestFieldDdItemSelect extends TestFieldDdItemSelectAbstract {

  protected $itemId1, $itemId2;

  function createTags() {
    $this->itemId1 = static::$im2->create(['title' => $this->v1]);
    $this->itemId2 = static::$im2->create(['title' => $this->v2]);
  }

  function createData() {
    return ['sample' => $this->itemId1];
  }

  function runTests($request = false) {
    $this->a($this->itemId1, $this->v1);
    $this->updateItem(['sample' => $this->itemId2], $request);
    $this->a($this->itemId2, $this->v2);
  }

  function a($tagId, $v) {
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample']['title'] == $v);
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), '<option value="'.$tagId.'" selected>'.$v.'</option>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), ">$v</div>"));
  }

}
