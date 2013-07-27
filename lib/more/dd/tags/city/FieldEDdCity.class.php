<?php

DdFieldCore::registerType('ddCity', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Город',
  'order' => 300,
  'tags' => true,
  'tagsTree' => true
]);

class FieldEDdCity extends FieldEDdTagsConsecutiveSelect {}