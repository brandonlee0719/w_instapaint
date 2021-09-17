# Polls :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### New Features

- Support multiple choices when vote polls.
- Support sponsor polls.
- Support feature polls.
- New layout for all app pages and blocks.
- Users can select actions of items on listing page same as on detail page.
- Support drag/drop, preview, progress bar when users upload photos.
- Support AddThis on poll detail page.
- Support 3 styles for pagination.
- Validate all settings, user group settings, and block settings.


### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | poll_view_time_stamp | Poll Time Stamp | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | poll_paging_mode | Pagination Style | Select Pagination Style for Polls Listing Page. |

### Removed User Group Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | can_edit_title | Can members of this user group edit the title, image, random setting, privacy setting and comment setting on a poll? | Don't use anymore |
| 2 | can_edit_question | Can members of this user group edit the question and answers of a poll?  | Don't use anymore |
| 3 | can_view_hidden_poll_votes | Can view votes even if the poll is marked to hide votes? (Admin Option) | Don't use anymore |

### New User Group Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | can_feature_poll | Can feature polls? |  |
| 2 | can_sponsor_poll | Can members of this user group mark a poll as Sponsor without paying fee? | |
| 3 | can_purchase_sponsor_poll | Can members of this user group purchase a sponsored ad space for their polls? | |
| 4 | poll_sponsor_price | How much is the sponsor space worth for polls? This works in a CPM basis. | |
| 5 | auto_publish_sponsored_item | After the user has purchased a sponsored space, should the item be published right away? | If set to false, the admin will have to approve each new purchased sponsored event space before it is shown in the site. |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ---- | ---- |
| 1 | Apps\Core_Polls\Service\Poll | getPolls | 4.7.0 | Don't use anymore |

### New Blocks

| ID | Block | Name | Description |
| --- | -------- | ---- | ------------ |
| 1 | poll.featured | Featured | Show featured polls. |
| 2 | poll.sponsored | Sponsored | Show sponsored polls. |
| 3 | poll.latest-votes | Latest Votes | Show latest votes of a poll. |