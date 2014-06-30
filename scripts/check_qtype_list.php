<?php

$resolver = Drupal::service('controller_resolver');
$callable = $resolver->getControllerFromDefinition('Drupal\Core\Entity\Controller\EntityListController::listing');
$result = $callable('chgk_question_type');
print $result['#type']."\n";
print $result['#header']['title']."\n";
print $result['#rows']['chgk']['title']."\n";
