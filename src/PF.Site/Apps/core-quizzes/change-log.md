# Quizzes :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### New Features

- Notify to quizzes owner when the quiz has new answer.
- Support sponsor quizzes.
- Support feature quizzes.
- New layout for all app pages and blocks.
- Users can select actions of items on listing page same as on detail page.
- Support drag/drop, preview, progress bar when users upload photos.
- Support AddThis on quiz detail page.
- Support 3 styles for pagination.
- Validate all settings, user group settings, and block settings.

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | quizzes_to_show | Quizzes to show | Don't use anymore |
| 2 | quiz_view_time_stamp | Quiz Time Stamp | Don't use anymore ||
| 3 | takers_to_show | Recent Takers To Show | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | quiz_paging_mode | Pagination Style | Select Pagination Style for Quizzes Listing Page. |

### Removed User Group Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | can_edit_own_title | This setting tells if members of this user group can edit the title, description and privacy settings in quizzes they posted. | Use setting "Can edit their own quizzes?" |
| 2 | can_edit_others_title | This setting tells if members of this user group can edit the title, description and privacy settings in quizzes posted by other members. | Use setting "Can edit all quizzes?" |

### New User Group Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | quiz_max_upload_size | Max file size for quiz photos upload | Max file size for quiz photos upload in kilobits (kb). For unlimited add "0" without quotes. |
| 2 | can_feature_quiz | Can feature quizzes? |  |
| 3 | can_sponsor_quiz | Can members of this user group mark a quiz as Sponsor without paying fee? | |
| 4 | can_purchase_sponsor_quiz | Can members of this user group purchase a sponsored ad space for their quizzes? | |
| 5 | quiz_sponsor_price | How much is the sponsor space worth for quizzes? This works in a CPM basis. | |
| 6 | auto_publish_sponsored_item | After the user has purchased a sponsored space, should the item be published right away? | If set to false, the admin will have to approve each new purchased sponsored event space before it is shown in the site. |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ---- | ---- |
| 1 | Apps\Core_Quizzes\Service\Callback | updateCommentText | 4.6.0 | Don't use anymore |
| 2 | Apps\Core_Quizzes\Service\Quiz | getResults | 4.6.0 | Don't use anymore |

### New Blocks

| ID | Block | Name | Description |
| --- | -------- | ---- | ------------ |
| 1 | quiz.featured | Featured | Show featured quizzes. |
| 2 | quiz.sponsored | Sponsored | Show sponsored quizzes. |