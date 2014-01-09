<?php

/**
 * @file
 * Callbacks for adding, editing, and deleting questions 
 */


/**
 * Returns HTML for a list of available question types for question creation.
 *
 * @param $variables
 *   An associative array containing:
 *   - content: An array of question types.
 *
 * @see chgk_question_add_page()
 *
 * @ingroup themeable
 */
function theme_chgk_question_add_list($variables) {
  $content = $variables['content'];

  if ($content) {
    $output = '<dl class="question-type-list">';
    foreach ($content as $type) {
      $output .= '<dt>' . l($type->label, 'question/add/' . $type->id) . '</dt>';
      $output .= '<dd>' . filter_xss_admin($type->description) . '</dd>';
    }
    $output .= '</dl>';
  }
  else {
    $output = '<p>' . t('You have not created any question types yet. Go to the <a href="@create-question-type">question type creation page</a> to add a new question type.', array('@create-question-type' => url('admin/structure/question-types/add'))) . '</p>';
  }
  return $output;
}
