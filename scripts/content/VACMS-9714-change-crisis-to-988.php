<?php

/**
 * @file
 * Replace 800-273-8255 with 988 for VAMC System Operating Status
 *
 * VACMS-9714 Script to replace instances of 800-273-8255
 */

use Psr\Log\LogLevel;

$ops_status_total_pattern = "\<a.*\>800[\-\.]273[\-\.]8255\<\/a\>";
$ops_status_pattern = "/\<a.*\>800[\-\.]273[\-\.]8255\<\/a\>/i";
$replacement_string = '<a aria-label="9 8 8" href="tel:988">988</a>';


$sandbox = ['#finished' => 0];
do {
  print(va_gov_change_crisis_hotline_to_988_nodes($sandbox, $ops_status_total_pattern, $ops_status_pattern, $replacement_string));
} while ($sandbox['#finished'] < 1);
// Node processing complete.  Call this done.
return;

/**
 * Replace 800-273-8255 with 988
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

  // Get the node count for nodes with 800-273-8255 in Patient Resources (field_operating_status_emerg_inf)
  // This runs only once.
  if (!isset($sandbox['total'])) {
    $paragraph_query = Drupal::database()->select('node__field_operating_status_emerg_inf', 'patient_resources');
    // $paragraph_query->join('node__field_facility_operating_status', 'nffos', 'patient_resources.entity_id = nffos.entity_id');
    $paragraph_query->fields('patient_resources', ['entity_id']);
    $paragraph_query->groupBy('entity_id');
    $paragraph_query->condition('patient_resources.field_operating_status_emerg_inf_value', $pattern_string, 'REGEXP');
    $nids_to_update = $paragraph_query->execute()->fetchCol();
    $result_count = count($nids_to_update);
    $sandbox['total'] = $result_count;
    $sandbox['current'] = 0;

    // Create non-numeric keys to accurately remove each nid when processed.
    $sandbox['nids_to_update'] = array_combine(
      array_map('_va_gov_stringifynid', array_values($nids_to_update)),
      array_values($nids_to_update)
    );
  }


  //   // Do not continue if no nodes are found.
  if (empty($sandbox['total'])) {
    $sandbox['#finished'] = 1;
    return "No nodes found for processing.\n";
  }

  $limit = 25;

  //   // Load entities.
  $node_ids = array_slice($sandbox['nids_to_update'], 0, $limit, TRUE);
  $nodes = $node_storage->loadMultiple($node_ids);
  foreach ($nodes as $node) {
      // Make this change a new revision.
      /** @var \Drupal\node\NodeInterface $node */
      $node->setNewRevision(TRUE);

      // Wipe out comments that have timezones.
      $old_value = $node->get('field_operating_status_emerg_inf')->value;
      $new_value = preg_replace($pattern_regex, $replacement_string, $old_value);
      $node->field_operating_status_emerg_inf->setValue(
        [
          'value' => $new_value,
          'format' => 'rich_text',
        ]
      );

      //     // Set revision author to uid 1317 (CMS Migrator user).
      $node->setRevisionUserId(1317);
      $node->setChangedTime(time());
      $node->setRevisionCreationTime(time());
      $node->setRevisionLogMessage('Updated Crisis number from 800-237-8255 to 988');
      $node->setSyncing(TRUE);
      $node->save();
      unset($sandbox['nids_to_update']["node_{$node->id()}"]);
      $nids[] = $node->id();
      $sandbox['current'] = $sandbox['total'] - count($sandbox['nids_to_update']);

  }

  // Log the processed nodes.
  Drupal::logger('va_gov_db')
    ->log(LogLevel::INFO, 'Nodes: %current nodes phone numbers updated. Nodes processed: %nids', [
      '%current' => $sandbox['current'],
      '%nids' => implode(', ', $nids),
    ]);

  $sandbox['#finished'] = ($sandbox['current'] / $sandbox['total']);

  //  Log the all-finished notice.
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
