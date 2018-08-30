const runUrl                   = window.runUrl,
      firstStage               = window.firstStage,
      finishedRedirectUrl      = window.finishedRedirectUrl,
      submissionsStageName     = 'status-submissions';

let migrateSubmissions       = false,
    submissionFormsCount     = 0,
    submissionFormsProcessed = 0;

$(document).ready(function () {

  $('#start-migration-button').click(function (event) {
    setMigrateSubmissions();
    startInProgress();
    run(firstStage, null, null);
  });

  function run(stage, nextForm, nextPage) {

    const stageName = stage['type-name'];
    let stageText = stage['in-progress-text'];

    if (submissionFormsProcessed > 0 &&
      stageName === submissionsStageName &&
      submissionFormsCount > 0 &&
      migrateSubmissions === true
    ) {
      const precent = Math.round((submissionFormsProcessed / submissionFormsCount) * 100);
      stageText += ' ' + precent + '%';
    }

    updateStatusFooter(stageText, true);

    const postData = {
      'stage': stageName,
      'nextForm': nextForm,
      'nextPage': nextPage
    };

    $.ajax({
      'type': 'post',
      'contentType': 'application/x-www-form-urlencoded; charset=UTF-8',
      'cache': false,
      'url': runUrl,
      'dataType': 'json',
      'timeout': 50000000,
      'data': postData
    }).done(function (data) {
      console.log('Successfully ran ' + data['stage']);

      const success      = data['success'];
      const nextStage    = data['nextStage'];
      const currentStage = data['stage'];
      const finished     = data['finished'];

      if (success) {

        if (data['stage']['type-name'] === submissionsStageName && migrateSubmissions) {
          submissionFormsCount = data['submissionsInfo']['formsCount'];
          nextForm         = data['submissionsInfo']['nextForm'];
          let finishedForm     = data['submissionsInfo']['finishedForm'];
          nextPage         = data['submissionsInfo']['nextPage'];

          if (finishedForm === true) {
            submissionFormsProcessed = submissionFormsProcessed + 1;
            console.log('Submission forms processed: ' + submissionFormsProcessed);
          }
        }

        if (finished === false) {

          if (!migrateSubmissions && nextStage['type-name'] === submissionsStageName) {
            addToStatus(currentStage['in-progress-text']);
            finishMigration(data);
          } else if (data['stage']['type-name'] === submissionsStageName) {
            run(data['stage'], nextForm, nextPage);

          } else if (nextStage) {
            addToStatus(currentStage['in-progress-text']);
            run(nextStage, null, null);
          }

        } else {
          finishMigration(data);
        }
      } else {
        updateStatusFooter(currentStage['in-progress-text'], true);
        showErrorMessage();
      }

    }).error(function (jqXHR, textStatus, errorThrown) {
      console.log("Something went wrong: " + jQuery.parseJSON(jqXHR.responseText)['errors']);
    });
  }

  function finishMigration(data) {
    // window.location.replace(finishedRedirectUrl);

    if (data['stage']['type-name'] === submissionsStageName) {
      addToStatus(data['stage']['in-progress-text']);
    }

    removeFooter();
    showSuccessMessage();
  }

  function removeFooter() {
    $('#in-progress-status-footer').hide();
  }

  function startInProgress() {
    $('#ready-wrapper').hide();
    $('#in-progress-wrapper').show();
  }

  function showErrorMessage() {
    $('#in-progress-error-wrapper').show();
  }


  function showSuccessMessage() {
    $('#in-progress-success-wrapper').show();
  }

  function updateStatusFooter(title, success) {
    const footerElement = $('#in-progress-status-footer');
    footerElement.empty();
    footerElement.text(getInProgressStatusFooter(title, success));
  }

  function getInProgressStatusFooter(title, success) {

    if (success) {
      return title + '...';
    }

    return title + '...<span class="in-progress-error">error!</span>';
  }

  function addToStatus(title) {
    const statusWrapperElement = $('#in-progress-status-wrapper');
    statusWrapperElement.append(getInProgressStatus(title));
  }

  function getInProgressStatus(title) {
    return '<div class="in-progress-status">' + title + '...<span class="in-progress-complete">complete!</span></div>'
  }

  function setMigrateSubmissions() {
    migrateSubmissions = $('#migrate-submissions').first().is(":checked");
  }

});
