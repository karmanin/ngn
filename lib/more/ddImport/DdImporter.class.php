<?php

class DdImporter {
  
  /**
   * @var DdItemsManagerPage
   */
  protected $im;
  
  /**
   * @var DdImportDataReceiver
   */
  protected $oReceiver;
  
  function __construct(DdItemsManagerPage $im, DdImportDataReceiver $oReceiver) {
    $this->im = $im;
    $this->oReceiver = $oReceiver;
  }
  
  function makeImport() {
    $oF = $this->oReceiver->getFieldObj();
    $fieldTypes = $oF->getTypes();    
    foreach ($this->oReceiver->getData() as $v) {
      $method = 'f_'.$fieldType;
      $formatterExists = method_exists($this, $method);
      $_v = $v;
      foreach (array_keys($v) as $fieldName) {
        $value = trim($v[$fieldName]);
        if (empty($value)) continue;
        $fieldType = $fieldTypes[$fieldName];
        $method = 'f_'.$fieldType;
        // Если есть форматтер для этого типа, очищаем поле
        if (method_exists($this, $method))
          $v[$fieldName] = '';
      }      
      $itemId = $this->im->create($v);
      foreach (array_keys($_v) as $fieldName) {
        $value = trim($_v[$fieldName]);
        if (empty($value)) continue;
        $fieldType = $fieldTypes[$fieldName];
        $method = 'f_'.$fieldType;
        if (method_exists($this, $method))
          $this->$method($value, $fieldName, $itemId);
      }
    }
    die2('Import complete');
  }
  
  protected function tagsTreeSelectTagsCreate($value, $fieldName) {
    $oTags = DdTags::get($this->im->items->strName, $fieldName);
    $tags = array_map('trim', explode('→', $value));
    $parentId = 0;
    foreach ($tags as $tag) {
      $parentId = $oTags->create($tag, $parentId);
      $tagIds[] = $parentId;
    }
    return $tagIds;
  }
  
  protected function f_tagsTreeSelect($value, $fieldName, $itemId) {
    $tagIds = $this->tagsTreeSelectTagsCreate($value, $fieldName);
    //DdTags::items($this->dm->strName, $fieldName)->createByIds($itemId);
    DdTagsItems::createByIds(
      $this->im->items->strName,
      $fieldName,
      $itemId,
      $tagIds
    );
    $this->im->items->update($itemId, [
      $fieldName => $tagIds[count($tagIds)-1]
    ]);
  }
  
  protected function tagsTreeMultiselectTagsCreate($value, $fieldName) {
    $oTags = DdTags::get($this->im->items->strName, $fieldName);
    $value = array_map('trim', explode(";", $value));
    foreach ($value as $n => $tags) {
      $tags = array_map('trim', explode('→', $tags));
      $parentId = 0;
      foreach ($tags as $tag) {
        $parentId = $oTags->create($tag, $parentId);
        $collections[$n][] = $parentId;
      }
    }
    return $collections;
  }
  
  protected function f_tagsTreeMultiselect($value, $fieldName, $itemId) {
    $collectionTagIds = $this->tagsTreeMultiselectTagsCreate($value, $fieldName);
    DdTagsItems::createByIdsCollection(
      $this->im->items->strName,
      $fieldName,
      $itemId,
      $collectionTagIds
    );
    $tagIds = [];
    foreach ($collectionTagIds as $_tagIds)
      $tagIds = Arr::append($tagIds, $_tagIds);
    $this->im->items->update($itemId, [
      $fieldName => serialize($tagIds)
    ]);
  }
  
  protected function f_tagsSelect($value, $fieldName, $itemId) {
    DdTagsItems::createByIds(
      $this->im->items->strName,
      $fieldName,
      $itemId,
      [DdTags::get($this->im->items->strName, $fieldName)->create($value)]
    );
  }
  
  protected function f_tagsMultiselect() {
  }
  
  // ----------------------------------
  
  static function import($pageId, $file) {
    $strName = db()->selectCell('SELECT strName FROM pages WHERE id=?d', $pageId);
    $oI = new DdItemsPage($pageId);
    $oI->forceDublicateInsertCheck = true;
    $o = new DdImporter(
      new DdItemsManagerPage(
        $oI,
        new DdFormPage(new DdFields($strName), $pageId)
      ),
      new DdImportDataExcel(
        new DdImportField($strName),
        $file
      )
    );
    $o->makeImport();
  }
  
}
