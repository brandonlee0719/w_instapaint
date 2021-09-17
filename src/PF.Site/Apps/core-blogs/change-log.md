# Blog :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs

* Setting cache time of sponsored and featured blocks don't work.
* Register users cannot sponsor blogs in case the price is 0.
* Sub-categories show wrong links in My Blogs page.
* Admincp - Manage Categories: Show `No categories found` after deleting a sub-category.
* The button `Sponsor your items` still be shown while users don't have permission to sponsor.
* Pending Blogs page: Does not show message after approving blogs by mass action.
* Duplicate blogs when click on publish button many times.

### Improvements:

* Improve layout of pages and blocks.
* Support both of topic and hashtag.
* Support drag/drop, preview, progress bar when users upload photo.
* Validate all settings, user group settings, and block settings.

### Changed files:

- Ajax/Ajax.php
- Block/AddCategoryList.php
- Block/BlogNew.php
- Block/Categories.php
- Block/Featured.php
- Block/Feed.php
- Block/PopularTopic.php
- Block/Preview.php
- Block/Related.php
- Block/Sponsored.php
- Block/TopBloggers.php
- Controller/AddController.php
- Controller/Admin/AddCategoryController.php
- Controller/Admin/CategoryController.php
- Controller/Admin/DeleteCategoryController.php
- Controller/DeleteController.php
- Controller/IndexController.php
- Controller/ProfileController.php
- Controller/ViewController.php
- Install.php
- Installation/Database/Blog.php
- Installation/Database/Blog_Category.php
- Installation/Database/Blog_Category_Data.php
- Installation/Database/Blog_Text.php
- Installation/Version/v453.php
- Service/Api.php
- Service/Blog.php
- Service/Browse.php
- Service/Cache/Remove.php
- Service/Callback.php
- Service/Category/Category.php
- Service/Category/Process.php
- Service/Permission.php
- Service/Process.php
- assets/autoload.js
- assets/main.less
- hooks/admincp.service_maintain_delete_files_get_list.php
- hooks/bundle__start.php
- hooks/route_start.php
- hooks/template_template_getmenu_3.php
- hooks/validator.admincp_user_settings_blog.php
- phrase.json
- start.php
- views/block/add-category-list.html.php
- views/block/categories.html.php
- views/block/entry.html.php
- views/block/entry_block.html.php
- views/block/featured.html.php
- views/block/feed.html.php
- views/block/link.html.php
- views/block/new.html.php
- views/block/preview.html.php
- views/block/related.html.php
- views/block/specialmenu.html.php
- views/block/sponsored.html.php
- views/block/top.html.php
- views/controller/add.html.php
- views/controller/admincp/add.html.php
- views/controller/admincp/category.html.php
- views/controller/admincp/delete-category.html.php
- views/controller/delete.html.php
- views/controller/edit.html.php
- views/controller/index.html.php
- views/controller/profile.html.php
- views/controller/view.html.php


### Removed Blocks

| ID | Block | Name | Reason |
| --- | -------- | ----- | ---- |
| 1 | blog.topic | Popular Topic | Don't use anymore. Use the block `tag.cloud` instead |

## Version 4.5.3

### Information

- **Release Date:** September 15, 2017
- **Best Compatibility:** phpFox >= 4.5.3

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | blog_time_stamp | Time Stamps | Don't use anymore. Use the global setting `Global Time Stamp` |
| 2 | top_bloggers_display_limit | Top Bloggers Limit | Move to block setting |
| 3 | top_bloggers_min_post | Blog Count for Top Bloggers | Move to block setting |
| 4 | cache_top_bloggers | Cache Top Bloggers | Move to block setting |
| 5 | cache_top_bloggers_limit | Top Bloggers Cache Time | Move to block setting |
| 6 | display_post_count_in_top_bloggers | Display Post Count for Top Bloggers | Move to block setting |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | blog_paging_mode | Pagination Style | Pagination Style at Search Page (3 styles) |
| 2 | display_blog_created_in_group | Display blogs which created in Group to Blogs app | Enable to display all public blogs created in Group to Blogs app. Disable to hide them |
| 3 | display_blog_created_in_page | Display blogs which created in Page to Blogs app | Enable to display all public blogs created in Page to Blogs app. Disable to hide them |

### New User Group Settings

| ID | Var name | Info |
| --- | -------- | ---- |
| 1 | can_feature_blog | Can feature blogs |
| 2 | can_sponsor_blog | Can members of this user group mark a blog as Sponsor without paying fee |
| 3 | can_purchase_sponsor | Can members of this user group purchase a sponsored ad space |
| 4 | blog_sponsor_price | How much is the sponsor space worth? This works in a CPM basis |
| 5 | auto_publish_sponsored_item | After the user has purchased a sponsored space, should the item be published right away? If set to false, the admin will have to approve each new purchased sponsored event space before it is shown in the site |
| 6 | blog_photo_max_upload_size | Photo max upload size |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ---- | ---- |
| 1 | Apps\Core_Blogs\Service\Category\Category | getCategoriesById | 4.6.0 | Don't use anymore |
| 2 | Apps\Core_Blogs\Service\Category\Category | getBlogsByCategory | 4.6.0 | Don't use anymore |
| 3 | Apps\Core_Blogs\Service\Category\Category | getSearch | 4.6.0 | Don't use anymore |
| 4 | Apps\Core_Blogs\Service\Category\Category | get | 4.6.0 | Don't use anymore |
| 5 | Apps\Core_Blogs\Service\Category\Process | deleteMultiple | 4.6.0 | Don't use anymore |
| 6 | Apps\Core_Blogs\Service\Category\Process | toggleCategory | 4.6.0 | Don't use anymore |
| 7 | Apps\Core_Blogs\Service\Blog | getInfoForAction | 4.6.0 | Don't use anymore |
| 8 | Apps\Core_Blogs\Service\Blog | filterText | 4.6.0 | Don't use anymore |
| 9 | Apps\Core_Blogs\Service\Blog | filterText | 4.6.0 | Don't use anymore |

### New Blocks

| ID | Block | Name | Description |
| --- | -------- | ----- | ---- |
| 1 | blog.featured | Featured | Display featured blogs list |
| 2 | blog.sponsored | Sponsored | Display sponsored blogs list |
| 3 | blog.topic | PopularTopic | Display most used topics list |
| 4 | blog.related | Related | Display blogs list which have same category with current viewing blog |

