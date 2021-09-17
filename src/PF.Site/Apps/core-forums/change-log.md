# Forum :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Allow admin can view and approve/deny pending posts in thread detail page.
- Support both of topic and hashtag.
- Hide `Reply` button and thread tools in closed threads detail pages.
- Users can select actions of thread/post on listing page same as on thread detail page.
- Support AddThis in thread detail page.
- Support 3 styles for paginations.
- Validate all settings, user group settings, and block settings.

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | total_recent_posts_display | Total Recent Posts Display | Don't use anymore |
| 2 | total_recent_discussions_display | Total Recent Discussions Display | Don't use anymore |
| 3 | forum_user_time_stamp | Forum User Time Stamp | Don't use anymore |
| 4 | can_add_tags_on_threads | Can add tags to threads? | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | forum_paging_mode | Pagination Style | Select Pagination Style at Search Page. |
| 2 | forum_meta_description | Forum Meta Description | Meta description added to pages related to the Forum app. |
| 3 | forum_meta_keywords | Forum Meta Keywords | Meta keywords that will be displayed on sections related to the Forum app. |
| 4 | default_search_type | Default option to search in main forum page | |

### Removed Blocks

| ID | Block | Name |  Reason |
| --- | -------- | ---- | ---- |
| 1 | forum.recent | Recent Threads | Don't use anymore |

### New Blocks

| ID | Block | Name | Description |
| --- | -------- | ---- | ------------ |
| 1 | forum.recent-post | Recent Posts | Show recent posts of forum |
| 2 | forum.recent-thread | Recent Discussions | Show recent threads of forum |
| 2 | forum.sponsored | Sponsored Threads | Show sponsored threads of forum |


