Core_drag = {
  init: function(aParams) {
    $(document).ready(function() {
      $(aParams['table']).tableDnD(
          {
            dragHandle: 'drag_handle',
            onDragClass: 'drag_my_class',
            onDrop: function(oTable, oRow) {
              iCnt = 0;
              sParams = '';
              $('.drag_handle input').each(function() {
                iCnt++;

                sParams += '&' + $(this).attr('name') + '=' + iCnt;
              });

              $('.drag_handle_input').each(function() {
                sParams += '&' + $(this).attr('name') + '=' +
                    $(this).attr('value');
              });

              if (aParams['ajax'].substr(0, 7) == 'http://' ||
                  aParams['ajax'].substr(0, 8) == 'https://') {
                $Core.processing();
                $.ajax({
                  url: aParams['ajax'],
                  type: 'POST',
                  data: sParams,
                  success: function(e) {
                    $('.ajax_processing').remove();
                    if (typeof e == 'object' && typeof e.run == 'string') {
                      eval(e.run);
                    }
                  },
                });
              }
              else {
                $Core.ajaxMessage();
                $.ajaxCall(aParams['ajax'], sParams +
                    '&global_ajax_message=true');
              }
            },
          });

      $(aParams['table'] + ' tr.checkRow').hover(
          function() {
            $(this.cells[0]).addClass('drag_show_handle');
          },
          function() {
            $(this.cells[0]).removeClass('drag_show_handle');
          }
      );
    });
  },
};