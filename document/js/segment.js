/**
 * Segment
 */

function updateData(datas, name, contentId, text, isDeleted, newText, newIsDeleted) {

  for (i = 0; i < datas.length; i++) {
    data = datas[i];

    if (data[0] == name && data[1] == contentId) {

      data[4] = newText;
      data[5] = newIsDeleted;

      return;
    }
  }

  datas.push([name, contentId, text, isDeleted, newText, newIsDeleted]);
  datas.sort();
}

function submitData(datas) {

  updatedDatas = [];

  for (i = 0; i < datas.length; i++) {

    data = datas[i];

    if ('' == data[1] && '1' == data[5]) {
      console.log('Invalid', i, datas[i]);
      continue;
    }

    if (data[2] == data[4] && data[3] == data[5]) {
      console.log('Same', i, datas[i]);
      continue;
    }

    updatedDatas.push(data);
  }

  return JSON.stringify(updatedDatas);
}

function onClickText(datas, $textSpan) {

  var text = $textSpan.text();
  if (! text) return;

  var name = $textSpan.parent().parent().data("name");
  var contentId = $textSpan.data("content-id");
  var isDeleted = $textSpan.attr("data-is-deleted");
  var isNew = $textSpan.attr("data-is-new");

  var newText = text;
  var newIsDeleted = isDeleted;

  var updateStatus = function () {
    updateData(datas, name, contentId, text, isDeleted, newText, newIsDeleted);
  };

  if (! contentId && '1' == isNew) {
    text = '';
  }

  var inputDiv = '';
  var buttons = '';

  if ('1' == isDeleted) {
    inputDiv = '<p><span class="delete-text-box">' + text + '</span></p>\n';
    buttons = '<button class="undeleteBtn">Un-Delete</button>\n';
  } else {
    inputDiv = '<textarea id="content" class="edit" cols="50" rows="3">' + text + '</textarea>\n';
    buttons = '<button class="updateBtn">Update</button>\n';

    if ('1' != isNew) {
        buttons += '<button class="deleteBtn">Delete</button>\n';
    }
  }

  var $editDiv = $('<div>'
    + inputDiv
    + '<br/>'
    + buttons
    + '<button class="cancelBtn">Cancel</button>'
    + '<button class="resetBtn">Reset</button>'
    + '</div>');

  var updateText = function () {
    newText = $editDiv.children(":first").val();
    if (newText && newText != text) {
      $textSpan.html(newText);
      $textSpan.attr("data-is-new", '0');

      updateStatus();
    }
  };

  var displayText = function () {
    $editDiv.remove();
    $textSpan.show();
  };

  var displayIsDeleted = function (isDeleted) {

      if ('1' == isDeleted) {
          $textSpan.removeClass('text-box');
          $textSpan.toggleClass('delete-text-box');
          $textSpan.attr("data-is-deleted", '1');
      } else {
          $textSpan.removeClass('delete-text-box');
          $textSpan.toggleClass('text-box');
          $textSpan.attr("data-is-deleted", '0');
      }

      newIsDeleted = isDeleted;
  }

  $editDiv.insertBefore($textSpan);
  $textSpan.hide();

  if ('0' == isDeleted) {
      var $input = $editDiv.children(":first");

      $input.click(function () { return false; });
      $input.select();
  }

  var $updateBtn = $editDiv.children(".updateBtn");
  if ($updateBtn) {
      $updateBtn.click(function () {
          updateText();
          displayText();
      });
  }

  var $cancelBtn = $editDiv.children(".cancelBtn");
  if ($cancelBtn) {
      $editDiv.children(".cancelBtn").click(function () {
          displayText();
      });
  }

  var $undeleteBtn = $editDiv.children(".undeleteBtn");
  if ($undeleteBtn) {
      $undeleteBtn.click(function () {
          displayIsDeleted('0');
          displayText();
          updateStatus();
      });
  }

  var $deleteBtn = $editDiv.children(".deleteBtn");
  if ($deleteBtn) {
      $deleteBtn.click(function () {
          displayIsDeleted('1');
          displayText();
          updateStatus();
      });
  }

  var $resetBtn = $editDiv.children(".resetBtn");
  if ($resetBtn) {
    $resetBtn.click(function () {

      if ('' == contentId) return;

      for (i = 0; i < datas.length; i++) {
        data = datas[i];

        if (data[0] == name && data[1] == contentId) {

          newText = data[2];
          newIsDeleted = data[3];

          $textSpan.html(newText);
          displayIsDeleted(newIsDeleted);

          displayText();
          updateStatus();

          return;
        }
      }

    });
  }
}

$(function () {

  var datas = [];

  $('.addBtn').click(function () {
    var $addBtn = $(this);
    var $content = $("<span class='edit-box'><span class='text-box' data-is-deleted='0' data-is-new='1' data-content-id=''>Click to edit it</span></span>");

    $content.insertBefore($addBtn);
    $content.children(":first").click(function () {
      onClickText(datas, $(this));
    });
  });

  $('.edit-box .delete-text-box').click(function () {
    onClickText(datas, $(this));
  });

  $('.edit-box .text-box').click(function () {
    onClickText(datas, $(this));
  });

  $('#submit').click(function () {
    $('#datas').val(submitData(datas));
  });

});

