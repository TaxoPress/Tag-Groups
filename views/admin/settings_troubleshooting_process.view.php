<div class="tg_settings_tabs_content">
  
  <h2><?php echo esc_html($task_set_name) ?></h2>

  <p><?php _e( "Please stay on this page until all processes have finished.", 'tag-groups' ) ?></p>

  <?php echo $task_html ?>

  <div id="tag_groups_tasks_final_words_error" style="display: none;">
    <h3><?php _e( 'There have been errors.', 'tag-groups' ) ?></h3>
    <span>
      <a class="button button-primary" href="<?php echo esc_url(remove_query_arg( 'process-tasks' )) ?>"><?php _e( 'Continue', 'tag-groups' ) ?></a>
    </span>
  </div>

  <div id="tag_groups_tasks_final_words_success" style="display: none;">
    <h3><?php _e( 'Finished!', 'tag-groups' ) ?></h3>
    <span>
      <a class="button button-primary" href="<?php echo esc_url(remove_query_arg( 'process-tasks' )) ?>"><?php _e( 'Continue', 'tag-groups' ) ?></a>
    </span>
  </div>

  <?php if ( ! empty( $bad_terms ) ) : ?>
    <h3><?php _e( 'The following terms cannot be used:', 'tag-groups' ) ?></h3>
    <ul class="tg_list">
      <?php foreach ( $bad_terms as $bad_term ) : ?>
        <li><?php echo $bad_term ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

</div>

<script>
var tagGroupsProcessErrors = false;
var tagGroupsTaskError = false;
var tagGroupsTaskCompleted = false;
var tagGroupsChunkWaiting = false;
var tagGroupsRunningTaskIndex = 0;
var tagGroupsTasks = JSON.parse("<?php echo str_replace( '"', '\"', json_encode( array_values( $tasks ) ) ) ?>");
var tagGroupsTasksTotals = JSON.parse("<?php echo str_replace( '"', '\"', json_encode( $totals ) ) ?>");
var tagGroupsTasksLanguages = JSON.parse("<?php echo str_replace( '"', '\"', json_encode( $languages ) ) ?>");
var tagGroupsTasksLength = tagGroupsTasks.length;
var tagGroupsTaskStartTime = 0;
var tagGroupsTaskTimout = <?php echo $timeout_task ?>;
var tagGroupsChunkStartTime = 0;
var tagGroupsChunkTimout = <?php echo $timeout_chunk ?>;
var tagGroupsProcessChunkOffset = 0;
var tagGroupsProcessChunkLength = <?php echo $chunk_length ?>;
var tagGroupsInterval = 0;
var tagGroupsAffected = 0;

jQuery(document).ready(function(){
  // do the first task
  tagGroupsProcessChunkOffset = 0;
  tagGroupsTaskError = false;
  jQuery("#tag_groups_task_"+tagGroupsTasks[0]).show();

  tagGroupsTaskStartTime = Date.now();
  tagGroupsChunkStartTime = Date.now();

  tagGroupsInterval = setInterval( tagGroupsTasksAjax, 10 );
});

function tagGroupsTasksAjax() {
  var percentage;

  if ( tagGroupsTaskCompleted ) {
    tagGroupsTaskCompleted = false;
    tagGroupsChunkWaiting = false;

    tagGroupsWriteResult();

    if (tagGroupsRunningTaskIndex<tagGroupsTasksLength-1) {
      // switch to next task
      tagGroupsRunningTaskIndex++;
      tagGroupsProcessChunkOffset = 0;
      tagGroupsTaskError = false;
      tagGroupsAffected = 0;
      // show the task
      jQuery("#tag_groups_task_"+tagGroupsTasks[tagGroupsRunningTaskIndex]).show();
      tagGroupsTaskStartTime = Date.now();
      tagGroupsChunkStartTime = Date.now();
    } else {
      // We are done
      window.clearInterval(tagGroupsInterval);
      tagGroupsProcessChunkOffset = tagGroupsTasksTotals[tagGroupsTasks[tagGroupsRunningTaskIndex]];
      tagGroupsWriteResult();
      if (tagGroupsProcessErrors) {
        jQuery("#tag_groups_tasks_final_words_error").show();
      } else {
        jQuery("#tag_groups_tasks_final_words_success").show();
      }
      return;
    }
  }

  // protection against task timeouts
  if (Date.now()-tagGroupsTaskStartTime>tagGroupsTaskTimout) {
    tagGroupsTaskError = true;
    console.log("[Tag Groups] Task timeout: "+tagGroupsTasks[tagGroupsRunningTaskIndex]);
  }

  // protection against chunk timeouts
  if (Date.now()-tagGroupsChunkStartTime>tagGroupsChunkTimout) {
    tagGroupsTaskError = true;
    console.log("[Tag Groups] Chunk timeout: "+tagGroupsTasks[tagGroupsRunningTaskIndex]);
  }

  if (tagGroupsTaskError){
    tagGroupsProcessErrors = true;
    tagGroupsTaskCompleted = true;
    ("[Tag Groups] Error: "+tagGroupsTasks[tagGroupsRunningTaskIndex]);
  }

  if (!tagGroupsChunkWaiting && !tagGroupsTaskError) {
    tagGroupsChunkWaiting = true;
    tagGroupsTaskAjax(tagGroupsTasks[tagGroupsRunningTaskIndex],tagGroupsProcessChunkOffset,tagGroupsProcessChunkLength);
  }

}

function tagGroupsWriteResult() {
  var resultText

  // Add info about the result
  if (tagGroupsTasks[tagGroupsRunningTaskIndex]=='migratepostmeta' || tagGroupsTasks[tagGroupsRunningTaskIndex].substring(0,16)=='rebuildpostcount') {
    resultText = "<?php _e( 'Number of processed items: ', 'tag-groups' ) ?>"+tagGroupsProcessChunkOffset + "/" + tagGroupsTasksTotals[tagGroupsTasks[tagGroupsRunningTaskIndex]];
  } else {
    resultText = "<?php _e( 'Number of changed items: ', 'tag-groups' ) ?>"+tagGroupsAffected + "/" + tagGroupsTasksTotals[tagGroupsTasks[tagGroupsRunningTaskIndex]];
  }
  jQuery("#tag_groups_task_result_"+tagGroupsTasks[tagGroupsRunningTaskIndex]).html(resultText);
}

function tagGroupsTaskAjax(task,offset,length) {

  jQuery.ajax({
    url: "<?php echo $ajax_link ?>",
    dataType: "text",
    data: {
      action: "tg_free_ajax_process",
      task: task,
      offset: offset,
      length: length,
      languagecode: tagGroupsTasksLanguages[tagGroupsTasks[tagGroupsRunningTaskIndex]],
      nonce: "<?php echo wp_create_nonce( 'tag-groups-process-nonce' ) ?>"
    },
    method: "post",
    success: function(rawData) {
      try {
        var data = JSON.parse(rawData.trim());
      } catch (e) {
        console.log(
          '[Tag Groups Pro] Error parsing data from server',
          e.message,
          ', data:"'+rawData.toString()+'"'
        );
        return false;
      }
      var done = data.done;
      var affected = data.affected;

      var total = tagGroupsTasksTotals[tagGroupsTasks[tagGroupsRunningTaskIndex]];

      if ( typeof done === "undefined" || done !== 1 ) {
        tagGroupsTaskError = true;
        console.log("[Tag Groups Pro] Error processing task.", task);
        return;
      }

      if ( typeof affected != "undefined" && affected >= 0 ) {
        tagGroupsAffected += 1*affected;
      }

      tagGroupsProcessChunkOffset+=tagGroupsProcessChunkLength;

      if ( tagGroupsProcessChunkOffset <= total ) {
        tagGroupsChunkStartTime = Date.now();
        percentage = Math.round((tagGroupsProcessChunkOffset/total)*100);
      } else {
        tagGroupsTaskCompleted = true;
        percentage = 100;
      }

      jQuery("#tag_groups_task_bar_"+tagGroupsTasks[tagGroupsRunningTaskIndex]).width(percentage+"%");
      jQuery("#tag_groups_task_progress_"+tagGroupsTasks[tagGroupsRunningTaskIndex]).html(percentage);

      //  unblock at the end
      tagGroupsChunkWaiting = false;
    },
    error: function(xhr, textStatus, errorThrown) {
      console.log("[Tag Groups] Error: " + xhr.responseText);
      tagGroupsTaskError = true;
    }
  });

}
</script>
