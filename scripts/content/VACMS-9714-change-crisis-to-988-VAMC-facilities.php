<?php

/**
 * @file
 * Replace 800-273-8255 with 988
 *
 * VACMS-8935-scrub-tz-in-hours-field-comment.php.
 */

use Psr\Log\LogLevel;

$ops_status_total_pattern = "(\<a.*\>)?(1[\-\.])?800[\-\.]273[\-\.]8255(\<\/a\>)?";
$ops_status_pattern = "/(\<a.*\>)?(1[\-\.])?800[\-\.]273[\-\.]8255(\<\/a\>)?/i";
$replacement_string = '<a aria-label="9 8 8" href="tel:988">988</a>';


$sandbox = ['#finished' => 0];
do {
  print(va_gov_change_crisis_hotline_to_988_nodes($sandbox, $ops_status_total_pattern, $ops_status_pattern, $replacement_string));
} while ($sandbox['#finished'] < 1);
// Node processing complete.  Call this done.
return;

/**
 * Remove timezones from hours field comments.
 *
 * @param array $sandbox
 *   Modeling the structure of hook_update_n sandbox.
 *
 * @return string
 *   Status message.
 */
function va_gov_change_crisis_hotline_to_988_nodes(array &$sandbox, $pattern_string, $pattern_regex, $replacement_string)
{
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');

  // Get the node count for nodes with timezones in hours comments.
  // This runs only once.
  if (!isset($sandbox['total'])) {
    // Hours field is attached to service_locations in all places,
    // save for facilities.
    // Facilities db search returned 0 results for timezones in comments.

    // $paragraph_query = Drupal::database()->select('node__field_content_block', 'nfcb');
    $paragraph_query = Drupal::database()->select('paragraph__field_wysiwyg', 'wysiwyg');
    $paragraph_query->join('node__field_content_block', 'nfcb', 'wysiwyg.entity_id = nfcb.field_content_block_target_id');

    // and node_field_data.nid = nfcb.entity_id and node_field_data.type like "%health_care_region_detail_page%"
    $paragraph_query->fields('nfcb', ['entity_id','field_content_block_target_id']);
    $paragraph_query->groupBy('entity_id');
    // $paragraph_query->condition('where nfcb.bundle like "%health_care_region_detail_page%"');
    // $paragraph_query->condition('field_wysiwyg_value REGEXP "(\<a .*\>)?(1[\-\.])?800[\-\.]273[\-\.]8255(\<\/a\>)?"');

    $paragraph_query->condition('wysiwyg.field_wysiwyg_value', $pattern_string, 'REGEXP');



    // $paragraph_query->condition('patient_resources.field_operating_status_emerg_inf_value', "\<a.*\>800[\-\.]273[\-\.]8255\<\/a\>", 'REGEXP');
    $nids_to_update = $paragraph_query->execute()->fetchAllKeyed();
    $result_count = count($nids_to_update);
    print($result_count);
    $sandbox['total'] = $result_count;
    $sandbox['current'] = 0;
    // Create non-numeric keys to accurately remove each nid when processed.
    $sandbox['nids_to_update'] = $nids_to_update;
  }


  //   // Do not continue if no nodes are found.
  if (empty($sandbox['total'])) {
    $sandbox['#finished'] = 1;
    return "No nodes found for processing.\n";
  }

  $limit = 25;

  //   // Load entities.
  $node_ids = array_keys($sandbox['nids_to_update']);


  $nodes = $node_storage->loadMultiple($node_ids);
  foreach ($nodes as $node) {
    $nid = $node->nid->getValue();
    $target_id = $sandbox['nids_to_update'][$nid[0]['value']];
      // Make this change a new revision.
      /** @var \Drupal\node\NodeInterface $node */
      $node->setNewRevision(TRUE);

      // Wipe out comments that have timezones.
      /*
      $field_value = $node->get($field_name);
    $field_items = $field_value->getValue();
    $referenced_entities = $field_value->referencedEntities();
      */
      $field_value = $node->get('field_content_block');
      $field_items = $field_value->getValue();
      $referenced_entities = $field_value->referencedEntities();
      $database=\Drupal::database();
      // foreach ($field_items as $key => $field_item) {
        // if ($referenced_entities->getValue(id) === $field_item['target_id']) {           // &&  $field_item['target_id'] === $target_id
        //     print("yes");
        //   }

      //  }
      foreach ($referenced_entities as $ref) {
        $refid = $ref->id->getValue()[0]['value'];
        // print_r($ref->id->getValue()[0]['value']);
        if ($ref->id->getValue()[0]['value'] == $target_id) {
          print($target_id);
          $old_value = $ref->field_wysiwyg->value;
          $new_value = preg_replace($pattern_regex, $replacement_string, $old_value, 1);
          $para_to_update = $referenced_entities['target_id'] =

      // print_r($old_value);
      // print_r($new_value);
      $ref->field_wysiwyg->setValue(
        [
          'value' => $new_value,
          'format' => 'rich_text',
        ]

        );
      }



    }

      //     // Set revision author to uid 1317 (CMS Migrator user).
      $node->setRevisionUserId(1317);
      $node->setChangedTime(time());
      $node->setRevisionCreationTime(time());
      $node->setRevisionLogMessage('Updated Crisis number from 800-237-8255 to 988');
      $node->setSyncing(TRUE);
      $node->save();

      unset($sandbox['nids_to_update']["{$node->id()}"]);
      $nids[] = $node->id();
      $sandbox['current'] = $sandbox['total'] - count($sandbox['nids_to_update']);

  }

  //   // Log the processed nodes.
  Drupal::logger('va_gov_db')
    ->log(LogLevel::INFO, 'Nodes: %current nodes phone numbers updated. Nodes processed: %nids', [
      '%current' => $sandbox['current'],
      '%nids' => implode(', ', $nids),
    ]);

  $sandbox['#finished'] = ($sandbox['current'] / $sandbox['total']);

  //   // Log the all-finished notice.
  if ($sandbox['#finished'] == 1) {
    Drupal::logger('va_gov_db')->log(LogLevel::INFO, 'RE-saving all %count nodes completed by change-crisis-to-988', [
      '%count' => $sandbox['total'],
    ]);
    return "Node updates complete. {$sandbox['current']} / {$sandbox['total']}\n";
  }

  return "Processed nodes... {$sandbox['current']} / {$sandbox['total']}.\n";
}

/**
 * Callback function to concat node ids with string.
 *
 * @param int $nid
 *   The node id.
 *
 * @return string
 *   The node id concatenated to the end of node_
 */
function _va_gov_stringifynid($nid)
{
  return "node_$nid";
}
