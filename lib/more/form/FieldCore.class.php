<?php

class FieldCore {

  const FIELD_ELEMENT_CLASS_PREFIX = 'FieldE';

  /**
   * @return FieldEAbstract
   */
  static function get($type, array $options = [], Form $form = null) {
    return O::get(self::FIELD_ELEMENT_CLASS_PREFIX.ucfirst($type), $options, $form);
  }

  static function getClass($type) {
    return self::FIELD_ELEMENT_CLASS_PREFIX.ucfirst($type);
  }

  static function isInput($type) {
    return self::hasAncestor($type, 'input');
  }

  static function hasAncestor($type, $ancestorType) {
    return ClassCore::hasAncestor(self::getClass($type), self::getClass($ancestorType));
  }

  static function staticProperty($type, $prop) {
    Misc::checkEmpty($type, '$type');
    $class = FieldCore::getClass($type);
    if (isset($class::$$prop)) return $class::$$prop;
    return false;
  }

}
