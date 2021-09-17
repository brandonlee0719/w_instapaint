# Events  :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Users can select actions of items on listing page same as on detail page.
- Support drag/drop, preview, progress bar when users upload banners.
- Support AddThis on event detail page.
- Support 3 styles for pagination.
- Validate all settings, user group settings, and block settings.
- Admins can control to show/hide events that belonged to pages/groups in events listing page.
- Allow admin can change default banners.

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | cache_events_per_user | Profile Event Count | Don't use anymore |
| 2 | cache_upcoming_events_info | Cache Upcoming Events (Hours) | Don't use anymore |
| 3 | can_view_pirvate_events | Can view private events? | Don't use anymore |
| 4 | event_basic_information_time_short | Event Basic Information Time Stamp (Short) | Don't use anymore |
| 5 | event_view_time_stamp_profile | Event Profile Time Stamp | Don't use anymore |
| 6 | event_browse_time_stamp | Event Browsing Time Stamp | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | event_paging_mode | Pagination Style | Select Pagination Style at Search Page. |
| 2 | event_default_sort_time | Default time to sort events | Select default time time to sort events in listing events page (Except Pending page and Profile page) and some blocks |
| 3 | event_display_event_created_in_group | Display events which created in Group to the All Events page at the Events app | Enable to display all public events to the both Events page in group detail and All Events page in Events app. Disable to display events created by an users to the both Events page in group detail and My Events page of this user in Events app and nobody can see these events in Events app but owner. |
| 3 | event_display_event_created_in_page | Display events which created in Page to the All Events page at the Events app | Enable to display all public events to the both Events page in page detail and All Events page in Events app. Disable to display events created by an users to the both Events page in page detail and My Events page of this user in Events app and nobody can see these events in Events app but owner. |
| 4 | event_meta_description | Events Meta Description | Meta description added to pages related to the Events app. |
| 5 | event_meta_keywords | Events Meta Keywords | Meta keywords that will be displayed on sections related to the Events app. |

### New Blocks

| ID | Block | Name | Description |
| --- | -------- | ---- | ------------ |
| 1 | event.suggestion | Suggestion | Suggest events have same categories with viewing event. |



