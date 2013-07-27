<? if ($d['items']) { ?>
<script type="text/javascript">
window.addEvent('domready', function(){

  Ngn.cp.ddItems = new Ngn.Items({
    isSorting: <?= $d['page']['settings']['order'] == 'oid' ? 'true' : 'false' ?>,
    itemsLayout: 'tile',
    idParam: 'itemId'
  });
  
  new Ngn.cp.DdItemsGroup($('items'));

  (function(){
    Ngn.equalItemHeights($$('.layoutMode_tile .items .item'))
  }).delay(100);
  
  abbreviate($$('.items .item h2'), 13);
  
});
</script>

<style>
.items .tools {
clear: both;
}
</style>

<?= $d['oDdo']->itemsBegin() ?>

<form method="post" id="itemsForm" method="post">
<div class="moduleBody module_<?= $d['page']['module'] ?> layoutMode_<?= $d['page']['settings']['ddItemsLayout'] ?>">
  <? $this->tpl('admin/common/pnums', $d) ?>
  <div class="items" id="items">
  <? $n=0; foreach ($d['items'] as $v) { ?>
    <div class="item <?= $f['name'] ?><?= $v['active'] ? '' : ' nonActive' ?>" id="<?= 'item_'.$v['id'].'_'.$v['oid'] ?>">
      <!-- Начало цикла вывода полей -->
      <? foreach ($d['oDdo']->fields as $f) { ?>
        <?= $d['oDdo']->el($v[$f['name']], $f['name'], $v['id']) ?>
      <? } ?>
      <!-- Конец цикла вывода полей -->
      <div class="tools loader">
        <a class="iconBtn edit" title="<?= LANG_EDIT ?>"
          href="<?= $this->getPath() ?>?a=edit&itemId=<?= $v['id'] ?>"><i></i></a>
        <a class="iconBtn delete" title="<?= LANG_DELETE ?>"
          href="<?= $this->getPath(4) ?>?a=delete&itemId=<?= $v['id'] ?>"><i></i></a>
        <a class="iconBtn <?= $v['active'] ? 'activate' : 'deactivate' ?>" title="<?= $v['active'] ? LANG_HIDE : LANG_SHOW ?>"
          href="<?= $this->getPath(4) ?>?a=<?= $v['active'] ? 'deactivate' : 'activate' ?>&itemId=<?= $v['id'] ?>"><i></i></a>
        <? if (!$d['page']['settings']['forbidItemPage']) { ?>
        <a class="iconBtn link" target="_blank" title="<?= LANG_OPEN_ENTRY_PAGE_ON_SITE ?>"
          href="/<?= $v['pagePath'].'/'.$v['id'] ?>"><i></i></a>
        <? } ?>
        <?php /*
        <? if ($d['canMove']) { ?>
          <a class="iconBtn move" title="<?= LANG_MOVE ?>"
            href="<?= $this->getPath() ?>?a=moveForm&itemId=<?= $v['id'] ?>"><i></i></a>
        <? } ?>
        */?>
        <input type="checkbox" name="itemIds[<?= $n ?>]" value="<?= $v['id'] ?>" />
        <div class="clear"><!-- --></div>
      </div>
    </div>
  <? $n++; } ?>
  <div class="clear"><!-- --></div>
  </div>
  <? $this->tpl('admin/common/pnums', $d) ?>
</div>
</form>

<?= $d['oDdo']->itemsEnd() ?>

<? } else { ?>
  <p class="info"><i></i><?= LANG_NO_ENTRIES ?></p>
<? } ?>