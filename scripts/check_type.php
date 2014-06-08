<?php

$definition = \Drupal::entityManager()->getDefinition('chgk_question_type');
print $definition->get('label')."\n";

$type= \Drupal::service('config.typed')->
  get('chgk.question_type.chgk');
print $type->get('id')->getString()."\n";
print $type->get('label')->getString()."\n";
print $type->get('description')->getString()."\n";
