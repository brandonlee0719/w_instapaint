# Core Groups :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 9th, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Support groups admin can re order widgets.
- Support upload thumbnail photo for main categories.
- Add statistic information of Groups (total items, pending items...) into Sit Statistics.
- Users can select actions of groups on listing page same as on detail page.
- Count items on menu My Groups.
- Support drag/drop, preview, progress bar when users upload thumbnail photos for groups.
- Hide all buttons/links if users don't have permission to do.
- Support 3 styles for pagination.
- Allow admin can change default photos.
- Validate all settings, user group settings, and block settings.
- Update new layout for all blocks and pages.

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | groups.pf_group_show_admins | Show Group Admins | Move to setting of block Admin |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | groups.groups_limit_per_category | Groups Limit Per Category | Define the limit of how many groups per category can be displayed when viewing All Groups page. |
| 2 | groups.pagination_at_search_groups | Paging Style |  |
| 3 | groups.display_groups_profile_photo_within_gallery | Display groups profile photo within gallery | Disable this feature if you do not want to display groups profile photos within the photo gallery. |
| 5 | groups.display_groups_cover_photo_within_gallery | Display groups cover photo within gallery | Disable this feature if you do not want to display groups cover photos within the photo gallery. |
| 6 | groups_meta_description | Groups Meta Description | Meta description added to groups related to the Groups app. |
| 7 | groups_meta_keywords | Groups Meta Keywords | Meta keywords that will be displayed on sections related to the Groups app. |

### Removed User Group Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | groups.pf_group_moderate | Can moderate groups? This will allow a user to edit/delete/approve groups added by other users. | Don't use anymore, split to 3 new user group settings "Can edit all groups?", "Can delete all groups?", "Can approve groups?" |

### New User Group Settings

| ID | Var name | Information |
| --- | -------- | ---- |
| 1 | groups.can_edit_all_groups | Can edit all groups? |
| 2 | groups.can_delete_all_groups | Can delete all groups? |
| 3 | groups.can_approve_groups | Can approve groups? |
| 4 | groups.flood_control| Define how many minutes this user group should wait before they can add new group. Note: Setting it to "0" (without quotes) is default and users will not have to wait. |