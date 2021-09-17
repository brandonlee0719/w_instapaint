# Marketplace  :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Use cron to send expired notifications.
- Support attachments for listing's description.
- Support emoji for listing's description.
- Users can select actions of listings on listing page same as on detail page.
- Count items on menu My Listings.
- Support drag/drop, preview, progress bar when users upload photos.
- Support AddThis on listing detail page.
- Support 3 styles for pagination.
- Allow admin can change default photos.
- Validate all settings, user group settings, and block settings.
- Update layout for all blocks and pages.

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | marketplace_view_time_stamp | Marketplace View Time Stamp | Don't use anymore |
| 2 | total_listing_more_from | Total "More From" Listings to Display | Don't use anymore |
| 3 | how_many_sponsored_listings | How Many Sponsored Listings To Show | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | marketplace_paging_mode | Pagination Style | Select Pagination Style at Search Page. |
| 4 | marketplace_meta_description | Marketplace Meta Description | Meta description added to pages related to the Marketplace app. |
| 5 | marketplace_meta_keywords | Marketplace Meta Keywords | Meta keywords that will be displayed on sections related to the Marketplace app. |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ----- | ---- |
| 1 | Apps\Core_Marketplace\Ajax | categoryOrdering | 4.7.0 | Don't use anymore |


