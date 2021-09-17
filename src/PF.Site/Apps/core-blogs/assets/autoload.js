var core_blogs_onchangeDeleteCategoryType = function (type) {
    if (type == 3)
        $('#category_select').show();
    else
        $('#category_select').hide();
};

var core_blogs_get_content = function (id) {
    var $editor = Editor.setId(id);
    Editor.getEditors();
    return $editor.getContent();
};